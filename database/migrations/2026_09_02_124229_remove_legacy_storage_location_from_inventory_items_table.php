<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $legacyCodes = DB::table('inventory_items')
            ->whereNull('storage_location_id')
            ->whereNotNull('storage_location')
            ->where('storage_location', '!=', '')
            ->distinct()
            ->pluck('storage_location');

        $locations = DB::table('storage_locations')
            ->whereIn('code', $legacyCodes)
            ->pluck('id', 'code');

        $unresolvedCodes = $legacyCodes->reject(fn (string $code): bool => $locations->has($code));

        if ($unresolvedCodes->isNotEmpty()) {
            throw new RuntimeException(
                'Create StorageLocation records for these legacy codes before migrating: '.$unresolvedCodes->implode(', ')
            );
        }

        foreach ($locations as $code => $locationId) {
            DB::table('inventory_items')
                ->whereNull('storage_location_id')
                ->where('storage_location', $code)
                ->update(['storage_location_id' => $locationId]);
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('storage_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('storage_location')->nullable()->after('storage_location_id');
        });

        DB::table('storage_locations')
            ->select(['id', 'code'])
            ->orderBy('id')
            ->each(function (object $location): void {
                DB::table('inventory_items')
                    ->where('storage_location_id', $location->id)
                    ->update(['storage_location' => $location->code]);
            });
    }
};

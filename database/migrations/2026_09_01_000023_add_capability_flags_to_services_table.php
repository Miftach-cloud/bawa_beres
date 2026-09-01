<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('requires_pickup')->default(true)->after('is_active');
            $table->boolean('requires_destination')->default(true)->after('requires_pickup');
            $table->boolean('requires_storage')->default(false)->after('requires_destination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['requires_pickup', 'requires_destination', 'requires_storage']);
        });
    }
};

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
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e.g. MLG01-A-R02-L03
            $table->string('warehouse', 100);     // e.g. MLG01 (Gudang Dinoyo)
            $table->string('zone', 50);          // e.g. Zone A (Furniture)
            $table->string('rack', 30);          // e.g. R02
            $table->string('level', 30);         // e.g. L03
            $table->string('type', 40)->default('STANDARD_RACK');
            $table->string('status', 40)->default('AVAILABLE');
            $table->unsignedInteger('capacity')->default(5);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['warehouse', 'zone']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_locations');
    }
};

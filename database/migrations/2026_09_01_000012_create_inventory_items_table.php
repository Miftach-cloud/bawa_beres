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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_code', 40)->unique();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('condition', 30)->default('GOOD');
            $table->string('status', 30)->default('EXPECTED');
            $table->string('storage_location')->nullable();
            $table->text('qr_code_payload')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('status');
            $table->index('condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};

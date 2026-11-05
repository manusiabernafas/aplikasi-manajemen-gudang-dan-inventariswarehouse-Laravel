<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_unit_id')->nullable()->constrained('product_units')->nullOnDelete();
            $table->decimal('quantity_ordered', 15, 2);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->decimal('quantity_rejected', 15, 2)->default(0);
            $table->enum('qc_status', ['pending', 'pass', 'reject', 'partial'])->default('pending');
            $table->text('qc_notes')->nullable();
            $table->foreignId('putaway_rack_id')->nullable()->constrained('racks')->nullOnDelete();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};

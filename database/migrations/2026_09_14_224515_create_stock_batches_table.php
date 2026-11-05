<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('rack_id')->nullable()->constrained('racks')->nullOnDelete();
            $table->string('batch_number', 40);
            $table->date('expiry_date')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('remaining_qty', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->foreignId('purchase_order_item_id')->nullable()
                ->constrained('purchase_order_items')->nullOnDelete();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id', 'remaining_qty']);
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};

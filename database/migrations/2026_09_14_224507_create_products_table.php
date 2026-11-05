<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('sku', 40)->unique();
            $table->string('barcode', 60)->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('base_unit', 20)->default('pcs');
            $table->enum('costing_method', ['fifo', 'average'])->default('fifo');
            $table->decimal('min_stock', 15, 2)->default(0);
            $table->decimal('max_stock', 15, 2)->nullable();
            $table->decimal('current_stock', 15, 2)->default(0);
            $table->decimal('average_cost', 15, 2)->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

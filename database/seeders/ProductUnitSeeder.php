<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductUnitSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            $product->units()->create([
                'unit_name' => 'pcs',
                'conversion_qty' => 1,
                'is_base' => true,
            ]);

            $product->units()->create([
                'unit_name' => 'box',
                'conversion_qty' => 12,
                'is_base' => false,
            ]);
        });
    }
}

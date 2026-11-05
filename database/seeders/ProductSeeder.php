<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'code');

        $products = [
            ['category' => 'ELK', 'sku' => 'ELK-0001', 'name' => 'Kabel HDMI 2m', 'min' => 50, 'max' => 500, 'cost' => 25000],
            ['category' => 'ELK', 'sku' => 'ELK-0002', 'name' => 'Power Bank 10000mAh', 'min' => 30, 'max' => 300, 'cost' => 95000],
            ['category' => 'ELK', 'sku' => 'ELK-0003', 'name' => 'Lampu LED 12W', 'min' => 100, 'max' => 1000, 'cost' => 18000],
            ['category' => 'ELK', 'sku' => 'ELK-0004', 'name' => 'Stop Kontak 4 Lubang', 'min' => 40, 'max' => 400, 'cost' => 32000],
            ['category' => 'ELK', 'sku' => 'ELK-0005', 'name' => 'Speaker Bluetooth Mini', 'min' => 20, 'max' => 200, 'cost' => 120000],
            ['category' => 'SMB', 'sku' => 'SMB-0001', 'name' => 'Beras Premium 5kg', 'min' => 100, 'max' => 1000, 'cost' => 62000],
            ['category' => 'SMB', 'sku' => 'SMB-0002', 'name' => 'Minyak Goreng 2L', 'min' => 80, 'max' => 800, 'cost' => 34000],
            ['category' => 'SMB', 'sku' => 'SMB-0003', 'name' => 'Gula Pasir 1kg', 'min' => 120, 'max' => 1200, 'cost' => 15500],
            ['category' => 'SMB', 'sku' => 'SMB-0004', 'name' => 'Tepung Terigu 1kg', 'min' => 100, 'max' => 900, 'cost' => 12000],
            ['category' => 'ATK', 'sku' => 'ATK-0001', 'name' => 'Kertas HVS A4 1 Rim', 'min' => 60, 'max' => 600, 'cost' => 48000],
            ['category' => 'ATK', 'sku' => 'ATK-0002', 'name' => 'Pulpen Gel Hitam (Box 12)', 'min' => 40, 'max' => 400, 'cost' => 21000],
            ['category' => 'ATK', 'sku' => 'ATK-0003', 'name' => 'Buku Tulis 38 Lembar', 'min' => 150, 'max' => 1500, 'cost' => 3500],
            ['category' => 'ATK', 'sku' => 'ATK-0004', 'name' => 'Stapler Sedang', 'min' => 25, 'max' => 250, 'cost' => 17000],
            ['category' => 'FUR', 'sku' => 'FUR-0001', 'name' => 'Kursi Kantor Ergonomis', 'min' => 5, 'max' => 60, 'cost' => 650000],
            ['category' => 'FUR', 'sku' => 'FUR-0002', 'name' => 'Meja Lipat Serbaguna', 'min' => 8, 'max' => 80, 'cost' => 320000],
            ['category' => 'FUR', 'sku' => 'FUR-0003', 'name' => 'Rak Buku 4 Susun', 'min' => 6, 'max' => 60, 'cost' => 280000],
            ['category' => 'FSH', 'sku' => 'FSH-0001', 'name' => 'Kaos Polos Cotton Combed', 'min' => 50, 'max' => 500, 'cost' => 38000],
            ['category' => 'FSH', 'sku' => 'FSH-0002', 'name' => 'Jaket Hoodie Fleece', 'min' => 30, 'max' => 300, 'cost' => 95000],
            ['category' => 'FSH', 'sku' => 'FSH-0003', 'name' => 'Celana Chino Slim Fit', 'min' => 25, 'max' => 250, 'cost' => 110000],
            ['category' => 'FSH', 'sku' => 'FSH-0004', 'name' => 'Topi Baseball Cap', 'min' => 40, 'max' => 400, 'cost' => 28000],
        ];

        foreach ($products as $index => $product) {
            $costing = $index % 3 === 0 ? 'average' : 'fifo';

            Product::create([
                'category_id' => $categories[$product['category']] ?? null,
                'sku' => $product['sku'],
                'name' => $product['name'],
                'base_unit' => 'pcs',
                'costing_method' => $costing,
                'min_stock' => $product['min'],
                'max_stock' => $product['max'],
                'current_stock' => 0,
                'average_cost' => 0,
                'is_active' => true,
            ]);
        }
    }
}

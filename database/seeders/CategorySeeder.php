<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'ELK', 'name' => 'Elektronik'],
            ['code' => 'SMB', 'name' => 'Sembako'],
            ['code' => 'ATK', 'name' => 'Alat Tulis Kantor'],
            ['code' => 'FUR', 'name' => 'Furniture'],
            ['code' => 'FSH', 'name' => 'Fashion'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

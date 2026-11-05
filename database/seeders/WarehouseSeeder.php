<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::insert([
            [
                'code' => 'WH-JKT',
                'name' => 'Gudang Pusat Jakarta',
                'address' => 'Jl. Marunda Raya No. 12, Jakarta Utara',
                'phone' => '021-5551234',
                'manager_name' => 'Budi Santoso',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'WH-SBY',
                'name' => 'Gudang Cabang Surabaya',
                'address' => 'Jl. Rungkut Industri No. 8, Surabaya',
                'phone' => '031-5559876',
                'manager_name' => 'Siti Rahma',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

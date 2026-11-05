<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['code' => 'SUP-001', 'name' => 'PT Sumber Elektronik Nusantara', 'contact_person' => 'Andi Wijaya', 'phone' => '0811222333', 'email' => 'andi@sen.co.id'],
            ['code' => 'SUP-002', 'name' => 'PT Sembako Makmur Jaya', 'contact_person' => 'Dewi Lestari', 'phone' => '0812333444', 'email' => 'dewi@smj.co.id'],
            ['code' => 'SUP-003', 'name' => 'CV Alat Tulis Sejahtera', 'contact_person' => 'Rudi Hartono', 'phone' => '0813444555', 'email' => 'rudi@ats.co.id'],
            ['code' => 'SUP-004', 'name' => 'PT Furniture Indo Prima', 'contact_person' => 'Lina Marlina', 'phone' => '0814555666', 'email' => 'lina@fip.co.id'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier + ['is_active' => true]);
        }
    }
}

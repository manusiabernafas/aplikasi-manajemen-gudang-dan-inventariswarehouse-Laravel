<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['code' => 'CUS-001', 'name' => 'Toko Berkat Jaya', 'contact_person' => 'Hendra Gunawan', 'phone' => '0821111222', 'email' => 'hendra@berkatjaya.co.id'],
            ['code' => 'CUS-002', 'name' => 'PT Retail Sejahtera', 'contact_person' => 'Maria Ulfah', 'phone' => '0822222333', 'email' => 'maria@retailsejahtera.co.id'],
            ['code' => 'CUS-003', 'name' => 'CV Mitra Dagang', 'contact_person' => 'Fajar Nugroho', 'phone' => '0823333444', 'email' => 'fajar@mitradagang.co.id'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer + ['is_active' => true]);
        }
    }
}

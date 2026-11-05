<?php

namespace Database\Seeders;

use App\Models\Rack;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class RackSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::all()->each(function (Warehouse $warehouse) {
            foreach (['A', 'B', 'C'] as $zone) {
                for ($aisle = 1; $aisle <= 3; $aisle++) {
                    Rack::create([
                        'warehouse_id' => $warehouse->id,
                        'code' => "{$zone}{$aisle}-01",
                        'zone' => $zone,
                        'aisle' => (string) $aisle,
                        'row' => '01',
                        'level' => '1',
                        'capacity' => 500,
                        'is_active' => true,
                    ]);
                }
            }
        });
    }
}

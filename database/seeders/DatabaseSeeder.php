<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Rack;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WarehouseSeeder::class,
            RackSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            ProductUnitSeeder::class,
        ]);

        $warehouseJkt = Warehouse::where('code', 'WH-JKT')->first();
        $warehouseSby = Warehouse::where('code', 'WH-SBY')->first();

        $admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@wms.test',
            'role' => User::ROLE_ADMIN,
            'warehouse_id' => null,
        ]);

        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'manager@wms.test',
            'role' => User::ROLE_WAREHOUSE_MANAGER,
            'warehouse_id' => $warehouseJkt->id,
        ]);

        User::factory()->create([
            'name' => 'Citra Dewi',
            'email' => 'purchasing@wms.test',
            'role' => User::ROLE_PURCHASING,
            'warehouse_id' => $warehouseJkt->id,
        ]);

        User::factory()->create([
            'name' => 'Dimas Prakoso',
            'email' => 'picker@wms.test',
            'role' => User::ROLE_PICKER,
            'warehouse_id' => $warehouseJkt->id,
        ]);

        $inventoryService = app(InventoryService::class);
        $racks = Rack::where('warehouse_id', $warehouseJkt->id)->get();

        Product::all()->each(function (Product $product, int $index) use ($inventoryService, $warehouseJkt, $warehouseSby, $racks, $admin) {
            $rack = $racks[$index % $racks->count()];

            // Deliberately understock every 4th product so the low-stock KPI has real data.
            $quantity = ($index % 4 === 0)
                ? max(1, (int) ($product->min_stock * 0.4))
                : (int) ($product->min_stock * random_int(2, 5));

            $inventoryService->receiveStock(
                product: $product,
                warehouse: $warehouseJkt,
                quantity: $quantity,
                unitCost: (float) $product->average_cost ?: random_int(5000, 100000),
                rack: $rack,
                batchNumber: 'BTC-INIT-'.$product->sku,
                expiryDate: $index % 5 === 0 ? now()->addDays(20)->toDateString() : null,
                userId: $admin->id,
                notes: 'Stok awal (seed)'
            );

            if ($index % 3 === 0) {
                $inventoryService->receiveStock(
                    product: $product,
                    warehouse: $warehouseSby,
                    quantity: (int) ($product->min_stock * 1.5),
                    unitCost: (float) $product->average_cost,
                    userId: $admin->id,
                    notes: 'Stok awal cabang (seed)'
                );
            }

            // Simulate a bit of outbound history so the dashboard's recent
            // movements table has more than one row per product.
            if ($index % 2 === 0 && $quantity > 5) {
                $inventoryService->issueStock(
                    product: $product->refresh(),
                    warehouse: $warehouseJkt,
                    quantity: min(5, $quantity - 1),
                    userId: $admin->id,
                    notes: 'Pengeluaran demo (seed)'
                );
            }
        });
    }
}

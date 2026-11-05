<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function __construct(private InventoryService $inventoryService) {}

    /**
     * Create a draft opname for a warehouse without touching stock yet.
     */
    public function create(Warehouse $warehouse, string $scheduledDate, int $userId, ?string $notes = null): StockOpname
    {
        return StockOpname::create([
            'opname_number' => StockOpname::generateNumber(),
            'warehouse_id' => $warehouse->id,
            'status' => 'draft',
            'scheduled_date' => $scheduledDate,
            'notes' => $notes,
            'created_by' => $userId,
        ]);
    }

    /**
     * Freeze the opname: snapshot every product's current system quantity
     * for this warehouse into stock_opname_items. From this point the
     * counted "system_qty" is locked, so ongoing warehouse activity during
     * the physical count can't silently change the baseline being compared
     * against.
     */
    public function freeze(StockOpname $opname): StockOpname
    {
        if ($opname->status !== 'draft') {
            throw new \RuntimeException('Hanya opname berstatus draft yang dapat dibekukan.');
        }

        return DB::transaction(function () use ($opname) {
            $products = Product::where('is_active', true)->get();

            foreach ($products as $product) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $product->id,
                    'system_qty' => $product->current_stock,
                    'unit_cost' => $product->average_cost,
                ]);
            }

            $opname->update([
                'status' => 'frozen',
                'frozen_at' => now(),
            ]);

            $this->logAudit('opname_frozen', $opname, $opname->created_by, [
                'items_count' => $products->count(),
            ]);

            return $opname->refresh();
        });
    }

    /**
     * Record the physical count for a single line item and compute variance.
     * Called repeatedly as pickers walk the warehouse with their tally sheets.
     */
    public function recordCount(StockOpnameItem $item, float $physicalQty, ?string $notes = null): StockOpnameItem
    {
        $item->update([
            'physical_qty' => $physicalQty,
            'variance_qty' => $physicalQty - (float) $item->system_qty,
            'notes' => $notes,
        ]);

        return $item;
    }

    /**
     * Close the opname: every line with a non-zero variance is reconciled
     * into the ledger via InventoryService::adjustStock (adjustment_in for
     * surplus, adjustment_out for shortage), then the opname is marked
     * completed. Wrapped in one transaction so a partial reconciliation
     * never leaves stock and opname status out of sync.
     */
    public function complete(StockOpname $opname, int $userId): StockOpname
    {
        if ($opname->status !== 'frozen') {
            throw new \RuntimeException('Hanya opname berstatus frozen yang dapat diselesaikan.');
        }

        return DB::transaction(function () use ($opname, $userId) {
            $warehouse = $opname->warehouse;

            $items = $opname->items()->whereNotNull('physical_qty')->get();

            foreach ($items as $item) {
                $variance = (float) $item->variance_qty;

                if ($variance !== 0.0) {
                    $this->inventoryService->adjustStock(
                        product: $item->product,
                        warehouse: $warehouse,
                        deltaQuantity: $variance,
                        reason: "Penyesuaian stock opname {$opname->opname_number}",
                        reference: $opname,
                        userId: $userId,
                    );
                }
            }

            $opname->update([
                'status' => 'completed',
                'completed_at' => now(),
                'approved_by' => $userId,
            ]);

            $this->logAudit('opname_completed', $opname, $userId, [
                'variance_items' => $items->where('variance_qty', '!=', 0)->count(),
            ]);

            return $opname->refresh();
        });
    }

    private function logAudit(string $action, StockOpname $opname, ?int $userId, array $newValues): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $opname->getMorphClass(),
            'model_id' => $opname->id,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Rack;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Receive stock into a warehouse: opens a new batch, writes an 'in' ledger
     * entry, and re-weights the product's moving-average cost. Always wrapped
     * in a transaction so the batch, movement, and product aggregate never
     * drift apart if one write fails.
     */
    public function receiveStock(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        float $unitCost,
        ?Rack $rack = null,
        ?string $batchNumber = null,
        ?string $expiryDate = null,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $notes = null,
    ): StockBatch {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Jumlah penerimaan harus lebih dari nol.');
        }

        return DB::transaction(function () use (
            $product, $warehouse, $quantity, $unitCost, $rack, $batchNumber,
            $expiryDate, $reference, $userId, $notes
        ) {
            $product = Product::whereKey($product->id)->lockForUpdate()->first();

            $batch = StockBatch::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'rack_id' => $rack?->id,
                'batch_number' => $batchNumber ?: 'BTC-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'expiry_date' => $expiryDate,
                'quantity' => $quantity,
                'remaining_qty' => $quantity,
                'unit_cost' => $unitCost,
                'received_at' => now(),
            ]);

            $newStock = (float) $product->current_stock + $quantity;
            $newAverageCost = $newStock > 0
                ? (((float) $product->current_stock * (float) $product->average_cost) + ($quantity * $unitCost)) / $newStock
                : $unitCost;

            $product->update([
                'current_stock' => $newStock,
                'average_cost' => round($newAverageCost, 2),
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'rack_id' => $rack?->id,
                'stock_batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'balance_after' => $newStock,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->id,
                'notes' => $notes,
                'created_by' => $userId,
            ]);

            $this->logAudit('stock_in', $product, $userId, [
                'quantity' => $quantity,
                'warehouse_id' => $warehouse->id,
                'batch' => $batch->batch_number,
            ]);

            return $batch;
        });
    }

    /**
     * Issue stock out of a warehouse. Consumes batches oldest-expiry-first
     * (FIFO) regardless of costing method, since batch/expiry tracking is
     * physical reality — but the cost booked per unit depends on the
     * product's costing_method (FIFO uses each batch's own cost, average
     * uses the product's moving average).
     *
     * @return StockMovement[] one movement row per batch touched
     */
    public function issueStock(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        ?Model $reference = null,
        ?int $userId = null,
        ?Rack $rack = null,
        ?string $notes = null,
    ): array {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Jumlah pengeluaran harus lebih dari nol.');
        }

        return DB::transaction(function () use (
            $product, $warehouse, $quantity, $reference, $userId, $rack, $notes
        ) {
            $product = Product::whereKey($product->id)->lockForUpdate()->first();

            if ((float) $product->current_stock < $quantity) {
                throw new InsufficientStockException($product->name, $quantity, (float) $product->current_stock);
            }

            $batchesQuery = StockBatch::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->where('remaining_qty', '>', 0)
                ->orderByRaw('expiry_date IS NULL, expiry_date asc')
                ->orderBy('received_at')
                ->lockForUpdate();

            if ($rack) {
                $batchesQuery->where('rack_id', $rack->id);
            }

            $batches = $batchesQuery->get();

            $remainingToIssue = $quantity;
            $movements = [];
            $runningBalance = (float) $product->current_stock;
            $costingCost = $product->costing_method === 'average' ? (float) $product->average_cost : null;

            foreach ($batches as $batch) {
                if ($remainingToIssue <= 0) {
                    break;
                }

                $takeFromBatch = min((float) $batch->remaining_qty, $remainingToIssue);
                $batch->decrement('remaining_qty', $takeFromBatch);

                $runningBalance -= $takeFromBatch;

                $movements[] = StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'rack_id' => $batch->rack_id,
                    'stock_batch_id' => $batch->id,
                    'type' => 'out',
                    'quantity' => $takeFromBatch,
                    'unit_cost' => $costingCost ?? $batch->unit_cost,
                    'balance_after' => $runningBalance,
                    'reference_type' => $reference?->getMorphClass(),
                    'reference_id' => $reference?->id,
                    'notes' => $notes,
                    'created_by' => $userId,
                ]);

                $remainingToIssue -= $takeFromBatch;
            }

            if ($remainingToIssue > 0) {
                throw new InsufficientStockException($product->name, $quantity, $quantity - $remainingToIssue);
            }

            $product->update(['current_stock' => $runningBalance]);

            $this->logAudit('stock_out', $product, $userId, [
                'quantity' => $quantity,
                'warehouse_id' => $warehouse->id,
            ]);

            return $movements;
        });
    }

    /**
     * Move stock between warehouses/racks as one atomic unit: an issue from
     * source booked as transfer_out, and a receipt into destination booked
     * as transfer_in, sharing the same StockTransfer reference.
     */
    public function transferStock(
        Product $product,
        Warehouse $fromWarehouse,
        Warehouse $toWarehouse,
        float $quantity,
        ?Rack $fromRack = null,
        ?Rack $toRack = null,
        ?Model $reference = null,
        ?int $userId = null,
    ): void {
        DB::transaction(function () use ($product, $fromWarehouse, $toWarehouse, $quantity, $fromRack, $toRack, $reference, $userId) {
            $outMovements = $this->issueStock($product, $fromWarehouse, $quantity, $reference, $userId, $fromRack, 'Transfer keluar');

            foreach ($outMovements as $movement) {
                $movement->update(['type' => 'transfer_out']);
            }

            $weightedCost = $quantity > 0
                ? array_sum(array_map(fn ($m) => $m->quantity * $m->unit_cost, $outMovements)) / $quantity
                : 0;

            $batch = $this->receiveStock(
                $product,
                $toWarehouse,
                $quantity,
                round($weightedCost, 2),
                $toRack,
                null,
                null,
                $reference,
                $userId,
                'Transfer masuk'
            );

            StockMovement::where('stock_batch_id', $batch->id)
                ->where('type', 'in')
                ->update(['type' => 'transfer_in']);
        });
    }

    /**
     * Manual or opname-driven adjustment. Positive delta books an
     * adjustment_in receipt; negative delta issues an adjustment_out.
     */
    public function adjustStock(
        Product $product,
        Warehouse $warehouse,
        float $deltaQuantity,
        string $reason,
        ?Model $reference = null,
        ?int $userId = null,
        ?Rack $rack = null,
    ): void {
        if ($deltaQuantity === 0.0) {
            return;
        }

        DB::transaction(function () use ($product, $warehouse, $deltaQuantity, $reason, $reference, $userId, $rack) {
            if ($deltaQuantity > 0) {
                $batch = $this->receiveStock(
                    $product, $warehouse, $deltaQuantity, (float) $product->average_cost,
                    $rack, null, null, $reference, $userId, $reason
                );
                StockMovement::where('stock_batch_id', $batch->id)->update(['type' => 'adjustment_in']);
            } else {
                $movements = $this->issueStock($product, $warehouse, abs($deltaQuantity), $reference, $userId, $rack, $reason);
                foreach ($movements as $movement) {
                    $movement->update(['type' => 'adjustment_out']);
                }
            }

            $this->logAudit('stock_adjustment', $product, $userId, [
                'delta' => $deltaQuantity,
                'reason' => $reason,
                'warehouse_id' => $warehouse->id,
            ]);
        });
    }

    private function logAudit(string $action, Model $model, ?int $userId, array $newValues): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->id,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
        ]);
    }
}

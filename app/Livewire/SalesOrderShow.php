<?php

namespace App\Livewire;

use App\Models\PickingList;
use App\Models\SalesOrder;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SalesOrderShow extends Component
{
    public SalesOrder $salesOrder;

    public function mount(SalesOrder $salesOrder): void
    {
        $this->salesOrder = $salesOrder;
    }

    /**
     * Build a picking list for this order: one line per SO item, located at
     * the rack holding its oldest-expiry batch (FIFO), then numbered into a
     * walking sequence ordered by rack code so pickers don't zig-zag the
     * warehouse.
     */
    public function generatePickingList(): void
    {
        DB::transaction(function () {
            $pickingList = PickingList::create([
                'picking_number' => PickingList::generateNumber(),
                'sales_order_id' => $this->salesOrder->id,
                'warehouse_id' => $this->salesOrder->warehouse_id,
                'status' => 'pending',
            ]);

            foreach ($this->salesOrder->items as $soItem) {
                $batch = StockBatch::where('product_id', $soItem->product_id)
                    ->where('warehouse_id', $this->salesOrder->warehouse_id)
                    ->where('remaining_qty', '>', 0)
                    ->orderByRaw('expiry_date IS NULL, expiry_date asc')
                    ->orderBy('received_at')
                    ->first();

                $pickingList->items()->create([
                    'sales_order_item_id' => $soItem->id,
                    'product_id' => $soItem->product_id,
                    'rack_id' => $batch?->rack_id,
                    'stock_batch_id' => $batch?->id,
                    'quantity_to_pick' => $soItem->quantity_ordered,
                ]);
            }

            $ordered = $pickingList->items()->with('rack')->get()
                ->sortBy(fn ($item) => $item->rack->code ?? 'zzz')
                ->values();

            foreach ($ordered as $index => $item) {
                $item->update(['sequence' => $index + 1]);
            }

            $this->salesOrder->update(['status' => 'picking']);
        });

        $latestPickingList = PickingList::where('sales_order_id', $this->salesOrder->id)->latest()->first();
        $this->redirect(route('picking-lists.show', $latestPickingList), navigate: false);
    }

    public function render()
    {
        return view('livewire.sales-order-show')->layout('layouts.app', ['title' => 'Detail Sales Order']);
    }
}

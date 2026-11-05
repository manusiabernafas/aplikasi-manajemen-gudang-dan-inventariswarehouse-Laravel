<?php

namespace App\Livewire;

use App\Models\PickingList;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PickingListShow extends Component
{
    public PickingList $pickingList;

    /** @var array<int, string> */
    public array $quantityPicked = [];

    public string $errorMessage = '';

    public function mount(PickingList $pickingList): void
    {
        $this->pickingList = $pickingList;

        foreach ($pickingList->items as $item) {
            $this->quantityPicked[$item->id] = $item->quantity_picked > 0
                ? (string) $item->quantity_picked
                : (string) $item->quantity_to_pick;
        }
    }

    public function startPicking(): void
    {
        $this->pickingList->update([
            'status' => 'in_progress',
            'assigned_to' => auth()->id(),
            'started_at' => now(),
        ]);
        $this->pickingList->refresh();
    }

    public function completePicking(InventoryService $inventoryService): void
    {
        try {
            DB::transaction(function () use ($inventoryService) {
                foreach ($this->pickingList->items as $item) {
                    $qty = (float) ($this->quantityPicked[$item->id] ?? 0);

                    if ($qty <= 0) {
                        continue;
                    }

                    $item->update(['quantity_picked' => $qty]);

                    $inventoryService->issueStock(
                        product: $item->product,
                        warehouse: $this->pickingList->warehouse,
                        quantity: $qty,
                        reference: $this->pickingList,
                        userId: auth()->id(),
                        rack: $item->rack,
                        notes: "Picking {$this->pickingList->picking_number}",
                    );

                    $item->salesOrderItem->update([
                        'quantity_picked' => $qty,
                        'quantity_packed' => $qty,
                    ]);
                }

                $this->pickingList->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                $this->pickingList->salesOrder->update(['status' => 'packed']);
            });

            $this->pickingList->refresh();
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.picking-list-show')->layout('layouts.app', ['title' => 'Detail Picking List']);
    }
}

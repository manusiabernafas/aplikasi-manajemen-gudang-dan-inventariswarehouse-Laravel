<?php

namespace App\Livewire;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Rack;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PurchaseOrderShow extends Component
{
    public PurchaseOrder $purchaseOrder;

    public ?int $receivingItemId = null;

    public ?float $receiveQty = null;

    public ?float $rejectQty = null;

    public string $rackId = '';

    public string $qcNotes = '';

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    #[Computed]
    public function racks()
    {
        return Rack::where('warehouse_id', $this->purchaseOrder->warehouse_id)->orderBy('code')->get();
    }

    public function openReceiveForm(int $itemId): void
    {
        $item = PurchaseOrderItem::findOrFail($itemId);
        $this->receivingItemId = $itemId;
        $this->receiveQty = $item->remainingToReceive() > 0 ? $item->remainingToReceive() : null;
        $this->rejectQty = 0;
        $this->rackId = (string) ($this->racks->first()->id ?? '');
        $this->qcNotes = '';
    }

    public function cancelReceiveForm(): void
    {
        $this->receivingItemId = null;
    }

    public function processReceiving(InventoryService $inventoryService): void
    {
        $this->validate([
            'receiveQty' => ['nullable', 'numeric', 'min:0'],
            'rejectQty' => ['nullable', 'numeric', 'min:0'],
            'rackId' => ['required_if:receiveQty,>,0'],
        ]);

        $item = PurchaseOrderItem::findOrFail($this->receivingItemId);
        $receiveQty = (float) ($this->receiveQty ?? 0);
        $rejectQty = (float) ($this->rejectQty ?? 0);

        if ($receiveQty <= 0 && $rejectQty <= 0) {
            $this->addError('receiveQty', 'Isi jumlah diterima atau ditolak.');

            return;
        }

        if ($receiveQty + $rejectQty > $item->remainingToReceive() + 0.0001) {
            $this->addError('receiveQty', 'Total melebihi sisa quantity yang belum diproses.');

            return;
        }

        DB::transaction(function () use ($inventoryService, $item, $receiveQty, $rejectQty) {
            if ($receiveQty > 0) {
                $inventoryService->receiveStock(
                    product: $item->product,
                    warehouse: $this->purchaseOrder->warehouse,
                    quantity: $receiveQty,
                    unitCost: (float) $item->unit_price,
                    rack: Rack::find($this->rackId),
                    reference: $this->purchaseOrder,
                    userId: auth()->id(),
                    notes: "Penerimaan PO {$this->purchaseOrder->po_number}",
                );
            }

            $newReceived = (float) $item->quantity_received + $receiveQty;
            $newRejected = (float) $item->quantity_rejected + $rejectQty;

            $qcStatus = match (true) {
                $newRejected > 0 && $newReceived > 0 => 'partial',
                $newRejected > 0 && $newReceived <= 0 => 'reject',
                default => 'pass',
            };

            $item->update([
                'quantity_received' => $newReceived,
                'quantity_rejected' => $newRejected,
                'qc_status' => $qcStatus,
                'qc_notes' => $this->qcNotes ?: $item->qc_notes,
                'putaway_rack_id' => $this->rackId ?: $item->putaway_rack_id,
            ]);

            $this->refreshPoStatus();
        });

        $this->receivingItemId = null;
        $this->purchaseOrder->refresh();
    }

    private function refreshPoStatus(): void
    {
        $items = $this->purchaseOrder->items()->get();

        $allDone = $items->every(fn ($i) => ($i->quantity_received + $i->quantity_rejected) >= $i->quantity_ordered - 0.0001);
        $anyStarted = $items->contains(fn ($i) => ($i->quantity_received + $i->quantity_rejected) > 0);

        $this->purchaseOrder->update([
            'status' => $allDone ? 'received' : ($anyStarted ? 'partially_received' : $this->purchaseOrder->status),
        ]);
    }

    public function render()
    {
        return view('livewire.purchase-order-show')->layout('layouts.app', ['title' => 'Detail Purchase Order']);
    }
}

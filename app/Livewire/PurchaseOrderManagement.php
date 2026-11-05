<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderManagement extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public bool $showModal = false;

    public string $supplierId = '';

    public string $warehouseId = '';

    public string $orderDate = '';

    public string $expectedDate = '';

    public string $notes = '';

    /** @var array<int, array{product_id: string, quantity: float|null, unit_price: float|null}> */
    public array $items = [];

    public function mount(): void
    {
        $this->orderDate = now()->toDateString();
        $this->resetItems();
    }

    #[Computed]
    public function purchaseOrders()
    {
        return PurchaseOrder::with(['supplier', 'warehouse'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function products()
    {
        return Product::where('is_active', true)->orderBy('name')->get();
    }

    private function resetItems(): void
    {
        $this->items = [['product_id' => '', 'quantity' => null, 'unit_price' => null]];
    }

    public function addItemRow(): void
    {
        $this->items[] = ['product_id' => '', 'quantity' => null, 'unit_price' => null];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->supplierId = '';
        $this->warehouseId = '';
        $this->orderDate = now()->toDateString();
        $this->expectedDate = '';
        $this->notes = '';
        $this->resetItems();
        $this->showModal = true;
    }

    public function savePurchaseOrder(): void
    {
        $this->validate([
            'supplierId' => ['required'],
            'warehouseId' => ['required'],
            'orderDate' => ['required', 'date'],
            'expectedDate' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () {
            $total = 0;
            foreach ($this->items as $item) {
                $total += (float) $item['quantity'] * (float) $item['unit_price'];
            }

            $po = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generateNumber(),
                'supplier_id' => $this->supplierId,
                'warehouse_id' => $this->warehouseId,
                'status' => 'draft',
                'order_date' => $this->orderDate,
                'expected_date' => $this->expectedDate ?: null,
                'total_amount' => $total,
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => (float) $item['quantity'] * (float) $item['unit_price'],
                ]);
            }
        });

        $this->showModal = false;
        unset($this->purchaseOrders);
    }

    public function markAsOrdered(int $poId): void
    {
        PurchaseOrder::whereKey($poId)->where('status', 'draft')->update([
            'status' => 'ordered',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        unset($this->purchaseOrders);
    }

    public function render()
    {
        return view('livewire.purchase-order-management')->layout('layouts.app', ['title' => 'Purchase Order']);
    }
}

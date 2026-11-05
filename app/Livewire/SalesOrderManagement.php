<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SalesOrderManagement extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public bool $showModal = false;

    public string $customerId = '';

    public string $warehouseId = '';

    public string $orderDate = '';

    public string $requiredDate = '';

    public string $notes = '';

    /** @var array<int, array{product_id: string, quantity: float|null, unit_price: float|null}> */
    public array $items = [];

    public function mount(): void
    {
        $this->orderDate = now()->toDateString();
        $this->resetItems();
    }

    #[Computed]
    public function salesOrders()
    {
        return SalesOrder::with(['customer', 'warehouse'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function customers()
    {
        return Customer::where('is_active', true)->orderBy('name')->get();
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
        $this->customerId = '';
        $this->warehouseId = '';
        $this->orderDate = now()->toDateString();
        $this->requiredDate = '';
        $this->notes = '';
        $this->resetItems();
        $this->showModal = true;
    }

    public function saveSalesOrder(): void
    {
        $this->validate([
            'customerId' => ['required'],
            'warehouseId' => ['required'],
            'orderDate' => ['required', 'date'],
            'requiredDate' => ['nullable', 'date'],
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

            $so = SalesOrder::create([
                'so_number' => SalesOrder::generateNumber(),
                'customer_id' => $this->customerId,
                'warehouse_id' => $this->warehouseId,
                'status' => 'draft',
                'order_date' => $this->orderDate,
                'required_date' => $this->requiredDate ?: null,
                'total_amount' => $total,
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                $so->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => (float) $item['quantity'] * (float) $item['unit_price'],
                ]);
            }
        });

        $this->showModal = false;
        unset($this->salesOrders);
    }

    public function confirmOrder(int $soId): void
    {
        SalesOrder::whereKey($soId)->where('status', 'draft')->update(['status' => 'confirmed']);
        unset($this->salesOrders);
    }

    public function render()
    {
        return view('livewire.sales-order-management')->layout('layouts.app', ['title' => 'Sales Order']);
    }
}

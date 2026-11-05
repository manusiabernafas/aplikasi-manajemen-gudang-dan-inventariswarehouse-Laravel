<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class StockTransferManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public string $fromWarehouseId = '';

    public string $toWarehouseId = '';

    public string $notes = '';

    /** @var array<int, array{product_id: string, quantity: float|null}> */
    public array $items = [];

    public string $errorMessage = '';

    public function mount(): void
    {
        $this->resetItems();
    }

    #[Computed]
    public function transfers()
    {
        return StockTransfer::with(['fromWarehouse', 'toWarehouse', 'items.product'])
            ->latest()
            ->paginate(10);
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
        $this->items = [['product_id' => '', 'quantity' => null]];
    }

    public function addItemRow(): void
    {
        $this->items[] = ['product_id' => '', 'quantity' => null];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->errorMessage = '';
        $this->fromWarehouseId = (string) ($this->warehouses->first()->id ?? '');
        $this->toWarehouseId = (string) ($this->warehouses->skip(1)->first()->id ?? '');
        $this->notes = '';
        $this->resetItems();
        $this->showModal = true;
    }

    public function submitTransfer(InventoryService $inventoryService): void
    {
        $this->validate([
            'fromWarehouseId' => ['required'],
            'toWarehouseId' => ['required', 'different:fromWarehouseId'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        try {
            DB::transaction(function () use ($inventoryService) {
                $transfer = StockTransfer::create([
                    'transfer_number' => StockTransfer::generateNumber(),
                    'from_warehouse_id' => $this->fromWarehouseId,
                    'to_warehouse_id' => $this->toWarehouseId,
                    'status' => 'draft',
                    'notes' => $this->notes,
                    'requested_by' => auth()->id(),
                ]);

                foreach ($this->items as $item) {
                    $transfer->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                    ]);

                    $inventoryService->transferStock(
                        product: Product::findOrFail($item['product_id']),
                        fromWarehouse: Warehouse::findOrFail($this->fromWarehouseId),
                        toWarehouse: Warehouse::findOrFail($this->toWarehouseId),
                        quantity: (float) $item['quantity'],
                        reference: $transfer,
                        userId: auth()->id(),
                    );
                }

                $transfer->update([
                    'status' => 'completed',
                    'approved_by' => auth()->id(),
                    'transferred_at' => now(),
                ]);
            });

            $this->showModal = false;
            unset($this->transfers);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.stock-transfer-management')->layout('layouts.app', ['title' => 'Stock Transfer']);
    }
}

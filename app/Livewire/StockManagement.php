<?php

namespace App\Livewire;

use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class StockManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public string $warehouseFilter = '';

    public string $categoryFilter = '';

    public string $statusFilter = '';

    // Quick Adjustment modal state
    public bool $showAdjustModal = false;

    public ?int $adjustProductId = null;

    public string $adjustWarehouseId = '';

    public string $adjustDirection = 'in';

    public ?float $adjustQuantity = null;

    public string $adjustReason = '';

    // Transfer modal state
    public bool $showTransferModal = false;

    public ?int $transferProductId = null;

    public string $transferFromWarehouseId = '';

    public string $transferToWarehouseId = '';

    public ?float $transferQuantity = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingWarehouseFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function products()
    {
        $query = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter === 'low', fn ($q) => $q->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0))
            ->when($this->statusFilter === 'out', fn ($q) => $q->where('current_stock', '<=', 0))
            ->when($this->statusFilter === 'healthy', fn ($q) => $q->whereColumn('current_stock', '>', 'min_stock'));

        if ($this->warehouseFilter) {
            $query->addSelect([
                'warehouse_stock' => StockBatch::selectRaw('COALESCE(SUM(remaining_qty), 0)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('warehouse_id', $this->warehouseFilter),
            ]);
        }

        return $query->orderBy('name')->paginate(10);
    }

    public function openAdjustModal(int $productId): void
    {
        $this->resetValidation();
        $this->adjustProductId = $productId;
        $this->adjustWarehouseId = (string) ($this->warehouses->first()->id ?? '');
        $this->adjustDirection = 'in';
        $this->adjustQuantity = null;
        $this->adjustReason = '';
        $this->showAdjustModal = true;
    }

    public function closeAdjustModal(): void
    {
        $this->showAdjustModal = false;
    }

    public function submitAdjust(InventoryService $inventoryService): void
    {
        $this->validate([
            'adjustWarehouseId' => ['required'],
            'adjustQuantity' => ['required', 'numeric', 'gt:0'],
            'adjustReason' => ['required', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($this->adjustProductId);
        $warehouse = Warehouse::findOrFail($this->adjustWarehouseId);

        $delta = $this->adjustDirection === 'in' ? (float) $this->adjustQuantity : -(float) $this->adjustQuantity;

        try {
            $inventoryService->adjustStock(
                product: $product,
                warehouse: $warehouse,
                deltaQuantity: $delta,
                reason: $this->adjustReason,
                userId: auth()->id(),
            );

            $this->showAdjustModal = false;
            unset($this->products);
            $this->dispatch('stock-updated');
        } catch (InsufficientStockException $e) {
            $this->addError('adjustQuantity', $e->getMessage());
        }
    }

    public function openTransferModal(int $productId): void
    {
        $this->resetValidation();
        $this->transferProductId = $productId;
        $this->transferFromWarehouseId = (string) ($this->warehouses->first()->id ?? '');
        $this->transferToWarehouseId = (string) ($this->warehouses->skip(1)->first()->id ?? '');
        $this->transferQuantity = null;
        $this->showTransferModal = true;
    }

    public function closeTransferModal(): void
    {
        $this->showTransferModal = false;
    }

    public function submitTransfer(InventoryService $inventoryService): void
    {
        $this->validate([
            'transferFromWarehouseId' => ['required'],
            'transferToWarehouseId' => ['required', 'different:transferFromWarehouseId'],
            'transferQuantity' => ['required', 'numeric', 'gt:0'],
        ]);

        $product = Product::findOrFail($this->transferProductId);
        $from = Warehouse::findOrFail($this->transferFromWarehouseId);
        $to = Warehouse::findOrFail($this->transferToWarehouseId);

        try {
            $inventoryService->transferStock(
                product: $product,
                fromWarehouse: $from,
                toWarehouse: $to,
                quantity: (float) $this->transferQuantity,
                userId: auth()->id(),
            );

            $this->showTransferModal = false;
            unset($this->products);
            $this->dispatch('stock-updated');
        } catch (InsufficientStockException $e) {
            $this->addError('transferQuantity', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.stock-management')->layout('layouts.app', ['title' => 'Manajemen Stok']);
    }
}

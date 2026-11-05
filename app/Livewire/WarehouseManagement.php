<?php

namespace App\Livewire;

use App\Models\Rack;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WarehouseManagement extends Component
{
    public ?int $selectedWarehouseId = null;

    // Warehouse modal
    public bool $showWarehouseModal = false;

    public ?int $editingWarehouseId = null;

    public string $whCode = '';

    public string $whName = '';

    public string $whAddress = '';

    public string $whPhone = '';

    public string $whManagerName = '';

    // Rack modal
    public bool $showRackModal = false;

    public ?int $editingRackId = null;

    public string $rackCode = '';

    public string $rackZone = '';

    public string $rackAisle = '';

    public string $rackRow = '';

    public string $rackLevel = '';

    public ?int $rackCapacity = null;

    public string $errorMessage = '';

    public function mount(): void
    {
        $this->selectedWarehouseId = Warehouse::query()->orderBy('name')->value('id');
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::withCount('racks')->orderBy('name')->get();
    }

    #[Computed]
    public function racks()
    {
        if (! $this->selectedWarehouseId) {
            return collect();
        }

        return Rack::where('warehouse_id', $this->selectedWarehouseId)->orderBy('code')->get();
    }

    public function selectWarehouse(int $warehouseId): void
    {
        $this->selectedWarehouseId = $warehouseId;
    }

    public function openWarehouseModal(?int $warehouseId = null): void
    {
        $this->resetValidation();
        $this->errorMessage = '';
        $this->editingWarehouseId = $warehouseId;

        if ($warehouseId) {
            $wh = Warehouse::findOrFail($warehouseId);
            $this->whCode = $wh->code;
            $this->whName = $wh->name;
            $this->whAddress = (string) $wh->address;
            $this->whPhone = (string) $wh->phone;
            $this->whManagerName = (string) $wh->manager_name;
        } else {
            $this->whCode = '';
            $this->whName = '';
            $this->whAddress = '';
            $this->whPhone = '';
            $this->whManagerName = '';
        }

        $this->showWarehouseModal = true;
    }

    public function saveWarehouse(): void
    {
        $this->validate([
            'whCode' => ['required', 'string', 'max:20', 'unique:warehouses,code,'.$this->editingWarehouseId],
            'whName' => ['required', 'string', 'max:255'],
            'whAddress' => ['nullable', 'string', 'max:255'],
            'whPhone' => ['nullable', 'string', 'max:30'],
            'whManagerName' => ['nullable', 'string', 'max:255'],
        ]);

        Warehouse::updateOrCreate(['id' => $this->editingWarehouseId], [
            'code' => $this->whCode,
            'name' => $this->whName,
            'address' => $this->whAddress,
            'phone' => $this->whPhone,
            'manager_name' => $this->whManagerName,
            'is_active' => true,
        ]);

        $this->showWarehouseModal = false;
        unset($this->warehouses);
    }

    public function deleteWarehouse(int $warehouseId): void
    {
        try {
            Warehouse::findOrFail($warehouseId)->delete();
            unset($this->warehouses);

            if ($this->selectedWarehouseId === $warehouseId) {
                $this->selectedWarehouseId = Warehouse::query()->orderBy('name')->value('id');
            }
        } catch (QueryException) {
            $this->errorMessage = 'Gudang tidak dapat dihapus karena masih memiliki data terkait (rak/stok/transaksi).';
        }
    }

    public function openRackModal(?int $rackId = null): void
    {
        $this->resetValidation();
        $this->errorMessage = '';
        $this->editingRackId = $rackId;

        if ($rackId) {
            $rack = Rack::findOrFail($rackId);
            $this->rackCode = $rack->code;
            $this->rackZone = (string) $rack->zone;
            $this->rackAisle = (string) $rack->aisle;
            $this->rackRow = (string) $rack->row;
            $this->rackLevel = (string) $rack->level;
            $this->rackCapacity = $rack->capacity;
        } else {
            $this->rackCode = '';
            $this->rackZone = '';
            $this->rackAisle = '';
            $this->rackRow = '';
            $this->rackLevel = '';
            $this->rackCapacity = null;
        }

        $this->showRackModal = true;
    }

    public function saveRack(): void
    {
        $this->validate([
            'rackCode' => ['required', 'string', 'max:30'],
            'rackZone' => ['nullable', 'string', 'max:20'],
            'rackAisle' => ['nullable', 'string', 'max:10'],
            'rackRow' => ['nullable', 'string', 'max:10'],
            'rackLevel' => ['nullable', 'string', 'max:10'],
            'rackCapacity' => ['nullable', 'integer', 'min:0'],
        ]);

        Rack::updateOrCreate(['id' => $this->editingRackId], [
            'warehouse_id' => $this->selectedWarehouseId,
            'code' => $this->rackCode,
            'zone' => $this->rackZone,
            'aisle' => $this->rackAisle,
            'row' => $this->rackRow,
            'level' => $this->rackLevel,
            'capacity' => $this->rackCapacity,
            'is_active' => true,
        ]);

        $this->showRackModal = false;
        unset($this->racks);
    }

    public function deleteRack(int $rackId): void
    {
        try {
            Rack::findOrFail($rackId)->delete();
            unset($this->racks);
        } catch (QueryException) {
            $this->errorMessage = 'Rak tidak dapat dihapus karena masih memiliki stok/transaksi terkait.';
        }
    }

    public function render()
    {
        return view('livewire.warehouse-management')->layout('layouts.app', ['title' => 'Gudang & Rak']);
    }
}

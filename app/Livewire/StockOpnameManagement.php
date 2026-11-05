<?php

namespace App\Livewire;

use App\Models\StockOpname;
use App\Models\Warehouse;
use App\Services\StockOpnameService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpnameManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public string $warehouseId = '';

    public string $scheduledDate = '';

    public string $notes = '';

    public function mount(): void
    {
        $this->scheduledDate = now()->toDateString();
    }

    #[Computed]
    public function opnames()
    {
        return StockOpname::with('warehouse')->latest()->paginate(10);
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::where('is_active', true)->orderBy('name')->get();
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->warehouseId = (string) ($this->warehouses->first()->id ?? '');
        $this->scheduledDate = now()->toDateString();
        $this->notes = '';
        $this->showModal = true;
    }

    public function createOpname(StockOpnameService $service): void
    {
        $this->validate([
            'warehouseId' => ['required'],
            'scheduledDate' => ['required', 'date'],
        ]);

        $opname = $service->create(
            warehouse: Warehouse::findOrFail($this->warehouseId),
            scheduledDate: $this->scheduledDate,
            userId: auth()->id(),
            notes: $this->notes,
        );

        $this->showModal = false;
        $this->redirect(route('stock-opnames.show', $opname), navigate: false);
    }

    public function render()
    {
        return view('livewire.stock-opname-management')->layout('layouts.app', ['title' => 'Stock Opname']);
    }
}

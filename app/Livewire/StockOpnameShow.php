<?php

namespace App\Livewire;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Services\StockOpnameService;
use Livewire\Component;

class StockOpnameShow extends Component
{
    public StockOpname $stockOpname;

    /** @var array<int, string> */
    public array $physicalQty = [];

    public function mount(StockOpname $stockOpname): void
    {
        $this->stockOpname = $stockOpname;

        foreach ($stockOpname->items as $item) {
            $this->physicalQty[$item->id] = $item->physical_qty !== null ? (string) $item->physical_qty : '';
        }
    }

    public function freeze(StockOpnameService $service): void
    {
        $service->freeze($this->stockOpname);
        $this->stockOpname->refresh();

        foreach ($this->stockOpname->items as $item) {
            $this->physicalQty[$item->id] = '';
        }
    }

    public function saveCount(int $itemId, StockOpnameService $service): void
    {
        $value = $this->physicalQty[$itemId] ?? '';

        if ($value === '' || ! is_numeric($value)) {
            return;
        }

        $item = StockOpnameItem::findOrFail($itemId);
        $service->recordCount($item, (float) $value);
        $this->stockOpname->refresh();
    }

    public function complete(StockOpnameService $service): void
    {
        $service->complete($this->stockOpname, auth()->id());
        $this->stockOpname->refresh();
    }

    public function render()
    {
        return view('livewire.stock-opname-show')->layout('layouts.app', ['title' => 'Detail Stock Opname']);
    }
}

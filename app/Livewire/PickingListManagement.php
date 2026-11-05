<?php

namespace App\Livewire;

use App\Models\PickingList;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PickingListManagement extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function pickingLists()
    {
        return PickingList::with(['salesOrder.customer', 'warehouse', 'assignee'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.picking-list-management')->layout('layouts.app', ['title' => 'Picking List']);
    }
}

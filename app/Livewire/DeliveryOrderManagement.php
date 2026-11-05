<?php

namespace App\Livewire;

use App\Models\DeliveryOrder;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryOrderManagement extends Component
{
    use WithPagination;

    #[Computed]
    public function deliveryOrders()
    {
        return DeliveryOrder::with(['salesOrder.customer', 'warehouse'])->latest()->paginate(10);
    }

    public function markDelivered(int $id): void
    {
        DeliveryOrder::whereKey($id)->update(['status' => 'delivered', 'delivered_at' => now()]);
        unset($this->deliveryOrders);
    }

    public function render()
    {
        return view('livewire.delivery-order-management')->layout('layouts.app', ['title' => 'Surat Jalan']);
    }
}

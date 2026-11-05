<?php

namespace App\Livewire;

use App\Models\DeliveryOrder;
use Livewire\Component;

class DeliveryOrderShow extends Component
{
    public DeliveryOrder $deliveryOrder;

    public function mount(DeliveryOrder $deliveryOrder): void
    {
        $this->deliveryOrder = $deliveryOrder;
    }

    public function render()
    {
        return view('livewire.delivery-order-show')->layout('layouts.app', ['title' => 'Surat Jalan']);
    }
}

<?php

namespace App\Livewire;

use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use Livewire\Component;

class DeliveryOrderCreate extends Component
{
    public SalesOrder $salesOrder;

    public string $driverName = '';

    public string $vehicleNumber = '';

    public string $notes = '';

    public function mount(): void
    {
        $salesOrderId = request()->query('sales_order');
        $this->salesOrder = SalesOrder::with(['items.product', 'customer', 'warehouse'])->findOrFail($salesOrderId);
    }

    public function save(): void
    {
        $this->validate([
            'driverName' => ['required', 'string', 'max:255'],
            'vehicleNumber' => ['required', 'string', 'max:30'],
        ]);

        $deliveryOrder = DeliveryOrder::create([
            'do_number' => DeliveryOrder::generateNumber(),
            'sales_order_id' => $this->salesOrder->id,
            'warehouse_id' => $this->salesOrder->warehouse_id,
            'driver_name' => $this->driverName,
            'vehicle_number' => $this->vehicleNumber,
            'status' => 'shipped',
            'shipped_at' => now(),
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        $this->salesOrder->update(['status' => 'shipped']);

        $this->redirect(route('delivery-orders.show', $deliveryOrder), navigate: false);
    }

    public function render()
    {
        return view('livewire.delivery-order-create')->layout('layouts.app', ['title' => 'Buat Surat Jalan']);
    }
}

<?php

use App\Livewire\Actions\Logout;
use App\Livewire\Dashboard;
use App\Livewire\DeliveryOrderCreate;
use App\Livewire\DeliveryOrderManagement;
use App\Livewire\DeliveryOrderShow;
use App\Livewire\PartnerManagement;
use App\Livewire\PickingListManagement;
use App\Livewire\PickingListShow;
use App\Livewire\ProductManagement;
use App\Livewire\PurchaseOrderManagement;
use App\Livewire\PurchaseOrderShow;
use App\Livewire\SalesOrderManagement;
use App\Livewire\SalesOrderShow;
use App\Livewire\StockManagement;
use App\Livewire\StockOpnameManagement;
use App\Livewire\StockOpnameShow;
use App\Livewire\StockTransferManagement;
use App\Livewire\WarehouseManagement;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('stock', StockManagement::class)->name('stock.index');

    // Master Data
    Route::get('warehouses', WarehouseManagement::class)->name('warehouses.index');
    Route::get('products', ProductManagement::class)->name('products.index');
    Route::get('partners', PartnerManagement::class)->name('partners.index');

    // Inbound Logistics
    Route::get('purchase-orders', PurchaseOrderManagement::class)->name('purchase-orders.index');
    Route::get('purchase-orders/{purchaseOrder}', PurchaseOrderShow::class)->name('purchase-orders.show');

    // Inventory Control
    Route::get('stock-transfers', StockTransferManagement::class)->name('stock-transfers.index');
    Route::get('stock-opnames', StockOpnameManagement::class)->name('stock-opnames.index');
    Route::get('stock-opnames/{stockOpname}', StockOpnameShow::class)->name('stock-opnames.show');

    // Outbound Logistics
    Route::get('sales-orders', SalesOrderManagement::class)->name('sales-orders.index');
    Route::get('sales-orders/{salesOrder}', SalesOrderShow::class)->name('sales-orders.show');
    Route::get('picking-lists', PickingListManagement::class)->name('picking-lists.index');
    Route::get('picking-lists/{pickingList}', PickingListShow::class)->name('picking-lists.show');
    Route::get('delivery-orders', DeliveryOrderManagement::class)->name('delivery-orders.index');
    Route::get('delivery-orders/create', DeliveryOrderCreate::class)->name('delivery-orders.create');
    Route::get('delivery-orders/{deliveryOrder}', DeliveryOrderShow::class)->name('delivery-orders.show');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('logout', function (Logout $logout) {
    $logout();

    return redirect('/login');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';

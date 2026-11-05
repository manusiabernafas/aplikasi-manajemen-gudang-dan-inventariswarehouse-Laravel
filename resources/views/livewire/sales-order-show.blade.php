@php
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-600',
        'confirmed' => 'bg-indigo-100 text-indigo-700',
        'picking' => 'bg-amber-100 text-amber-700',
        'packed' => 'bg-violet-100 text-violet-700',
        'shipped' => 'bg-sky-100 text-sky-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-rose-100 text-rose-700',
    ];
    $statusLabels = [
        'draft' => 'Draft',
        'confirmed' => 'Dikonfirmasi',
        'picking' => 'Picking',
        'packed' => 'Dikemas',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    $pickingList = $salesOrder->pickingLists()->latest()->first();
    $deliveryOrder = $salesOrder->deliveryOrders()->latest()->first();
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('sales-orders.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar SO</a>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $salesOrder->so_number }}</h2>
            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$salesOrder->status] }}">{{ $statusLabels[$salesOrder->status] }}</span>
        </div>

        <div class="flex gap-2">
            @if ($salesOrder->status === 'confirmed')
                <button wire:click="generatePickingList" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    Buat Picking List
                </button>
            @elseif ($pickingList)
                <a href="{{ route('picking-lists.show', $pickingList) }}" class="rounded-lg border border-indigo-200 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                    Lihat Picking List
                </a>
            @endif

            @if ($salesOrder->status === 'packed')
                <a href="{{ route('delivery-orders.create', ['sales_order' => $salesOrder->id]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">
                    Buat Surat Jalan
                </a>
            @elseif ($deliveryOrder)
                <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="rounded-lg border border-emerald-200 px-4 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50">
                    Lihat Surat Jalan
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Customer</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $salesOrder->customer->name }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Gudang Pengirim</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $salesOrder->warehouse->name }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Nilai</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">Rp{{ number_format($salesOrder->total_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-sm font-semibold text-slate-900">Item Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Produk</th>
                        <th class="px-6 py-3 font-medium text-right">Qty Order</th>
                        <th class="px-6 py-3 font-medium text-right">Qty Dipacking</th>
                        <th class="px-6 py-3 font-medium text-right">Harga</th>
                        <th class="px-6 py-3 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($salesOrder->items as $item)
                        <tr>
                            <td class="px-6 py-3">
                                <p class="font-medium text-slate-800">{{ $item->product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $item->product->sku }}</p>
                            </td>
                            <td class="px-6 py-3 text-right text-slate-600">{{ number_format($item->quantity_ordered) }}</td>
                            <td class="px-6 py-3 text-right text-slate-600">{{ number_format($item->quantity_packed) }}</td>
                            <td class="px-6 py-3 text-right text-slate-600">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-right font-medium text-slate-800">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

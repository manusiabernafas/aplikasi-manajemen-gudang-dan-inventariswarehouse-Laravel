@php
    $salesOrder = $deliveryOrder->salesOrder()->with(['items.product', 'customer'])->first();
@endphp

<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('delivery-orders.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar surat jalan</a>
        <button onclick="window.print()" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            Cetak Surat Jalan
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        <div class="flex flex-col gap-3 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Surat Jalan</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $deliveryOrder->do_number }}</h2>
            </div>
            <div class="text-xs text-slate-500 sm:text-right">
                <p>Tanggal Kirim: {{ $deliveryOrder->shipped_at?->format('d M Y H:i') ?? '—' }}</p>
                <p>Status: <span class="font-semibold text-slate-700">{{ ucfirst($deliveryOrder->status) }}</span></p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 text-sm sm:grid-cols-2">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Dikirim Kepada</p>
                <p class="mt-1 font-semibold text-slate-800">{{ $salesOrder->customer->name }}</p>
                <p class="text-slate-500">{{ $salesOrder->customer->address }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Detail Pengiriman</p>
                <p class="mt-1 text-slate-700">SO: {{ $salesOrder->so_number }}</p>
                <p class="text-slate-700">Driver: {{ $deliveryOrder->driver_name }}</p>
                <p class="text-slate-700">Kendaraan: {{ $deliveryOrder->vehicle_number }}</p>
            </div>
        </div>

        <table class="mt-6 w-full text-left text-sm">
            <thead class="border-y border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="py-2 font-medium">Produk</th>
                    <th class="py-2 font-medium">SKU</th>
                    <th class="py-2 text-right font-medium">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($salesOrder->items as $item)
                    <tr>
                        <td class="py-2 text-slate-700">{{ $item->product->name }}</td>
                        <td class="py-2 text-slate-500">{{ $item->product->sku }}</td>
                        <td class="py-2 text-right font-medium text-slate-800">{{ number_format($item->quantity_packed) }} {{ $item->product->base_unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($deliveryOrder->notes)
            <p class="mt-4 text-xs text-slate-500">Catatan: {{ $deliveryOrder->notes }}</p>
        @endif

        <div class="mt-12 grid grid-cols-1 gap-8 text-center text-xs text-slate-500 sm:grid-cols-3 sm:gap-6">
            <div>
                <p class="mb-16">Dikirim oleh</p>
                <p class="border-t border-slate-300 pt-2">{{ $deliveryOrder->driver_name }}</p>
            </div>
            <div>
                <p class="mb-16">Diperiksa oleh</p>
                <p class="border-t border-slate-300 pt-2">Warehouse Staff</p>
            </div>
            <div>
                <p class="mb-16">Diterima oleh</p>
                <p class="border-t border-slate-300 pt-2">{{ $salesOrder->customer->name }}</p>
            </div>
        </div>
    </div>
</div>

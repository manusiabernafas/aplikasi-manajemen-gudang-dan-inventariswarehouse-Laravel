@php
    $statusStyles = [
        'pending' => 'bg-slate-100 text-slate-600',
        'in_progress' => 'bg-amber-100 text-amber-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
    ];
    $statusLabels = [
        'pending' => 'Menunggu',
        'in_progress' => 'Diproses',
        'completed' => 'Selesai',
    ];
    $items = $pickingList->items()->with(['product', 'rack'])->orderBy('sequence')->get();
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('picking-lists.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar picking</a>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $pickingList->picking_number }}</h2>
            <p class="text-xs text-slate-500">SO {{ $pickingList->salesOrder->so_number }} &middot; {{ $pickingList->warehouse->name }}</p>
            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$pickingList->status] }}">{{ $statusLabels[$pickingList->status] }}</span>
        </div>

        <div class="flex gap-2">
            @if ($pickingList->status === 'pending')
                <button wire:click="startPicking" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Mulai Picking</button>
            @elseif ($pickingList->status === 'in_progress')
                <button wire:click="completePicking" wire:confirm="Selesaikan picking? Stok akan dikurangi sesuai jumlah yang diambil." class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">
                    Selesaikan Picking
                </button>
            @endif
        </div>
    </div>

    @if ($errorMessage)
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm text-rose-700">{{ $errorMessage }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-sm font-semibold text-slate-900">Rute Pengambilan (urut rak terdekat)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">#</th>
                        <th class="px-6 py-3 font-medium">Rak</th>
                        <th class="px-6 py-3 font-medium">Produk</th>
                        <th class="px-6 py-3 font-medium text-right">Qty Diminta</th>
                        <th class="px-6 py-3 font-medium text-right">Qty Diambil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($items as $item)
                        <tr wire:key="pli-{{ $item->id }}">
                            <td class="px-6 py-3 text-slate-400">{{ $item->sequence }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $item->rack->code ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <p class="font-medium text-slate-800">{{ $item->product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $item->product->sku }}</p>
                            </td>
                            <td class="px-6 py-3 text-right text-slate-600">{{ number_format($item->quantity_to_pick) }}</td>
                            <td class="px-6 py-3 text-right">
                                @if ($pickingList->status === 'in_progress')
                                    <x-form.input type="number" step="0.01" wire:model="quantityPicked.{{ $item->id }}" class="text-right" wrapperClass="ml-auto w-24" />
                                @else
                                    {{ number_format($item->quantity_picked) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

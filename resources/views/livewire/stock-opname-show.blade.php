@php
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-600',
        'frozen' => 'bg-amber-100 text-amber-700',
        'reviewing' => 'bg-indigo-100 text-indigo-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-rose-100 text-rose-700',
    ];
    $statusLabels = [
        'draft' => 'Draft',
        'frozen' => 'Dibekukan',
        'reviewing' => 'Peninjauan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    $items = $stockOpname->items()->with('product')->get();
    $countedCount = $items->whereNotNull('physical_qty')->count();
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('stock-opnames.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar opname</a>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $stockOpname->opname_number }}</h2>
            <p class="text-xs text-slate-500">{{ $stockOpname->warehouse->name }} &middot; {{ $stockOpname->scheduled_date->format('d M Y') }}</p>
            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$stockOpname->status] }}">{{ $statusLabels[$stockOpname->status] }}</span>
        </div>

        <div class="flex gap-2">
            @if ($stockOpname->status === 'draft')
                <button wire:click="freeze" wire:confirm="Membekukan stok akan mengambil snapshot jumlah sistem saat ini untuk semua produk aktif. Lanjutkan?"
                    class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-400">
                    Bekukan Stok & Mulai Hitung
                </button>
            @elseif ($stockOpname->status === 'frozen')
                <button wire:click="complete" wire:confirm="Selesaikan opname? Selisih akan otomatis disesuaikan ke stok sistem."
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">
                    Selesaikan Opname ({{ $countedCount }}/{{ $items->count() }} dihitung)
                </button>
            @endif
        </div>
    </div>

    @if ($stockOpname->status === 'draft')
        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
            Klik "Bekukan Stok & Mulai Hitung" untuk mengambil snapshot stok sistem dan memulai penghitungan fisik.
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Produk</th>
                            <th class="px-6 py-3 font-medium text-right">Qty Sistem</th>
                            <th class="px-6 py-3 font-medium text-right">Qty Fisik</th>
                            <th class="px-6 py-3 font-medium text-right">Selisih</th>
                            <th class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($items as $item)
                            <tr wire:key="opnitem-{{ $item->id }}" class="hover:bg-slate-50/60">
                                <td class="px-6 py-3">
                                    <p class="font-medium text-slate-800">{{ $item->product->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $item->product->sku }}</p>
                                </td>
                                <td class="px-6 py-3 text-right text-slate-600">{{ number_format($item->system_qty) }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if ($stockOpname->status === 'frozen')
                                        <x-form.input type="number" step="0.01" wire:model="physicalQty.{{ $item->id }}" class="text-right" wrapperClass="ml-auto w-28" />
                                    @else
                                        {{ $item->physical_qty !== null ? number_format($item->physical_qty) : '—' }}
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right font-medium {{ $item->variance_qty > 0 ? 'text-emerald-600' : ($item->variance_qty < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                                    {{ $item->physical_qty !== null ? ($item->variance_qty > 0 ? '+' : '').number_format($item->variance_qty) : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    @if ($stockOpname->status === 'frozen')
                                        <button wire:click="saveCount({{ $item->id }})" class="rounded-lg border border-indigo-200 px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                            Simpan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

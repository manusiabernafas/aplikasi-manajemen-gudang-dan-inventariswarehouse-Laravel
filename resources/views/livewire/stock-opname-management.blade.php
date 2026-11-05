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
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Stock Opname</h2>
            <p class="text-xs text-slate-500">Hitung fisik stok, bandingkan dengan sistem, dan sesuaikan selisihnya.</p>
        </div>
        <button wire:click="openModal" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            + Buat Opname Baru
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">No. Opname</th>
                        <th class="px-6 py-3 font-medium">Gudang</th>
                        <th class="px-6 py-3 font-medium">Tanggal</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->opnames as $opname)
                        <tr wire:key="opn-{{ $opname->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $opname->opname_number }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $opname->warehouse->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $opname->scheduled_date->format('d M Y') }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$opname->status] }}">{{ $statusLabels[$opname->status] }}</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('stock-opnames.show', $opname) }}" class="rounded-lg bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada stock opname.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $this->opnames->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">Buat Stock Opname</h3>

                <form wire:submit="createOpname" class="mt-5 space-y-4">
                    <x-form.select wire:model="warehouseId" label="Gudang">
                        @foreach ($this->warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input type="date" wire:model="scheduledDate" label="Tanggal Pelaksanaan" />
                    <x-form.textarea wire:model="notes" rows="2" label="Catatan" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">Buat & Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

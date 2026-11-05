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
@endphp

<div class="space-y-6">
    <div>
        <h2 class="text-sm font-semibold text-slate-900">Picking List</h2>
        <p class="text-xs text-slate-500">Daftar tugas pengambilan barang untuk field/picker staff, diurutkan rute rak terdekat.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <x-form.select wire:model.live="statusFilter" wrapperClass="sm:w-64">
            <option value="">Semua Status</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-form.select>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">No. Picking</th>
                        <th class="px-6 py-3 font-medium">Sales Order</th>
                        <th class="px-6 py-3 font-medium">Gudang</th>
                        <th class="px-6 py-3 font-medium">Picker</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->pickingLists as $pickingList)
                        <tr wire:key="pl-{{ $pickingList->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $pickingList->picking_number }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $pickingList->salesOrder->so_number }} &middot; {{ $pickingList->salesOrder->customer->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $pickingList->warehouse->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $pickingList->assignee->name ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$pickingList->status] }}">{{ $statusLabels[$pickingList->status] }}</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('picking-lists.show', $pickingList) }}" class="rounded-lg bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada picking list.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $this->pickingLists->links() }}</div>
    </div>
</div>

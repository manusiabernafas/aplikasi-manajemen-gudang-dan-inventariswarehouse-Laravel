@php
    $statusStyles = [
        'pending' => 'bg-slate-100 text-slate-600',
        'shipped' => 'bg-sky-100 text-sky-700',
        'delivered' => 'bg-emerald-100 text-emerald-700',
    ];
    $statusLabels = [
        'pending' => 'Menunggu',
        'shipped' => 'Dikirim',
        'delivered' => 'Diterima',
    ];
@endphp

<div class="space-y-6">
    <div>
        <h2 class="text-sm font-semibold text-slate-900">Surat Jalan (Delivery Order)</h2>
        <p class="text-xs text-slate-500">Dokumen pengiriman barang ke customer.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">No. Surat Jalan</th>
                        <th class="px-6 py-3 font-medium">Sales Order</th>
                        <th class="px-6 py-3 font-medium">Customer</th>
                        <th class="px-6 py-3 font-medium">Driver / Kendaraan</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->deliveryOrders as $do)
                        <tr wire:key="do-{{ $do->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $do->do_number }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $do->salesOrder->so_number }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $do->salesOrder->customer->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $do->driver_name ?: '—' }} / {{ $do->vehicle_number ?: '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$do->status] }}">{{ $statusLabels[$do->status] }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($do->status === 'shipped')
                                        <button wire:click="markDelivered({{ $do->id }})" class="rounded-lg border border-emerald-200 px-2.5 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50">Tandai Diterima</button>
                                    @endif
                                    <a href="{{ route('delivery-orders.show', $do) }}" class="rounded-lg bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">Lihat / Cetak</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada surat jalan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $this->deliveryOrders->links() }}</div>
    </div>
</div>

@php
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-600',
        'ordered' => 'bg-indigo-100 text-indigo-700',
        'partially_received' => 'bg-amber-100 text-amber-700',
        'received' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-rose-100 text-rose-700',
    ];
    $statusLabels = [
        'draft' => 'Draft',
        'ordered' => 'Dipesan',
        'partially_received' => 'Sebagian Diterima',
        'received' => 'Diterima Lengkap',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('purchase-orders.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar PO</a>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $purchaseOrder->po_number }}</h2>
            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$purchaseOrder->status] }}">{{ $statusLabels[$purchaseOrder->status] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Supplier</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $purchaseOrder->supplier->name }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Gudang Tujuan</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $purchaseOrder->warehouse->name }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Nilai</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">Rp{{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-sm font-semibold text-slate-900">Item & Penerimaan Barang (Receiving / QC)</h3>
            <p class="text-xs text-slate-500">Proses penerimaan per item: tentukan jumlah lolos QC dan ditolak, lalu tempatkan ke rak.</p>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach ($purchaseOrder->items as $item)
                @php
                    $remaining = $item->remainingToReceive();
                    $qcBadge = [
                        'pending' => 'bg-slate-100 text-slate-600',
                        'pass' => 'bg-emerald-100 text-emerald-700',
                        'reject' => 'bg-rose-100 text-rose-700',
                        'partial' => 'bg-amber-100 text-amber-700',
                    ][$item->qc_status];
                @endphp
                <div class="px-6 py-4" wire:key="poitem-{{ $item->id }}">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $item->product->name }}</p>
                            <p class="text-xs text-slate-400">{{ $item->product->sku }} &middot; Harga Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-slate-500">
                            <span>Dipesan: <b class="text-slate-700">{{ number_format($item->quantity_ordered) }}</b></span>
                            <span>Diterima: <b class="text-emerald-600">{{ number_format($item->quantity_received) }}</b></span>
                            <span>Ditolak: <b class="text-rose-600">{{ number_format($item->quantity_rejected) }}</b></span>
                            <span class="rounded-full px-2 py-0.5 font-medium {{ $qcBadge }}">{{ strtoupper($item->qc_status) }}</span>
                        </div>
                    </div>

                    @if ($remaining > 0)
                        @if ($receivingItemId === $item->id)
                            <div class="mt-3 rounded-xl border border-indigo-100 bg-indigo-50/40 p-4">
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    <x-form.input type="number" step="0.01" wire:model="receiveQty" label="Qty Lolos QC" />
                                    <x-form.input type="number" step="0.01" wire:model="rejectQty" label="Qty Ditolak" />
                                    <x-form.select wire:model="rackId" label="Tempatkan di Rak">
                                        @foreach ($this->racks as $rack)
                                            <option value="{{ $rack->id }}">{{ $rack->code }}</option>
                                        @endforeach
                                    </x-form.select>
                                    <x-form.input type="text" wire:model="qcNotes" label="Catatan QC" />
                                </div>
                                @error('receiveQty') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
                                @error('rackId') <p class="mt-2 text-xs text-rose-600">Rak tujuan wajib dipilih.</p> @enderror

                                <div class="mt-3 flex justify-end gap-2">
                                    <button wire:click="cancelReceiveForm" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                                    <button wire:click="processReceiving" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">Proses Penerimaan</button>
                                </div>
                            </div>
                        @else
                            <button wire:click="openReceiveForm({{ $item->id }})" class="mt-3 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">
                                Terima Barang (sisa {{ number_format($remaining) }})
                            </button>
                        @endif
                    @else
                        <p class="mt-3 text-xs font-medium text-emerald-600">Item ini sudah selesai diproses sepenuhnya.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

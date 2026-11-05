<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Stock Transfer</h2>
            <p class="text-xs text-slate-500">Pindahkan stok antar gudang secara atomik dan tercatat.</p>
        </div>
        <button wire:click="openModal" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            + Buat Transfer
        </button>
    </div>

    @if ($errorMessage)
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm text-rose-700">{{ $errorMessage }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">No. Transfer</th>
                        <th class="px-6 py-3 font-medium">Dari</th>
                        <th class="px-6 py-3 font-medium">Ke</th>
                        <th class="px-6 py-3 font-medium">Item</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->transfers as $transfer)
                        <tr wire:key="trf-{{ $transfer->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $transfer->transfer_number }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $transfer->fromWarehouse->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $transfer->toWarehouse->name }}</td>
                            <td class="px-6 py-3 text-slate-600">
                                {{ $transfer->items->map(fn ($i) => $i->product->name.' ('.number_format($i->quantity).')')->join(', ') }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    {{ $transfer->status === 'completed' ? 'Selesai' : ucfirst($transfer->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-400">{{ $transfer->transferred_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada transfer stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $this->transfers->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">Transfer Stok Antar Gudang</h3>

                <form wire:submit="submitTransfer" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.select wire:model="fromWarehouseId" label="Dari Gudang">
                            @foreach ($this->warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.select wire:model="toWarehouseId" label="Ke Gudang" name="toWarehouseId">
                            @foreach ($this->warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="text-xs font-medium text-slate-600">Produk yang Dipindahkan</label>
                            <button type="button" wire:click="addItemRow" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">+ Tambah Baris</button>
                        </div>
                        <div class="space-y-2">
                            @foreach ($items as $index => $item)
                                <div class="flex flex-col gap-2 rounded-xl border border-slate-100 p-3 sm:flex-row sm:items-center sm:border-0 sm:p-0" wire:key="trfitem-{{ $index }}">
                                    <x-form.select wire:model="items.{{ $index }}.product_id" wrapperClass="sm:flex-1">
                                        <option value="">Pilih Produk</option>
                                        @foreach ($this->products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </x-form.select>
                                    <div class="flex gap-2">
                                        <x-form.input type="number" step="0.01" wire:model="items.{{ $index }}.quantity" placeholder="Qty" wrapperClass="flex-1 sm:w-28 sm:flex-none" />
                                        <button type="button" wire:click="removeItemRow({{ $index }})" class="h-fit shrink-0 rounded-xl p-2.5 text-rose-500 hover:bg-rose-50">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('items') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <x-form.textarea wire:model="notes" rows="2" label="Catatan" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-60">Proses Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

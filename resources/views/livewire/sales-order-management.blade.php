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
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Sales Order</h2>
            <p class="text-xs text-slate-500">Kelola pesanan penjualan dari customer.</p>
        </div>
        <button wire:click="openModal" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            + Buat Sales Order
        </button>
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
                        <th class="px-6 py-3 font-medium">No. SO</th>
                        <th class="px-6 py-3 font-medium">Customer</th>
                        <th class="px-6 py-3 font-medium">Gudang</th>
                        <th class="px-6 py-3 font-medium">Tanggal</th>
                        <th class="px-6 py-3 font-medium text-right">Total</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->salesOrders as $so)
                        <tr wire:key="so-{{ $so->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $so->so_number }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $so->customer->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $so->warehouse->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $so->order_date->format('d M Y') }}</td>
                            <td class="px-6 py-3 text-right text-slate-700">Rp{{ number_format($so->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$so->status] }}">{{ $statusLabels[$so->status] }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($so->status === 'draft')
                                        <button wire:click="confirmOrder({{ $so->id }})" class="rounded-lg border border-indigo-200 px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50">Konfirmasi</button>
                                    @endif
                                    <a href="{{ route('sales-orders.show', $so) }}" class="rounded-lg bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada sales order.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $this->salesOrders->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">Buat Sales Order</h3>

                <form wire:submit="saveSalesOrder" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.select wire:model="customerId" label="Customer" name="customerId">
                            <option value="">Pilih Customer</option>
                            @foreach ($this->customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.select wire:model="warehouseId" label="Gudang Pengirim" name="warehouseId">
                            <option value="">Pilih Gudang</option>
                            @foreach ($this->warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.input type="date" wire:model="orderDate" label="Tanggal Order" />
                        <x-form.input type="date" wire:model="requiredDate" label="Tanggal Dibutuhkan" />
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="text-xs font-medium text-slate-600">Item Pesanan</label>
                            <button type="button" wire:click="addItemRow" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">+ Tambah Baris</button>
                        </div>
                        <div class="space-y-2">
                            @foreach ($items as $index => $item)
                                <div class="flex flex-col gap-2 rounded-xl border border-slate-100 p-3 sm:flex-row sm:items-center sm:border-0 sm:p-0" wire:key="soitem-{{ $index }}">
                                    <x-form.select wire:model="items.{{ $index }}.product_id" wrapperClass="sm:flex-1">
                                        <option value="">Pilih Produk</option>
                                        @foreach ($this->products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </x-form.select>
                                    <div class="flex gap-2">
                                        <x-form.input type="number" step="0.01" wire:model="items.{{ $index }}.quantity" placeholder="Qty" wrapperClass="flex-1 sm:w-24 sm:flex-none" />
                                        <x-form.input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price" placeholder="Harga Jual" wrapperClass="flex-1 sm:w-32 sm:flex-none" />
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
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">Simpan Draft</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

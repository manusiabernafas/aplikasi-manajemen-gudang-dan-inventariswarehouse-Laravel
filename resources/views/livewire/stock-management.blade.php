<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Daftar Produk & Stok</h2>
            <p class="text-xs text-slate-500">Pantau level stok, filter per gudang, dan lakukan penyesuaian cepat.</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="grid grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:flex lg:items-center lg:gap-3">
        <div class="relative lg:min-w-[220px] lg:flex-1">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Cari nama, SKU, atau barcode..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm shadow-sm transition-colors hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
            >
        </div>

        <x-form.select wire:model.live="warehouseFilter">
            <option value="">Semua Gudang</option>
            @foreach ($this->warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
        </x-form.select>

        <x-form.select wire:model.live="categoryFilter">
            <option value="">Semua Kategori</option>
            @foreach ($this->categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </x-form.select>

        <x-form.select wire:model.live="statusFilter">
            <option value="">Semua Status</option>
            <option value="healthy">Stok Sehat</option>
            <option value="low">Stok Rendah</option>
            <option value="out">Stok Kosong</option>
        </x-form.select>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 z-10 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Produk</th>
                        <th class="px-6 py-3 font-medium">Kategori</th>
                        <th class="px-6 py-3 font-medium">Stok{{ $warehouseFilter ? ' (Gudang Terpilih)' : ' (Total)' }}</th>
                        <th class="px-6 py-3 font-medium">Level</th>
                        <th class="px-6 py-3 font-medium text-right">Biaya Rata-rata</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->products as $product)
                        @php
                            $stock = $warehouseFilter ? (float) ($product->warehouse_stock ?? 0) : (float) $product->current_stock;
                            $ceiling = (float) ($product->max_stock ?: ($product->min_stock * 3 ?: 1));
                            $percent = max(2, min(100, ($stock / max($ceiling, 1)) * 100));

                            $status = match (true) {
                                $stock <= 0 => 'out',
                                $stock <= $product->min_stock => 'low',
                                $product->max_stock && $stock >= $product->max_stock => 'over',
                                default => 'healthy',
                            };

                            $statusStyles = [
                                'out' => ['label' => 'Stok Kosong', 'badge' => 'bg-rose-100 text-rose-700', 'bar' => 'bg-rose-500'],
                                'low' => ['label' => 'Stok Rendah', 'badge' => 'bg-amber-100 text-amber-700', 'bar' => 'bg-amber-500'],
                                'over' => ['label' => 'Overstock', 'badge' => 'bg-violet-100 text-violet-700', 'bar' => 'bg-violet-500'],
                                'healthy' => ['label' => 'Sehat', 'badge' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500'],
                            ][$status];
                        @endphp
                        <tr wire:key="stock-row-{{ $product->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3">
                                <p class="font-medium text-slate-800">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <p class="font-semibold text-slate-800">{{ number_format($stock) }} <span class="font-normal text-slate-400">{{ $product->base_unit }}</span></p>
                                <p class="text-xs text-slate-400">min {{ number_format($product->min_stock) }}@if($product->max_stock) &middot; maks {{ number_format($product->max_stock) }}@endif</p>
                            </td>
                            <td class="px-6 py-3">
                                <div class="h-2 w-28 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $statusStyles['bar'] }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right text-slate-600">Rp{{ number_format($product->average_cost, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles['badge'] }}">
                                    {{ $statusStyles['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex justify-end gap-2">
                                    <button
                                        wire:click="openTransferModal({{ $product->id }})"
                                        class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50"
                                    >
                                        Transfer
                                    </button>
                                    <button
                                        wire:click="openAdjustModal({{ $product->id }})"
                                        class="rounded-lg bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-500"
                                    >
                                        Adjust
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">Tidak ada produk yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $this->products->links() }}
        </div>
    </div>

    {{-- Quick Adjustment Modal --}}
    @if ($showAdjustModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" wire:key="adjust-modal">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.closeAdjustModal()">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Penyesuaian Stok Cepat</h3>
                        <p class="text-xs text-slate-500">Koreksi jumlah stok fisik dengan pencatatan alasan.</p>
                    </div>
                    <button wire:click="closeAdjustModal" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="submitAdjust" class="mt-5 space-y-4">
                    <x-form.select wire:model="adjustWarehouseId" label="Gudang" name="adjustWarehouseId">
                        @foreach ($this->warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </x-form.select>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-600">Arah Penyesuaian</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border py-2.5 text-sm font-medium transition-colors
                                {{ $adjustDirection === 'in' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                                <input type="radio" wire:model="adjustDirection" value="in" class="hidden">
                                Tambah Stok
                            </label>
                            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border py-2.5 text-sm font-medium transition-colors
                                {{ $adjustDirection === 'out' ? 'border-rose-500 bg-rose-50 text-rose-700' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                                <input type="radio" wire:model="adjustDirection" value="out" class="hidden">
                                Kurangi Stok
                            </label>
                        </div>
                    </div>

                    <x-form.input type="number" step="0.01" wire:model="adjustQuantity" placeholder="0" label="Jumlah" name="adjustQuantity" />

                    <x-form.textarea wire:model="adjustReason" rows="2" placeholder="Contoh: Barang rusak saat penyimpanan" label="Alasan Penyesuaian" name="adjustReason" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeAdjustModal" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-60">
                            <span wire:loading.remove>Simpan Penyesuaian</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Transfer Modal --}}
    @if ($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" wire:key="transfer-modal">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.closeTransferModal()">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Transfer Stok Antar Gudang</h3>
                        <p class="text-xs text-slate-500">Pindahkan stok produk ini ke gudang lain.</p>
                    </div>
                    <button wire:click="closeTransferModal" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="submitTransfer" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.select wire:model="transferFromWarehouseId" label="Dari Gudang">
                            @foreach ($this->warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.select wire:model="transferToWarehouseId" label="Ke Gudang" name="transferToWarehouseId">
                            @foreach ($this->warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <x-form.input type="number" step="0.01" wire:model="transferQuantity" placeholder="0" label="Jumlah" name="transferQuantity" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeTransferModal" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-60">
                            <span wire:loading.remove>Proses Transfer</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

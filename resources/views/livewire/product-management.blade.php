<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Produk & Kategori</h2>
            <p class="text-xs text-slate-500">Kelola master produk, SKU, dan kategori.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openCategoryModal" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Kelola Kategori
            </button>
            <button wire:click="openProductModal" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                + Tambah Produk
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center">
        <div class="relative flex-1 sm:min-w-[220px]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
            </svg>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama atau SKU..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm shadow-sm transition-colors hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
        </div>
        <x-form.select wire:model.live="categoryFilter" wrapperClass="sm:w-56">
            <option value="">Semua Kategori</option>
            @foreach ($this->categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </x-form.select>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">SKU / Barcode</th>
                        <th class="px-6 py-3 font-medium">Nama Produk</th>
                        <th class="px-6 py-3 font-medium">Kategori</th>
                        <th class="px-6 py-3 font-medium">Satuan</th>
                        <th class="px-6 py-3 font-medium">Costing</th>
                        <th class="px-6 py-3 font-medium">Min/Maks</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->products as $product)
                        <tr wire:key="prod-{{ $product->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3">
                                <p class="font-medium text-slate-800">{{ $product->sku }}</p>
                                <p class="text-xs text-slate-400">{{ $product->barcode }}</p>
                            </td>
                            <td class="px-6 py-3 text-slate-700">{{ $product->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $product->base_unit }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ strtoupper($product->costing_method) }}</span>
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ number_format($product->min_stock) }} / {{ $product->max_stock ? number_format($product->max_stock) : '—' }}</td>
                            <td class="px-6 py-3">
                                <button wire:click="toggleProductActive({{ $product->id }})"
                                    class="rounded-full px-2.5 py-1 text-xs font-medium {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <button wire:click="openProductModal({{ $product->id }})" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Edit</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-slate-400">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $this->products->links() }}</div>
    </div>

    {{-- Product Modal --}}
    @if ($showProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">{{ $editingProductId ? 'Edit Produk' : 'Tambah Produk' }}</h3>

                <form wire:submit="saveProduct" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.input type="text" wire:model="sku" label="SKU" name="sku" />
                        <x-form.select wire:model="categoryId" label="Kategori">
                            <option value="">Tanpa Kategori</option>
                            @foreach ($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <x-form.input type="text" wire:model="name" label="Nama Produk" name="name" />

                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input type="text" wire:model="baseUnit" label="Satuan Dasar" />
                        <x-form.select wire:model="costingMethod" label="Metode Costing">
                            <option value="fifo">FIFO</option>
                            <option value="average">Average</option>
                        </x-form.select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input type="number" step="0.01" wire:model="minStock" label="Stok Minimum" name="minStock" />
                        <x-form.input type="number" step="0.01" wire:model="maxStock" label="Stok Maksimum" name="maxStock" />
                    </div>

                    <x-form.textarea wire:model="description" rows="2" label="Deskripsi" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showProductModal', false)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Category Modal --}}
    @if ($showCategoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Kelola Kategori</h3>
                    <button wire:click="$set('showCategoryModal', false)" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveCategory" class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-end">
                    <x-form.input type="text" wire:model="catCode" label="Kode" wrapperClass="sm:w-24" />
                    <x-form.input type="text" wire:model="catName" label="Nama Kategori" wrapperClass="flex-1" />
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">
                        {{ $editingCategoryId ? 'Update' : 'Tambah' }}
                    </button>
                </form>
                @error('catCode') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                @error('catName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                <div class="mt-4 max-h-72 space-y-1.5 overflow-y-auto">
                    @foreach ($this->categories as $category)
                        <div wire:key="cat-{{ $category->id }}" class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $category->name }}</p>
                                <p class="text-xs text-slate-400">{{ $category->code }} &middot; {{ $category->products_count }} produk</p>
                            </div>
                            <div class="flex gap-2">
                                <span wire:click="openCategoryModal({{ $category->id }})" class="cursor-pointer text-xs font-medium text-indigo-600 hover:text-indigo-700">Edit</span>
                                <span wire:click="deleteCategory({{ $category->id }})" wire:confirm="Hapus kategori ini? Produk terkait akan menjadi tanpa kategori." class="cursor-pointer text-xs font-medium text-rose-600 hover:text-rose-700">Hapus</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Gudang & Lokasi Rak</h2>
            <p class="text-xs text-slate-500">Kelola master gudang dan lokasi rak penyimpanan.</p>
        </div>
        <button wire:click="openWarehouseModal" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            + Tambah Gudang
        </button>
    </div>

    @if ($errorMessage)
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm text-rose-700">{{ $errorMessage }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Warehouse list --}}
        <div class="space-y-3 lg:col-span-1">
            @forelse ($this->warehouses as $warehouse)
                <button
                    wire:click="selectWarehouse({{ $warehouse->id }})"
                    wire:key="wh-{{ $warehouse->id }}"
                    class="w-full rounded-2xl border p-4 text-left shadow-sm transition
                        {{ $selectedWarehouseId === $warehouse->id ? 'border-indigo-500 bg-indigo-50/60' : 'border-slate-200 bg-white hover:border-slate-300' }}"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $warehouse->name }}</p>
                            <p class="text-xs text-slate-400">{{ $warehouse->code }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500">{{ $warehouse->racks_count }} rak</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">{{ $warehouse->address ?: 'Alamat belum diisi' }}</p>
                    <p class="mt-1 text-xs text-slate-400">PIC: {{ $warehouse->manager_name ?: '—' }} &middot; {{ $warehouse->phone ?: '—' }}</p>

                    <div class="mt-3 flex gap-2" onclick="event.stopPropagation()">
                        <span wire:click.stop="openWarehouseModal({{ $warehouse->id }})" class="cursor-pointer rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">Edit</span>
                        <span wire:click.stop="deleteWarehouse({{ $warehouse->id }})" wire:confirm="Hapus gudang ini?" class="cursor-pointer rounded-lg border border-rose-200 px-2.5 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">Hapus</span>
                    </div>
                </button>
            @empty
                <p class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-400">Belum ada gudang.</p>
            @endforelse
        </div>

        {{-- Racks --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Lokasi Rak</h3>
                    <p class="text-xs text-slate-500">Rak penyimpanan untuk gudang terpilih.</p>
                </div>
                @if ($selectedWarehouseId)
                    <button wire:click="openRackModal" class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                        + Tambah Rak
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Kode Rak</th>
                            <th class="px-6 py-3 font-medium">Zona</th>
                            <th class="px-6 py-3 font-medium">Lorong</th>
                            <th class="px-6 py-3 font-medium">Baris</th>
                            <th class="px-6 py-3 font-medium">Level</th>
                            <th class="px-6 py-3 font-medium">Kapasitas</th>
                            <th class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->racks as $rack)
                            <tr wire:key="rack-{{ $rack->id }}" class="hover:bg-slate-50/60">
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $rack->code }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $rack->zone ?: '—' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $rack->aisle ?: '—' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $rack->row ?: '—' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $rack->level ?: '—' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $rack->capacity ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="openRackModal({{ $rack->id }})" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Edit</button>
                                        <button wire:click="deleteRack({{ $rack->id }})" wire:confirm="Hapus rak ini?" class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada rak untuk gudang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Warehouse Modal --}}
    @if ($showWarehouseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">{{ $editingWarehouseId ? 'Edit Gudang' : 'Tambah Gudang' }}</h3>

                <form wire:submit="saveWarehouse" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.input type="text" wire:model="whCode" label="Kode Gudang" name="whCode" />
                        <x-form.input type="text" wire:model="whName" label="Nama Gudang" name="whName" />
                    </div>
                    <x-form.textarea wire:model="whAddress" rows="2" label="Alamat" />
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.input type="text" wire:model="whPhone" label="No. Telepon" />
                        <x-form.input type="text" wire:model="whManagerName" label="Nama PIC" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showWarehouseModal', false)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Rack Modal --}}
    @if ($showRackModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">{{ $editingRackId ? 'Edit Rak' : 'Tambah Rak' }}</h3>

                <form wire:submit="saveRack" class="mt-5 space-y-4">
                    <x-form.input type="text" wire:model="rackCode" placeholder="A1-01" label="Kode Rak" name="rackCode" />
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <x-form.input type="text" wire:model="rackZone" label="Zona" />
                        <x-form.input type="text" wire:model="rackAisle" label="Lorong" />
                        <x-form.input type="text" wire:model="rackRow" label="Baris" />
                        <x-form.input type="text" wire:model="rackLevel" label="Level" />
                    </div>
                    <x-form.input type="number" wire:model="rackCapacity" label="Kapasitas (unit)" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showRackModal', false)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

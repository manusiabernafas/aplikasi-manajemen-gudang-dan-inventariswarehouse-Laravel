<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Supplier & Customer</h2>
            <p class="text-xs text-slate-500">Kelola mitra bisnis untuk transaksi pembelian dan penjualan.</p>
        </div>
        <button wire:click="openModal" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            + Tambah {{ $activeTab === 'suppliers' ? 'Supplier' : 'Customer' }}
        </button>
    </div>

    @if ($errorMessage)
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm text-rose-700">{{ $errorMessage }}</div>
    @endif

    <div class="flex items-center gap-2 border-b border-slate-200">
        <button wire:click="setTab('suppliers')" class="border-b-2 px-4 py-2.5 text-sm font-medium {{ $activeTab === 'suppliers' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            Supplier
        </button>
        <button wire:click="setTab('customers')" class="border-b-2 px-4 py-2.5 text-sm font-medium {{ $activeTab === 'customers' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            Customer
        </button>
    </div>

    <div class="relative w-full sm:max-w-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
        </svg>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama atau kode..."
            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm shadow-sm transition-colors hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Kode</th>
                        <th class="px-6 py-3 font-medium">Nama</th>
                        <th class="px-6 py-3 font-medium">Kontak</th>
                        <th class="px-6 py-3 font-medium">Telepon</th>
                        <th class="px-6 py-3 font-medium">Email</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse (($activeTab === 'suppliers' ? $this->suppliers : $this->customers) as $partner)
                        <tr wire:key="partner-{{ $partner->id }}" class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $partner->code }}</td>
                            <td class="px-6 py-3 text-slate-700">{{ $partner->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $partner->contact_person ?: '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $partner->phone ?: '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $partner->email ?: '—' }}</td>
                            <td class="px-6 py-3">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="openModal({{ $partner->id }})" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Edit</button>
                                    <button wire:click="delete({{ $partner->id }})" wire:confirm="Hapus data ini?" class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-900">
                    {{ $editingId ? 'Edit' : 'Tambah' }} {{ $activeTab === 'suppliers' ? 'Supplier' : 'Customer' }}
                </h3>

                <form wire:submit="save" class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.input type="text" wire:model="code" label="Kode" name="code" />
                        <x-form.input type="text" wire:model="name" label="Nama" name="name" />
                    </div>
                    <x-form.input type="text" wire:model="contactPerson" label="Nama Kontak (PIC)" />
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <x-form.input type="text" wire:model="phone" label="Telepon" />
                        <x-form.input type="email" wire:model="email" label="Email" name="email" />
                    </div>
                    <x-form.textarea wire:model="address" rows="2" label="Alamat" />

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

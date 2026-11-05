<div class="space-y-6">
        {{-- KPI Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-slate-500">Total SKU Aktif</p>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 text-indigo-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-8.25-4.5L3.75 7.5m16.5 0-8.25 4.5m8.25-4.5v9l-8.25 4.5m0-9L3.75 7.5m8.25 4.5v9m-8.25-9v9l8.25 4.5" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($this->totalSkus) }}</p>
                <p class="mt-1 text-xs text-slate-500">Produk aktif dalam sistem</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-slate-500">Total Valuasi Stok</p>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 text-emerald-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182.553-.44 1.278-.659 2.003-.659.725 0 1.45.22 2.003.659" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Rp{{ number_format($this->totalValuation, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Nilai stok × biaya rata-rata</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50/40 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-amber-700">Stok Rendah</p>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 text-amber-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-amber-700">{{ number_format($this->lowStockCount) }}</p>
                <p class="mt-1 text-xs text-amber-700/70">Produk di bawah stok minimum</p>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50/40 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-rose-700">Stok Kosong</p>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 text-rose-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-rose-700">{{ number_format($this->outOfStockCount) }}</p>
                <p class="mt-1 text-xs text-rose-700/70">Produk perlu restock segera</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Chart --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Pergerakan Stok 7 Hari Terakhir</h3>
                        <p class="text-xs text-slate-500">Perbandingan barang masuk vs keluar</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1.5 text-slate-500"><span class="h-2 w-2 rounded-full bg-indigo-500"></span>Masuk</span>
                        <span class="flex items-center gap-1.5 text-slate-500"><span class="h-2 w-2 rounded-full bg-slate-300"></span>Keluar</span>
                    </div>
                </div>

                @php
                    $chart = $this->movementChart;
                    $max = max(1, collect($chart)->flatMap(fn ($d) => [$d['in'], $d['out']])->max());
                @endphp

                <div class="mt-6 flex h-48 items-end justify-between gap-3">
                    @foreach ($chart as $day)
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="flex h-40 w-full items-end justify-center gap-1">
                                <div class="w-1/2 rounded-t-md bg-indigo-500" style="height: {{ max(4, ($day['in'] / $max) * 100) }}%" title="Masuk: {{ $day['in'] }}"></div>
                                <div class="w-1/2 rounded-t-md bg-slate-200" style="height: {{ max(4, ($day['out'] / $max) * 100) }}%" title="Keluar: {{ $day['out'] }}"></div>
                            </div>
                            <span class="text-[11px] font-medium text-slate-400">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Low stock list --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Perlu Perhatian</h3>
                <p class="text-xs text-slate-500">Produk mendekati atau di bawah stok minimum</p>

                <div class="mt-4 space-y-3">
                    @forelse ($this->lowStockProducts as $product)
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-100 px-3 py-2.5">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                {{ (int) $product->current_stock }}/{{ (int) $product->min_stock }}
                            </span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">Semua stok dalam kondisi aman.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Stock Movements --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Pergerakan Stok Terbaru</h3>
                <a href="{{ route('stock.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Lihat semua stok &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Produk</th>
                            <th class="px-6 py-3 font-medium">Gudang</th>
                            <th class="px-6 py-3 font-medium">Tipe</th>
                            <th class="px-6 py-3 font-medium text-right">Qty</th>
                            <th class="px-6 py-3 font-medium">Oleh</th>
                            <th class="px-6 py-3 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->recentMovements as $movement)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-6 py-3">
                                    <p class="font-medium text-slate-800">{{ $movement->product->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-400">{{ $movement->product->sku ?? '' }}</p>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $movement->warehouse->code ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <x-movement-type-badge :type="$movement->type" />
                                </td>
                                <td class="px-6 py-3 text-right font-medium {{ $movement->isInbound() ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $movement->isInbound() ? '+' : '-' }}{{ number_format($movement->quantity) }}
                                </td>
                                <td class="px-6 py-3 text-slate-500">{{ $movement->creator->name ?? 'Sistem' }}</td>
                                <td class="px-6 py-3 text-slate-400">{{ $movement->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada pergerakan stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

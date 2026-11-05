@php
    $navGroups = [
        [
            'label' => 'Utama',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'chart-bar'],
                ['label' => 'Manajemen Stok', 'route' => 'stock.index', 'icon' => 'cube'],
            ],
        ],
        [
            'label' => 'Master Data',
            'items' => [
                ['label' => 'Gudang & Rak', 'route' => 'warehouses.index', 'icon' => 'building-storefront'],
                ['label' => 'Produk & Kategori', 'route' => 'products.index', 'icon' => 'tag'],
                ['label' => 'Supplier & Customer', 'route' => 'partners.index', 'icon' => 'users'],
            ],
        ],
        [
            'label' => 'Inbound Logistics',
            'items' => [
                ['label' => 'Purchase Order', 'route' => 'purchase-orders.index', 'icon' => 'clipboard-document-list'],
            ],
        ],
        [
            'label' => 'Inventory Control',
            'items' => [
                ['label' => 'Stock Transfer', 'route' => 'stock-transfers.index', 'icon' => 'arrows-right-left'],
                ['label' => 'Stock Opname', 'route' => 'stock-opnames.index', 'icon' => 'clipboard-document-check'],
            ],
        ],
        [
            'label' => 'Outbound Logistics',
            'items' => [
                ['label' => 'Sales Order', 'route' => 'sales-orders.index', 'icon' => 'shopping-cart'],
                ['label' => 'Picking List', 'route' => 'picking-lists.index', 'icon' => 'map'],
                ['label' => 'Surat Jalan', 'route' => 'delivery-orders.index', 'icon' => 'truck'],
            ],
        ],
    ];

    $icons = [
        'chart-bar' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
        'cube' => 'M21 7.5l-9-5.25L3 7.5m18 0-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25M21 7.5v9l-9 5.25m0-9.75v9.75',
        'building-storefront' => 'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-16.9-14.03A2.25 2.25 0 0 1 6.75 5.25h10.5a2.25 2.25 0 0 1 2.25 2.25v.386c0 .597-.237 1.169-.659 1.591l-.621.621A2.25 2.25 0 0 1 16.629 12H7.371a2.25 2.25 0 0 1-1.591-.659l-.621-.621a2.25 2.25 0 0 1-.659-1.591V6.97Z',
        'tag' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.83.699 2.528 0l7.409-7.409c.699-.699.699-1.83 0-2.528l-9.581-9.581A2.25 2.25 0 0 0 9.568 3ZM6.375 8.25a1.125 1.125 0 1 1 0-2.25 1.125 1.125 0 0 1 0 2.25Z',
        'users' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
        'clipboard-document-list' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.152 0 2.243.328 3.132.902m-1.023-.902a48.4 48.4 0 0 0-1.123.08c-1.13.094-1.976 1.057-1.976 2.192v.223',
        'check-badge' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286Z',
        'arrows-right-left' => 'M7.5 21 3 16.5m0 0L7.5 12M3 16.5h18M16.5 3 21 7.5m0 0L16.5 12M21 7.5H3',
        'clipboard-document-check' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'shopping-cart' => 'M2.25 3h1.386c.51 0 .955.343 1.087.836l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.918-4.7 2.373-7.15a1.19 1.19 0 0 0-1.157-1.397H5.106M7.5 14.25 5.106 5.85M7.5 14.25 3.94 5.836',
        'map' => 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.879 3 6.27 3 6.695V19.18c0 .836.88 1.379 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z',
        'truck' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375c-.621 0-1.125-.504-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 0h17.25m-17.25 0V9m17.25 5.25V9M3.75 9h16.5M3.75 9l.933-3.732A1.5 1.5 0 0 1 6.132 4.125h6.868c.62 0 1.163.42 1.32 1.023L15.75 9',
    ];
@endphp

<aside
    @keydown.escape.window="sidebarOpen = false"
    class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-slate-900 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    style="transition: transform .2s ease"
>
    <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-white/10 px-6">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['cube'] }}" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold leading-tight text-white">WMS Nexus</p>
            <p class="text-[11px] leading-tight text-slate-400">Warehouse &amp; Inventory</p>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6 scrollbar-thin">
        @foreach ($navGroups as $group)
            <div>
                <p class="px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $group['label'] }}</p>
                <div class="mt-2 space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php
                            $routeBase = ! empty($item['route']) ? preg_replace('/\.index$/', '', $item['route']) : null;
                            $isActive = ! empty($item['route']) && (request()->routeIs($item['route']) || request()->routeIs($routeBase.'.*'));
                            $isDisabled = $item['disabled'] ?? false;
                        @endphp

                        @if ($isDisabled)
                            <div class="group flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                                <span class="flex items-center gap-3">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 text-slate-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                                    </svg>
                                    {{ $item['label'] }}
                                </span>
                                <span class="rounded-full bg-slate-800 px-2 py-0.5 text-[10px] font-medium text-slate-400">Segera</span>
                            </div>
                        @else
                            <a
                                href="{{ route($item['route']) }}"
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ $isActive ? 'bg-indigo-500/15 text-indigo-300' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                                    class="h-5 w-5 {{ $isActive ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                                </svg>
                                {{ $item['label'] }}
                                @if ($isActive)
                                    <span class="ml-auto h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-lg bg-white/5 px-3 py-2.5">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name ?? 'Tamu' }}</p>
                <p class="truncate text-xs text-slate-400">{{ ucwords(str_replace('_', ' ', auth()->user()->role ?? '')) }}</p>
            </div>
        </div>
    </div>
</aside>

<div
    x-show="sidebarOpen"
    x-cloak
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-slate-900/60 lg:hidden"
></div>

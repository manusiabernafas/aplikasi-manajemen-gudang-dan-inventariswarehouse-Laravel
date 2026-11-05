@props(['title' => 'Dashboard'])

<header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 bg-white px-4 shadow-sm sm:px-6 lg:px-8">
    <button
        type="button"
        @click="sidebarOpen = true"
        class="-ml-1 rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="min-w-0 flex-1">
        <h1 class="truncate text-lg font-semibold text-slate-900">{{ $title }}</h1>
    </div>

    <div class="hidden items-center sm:flex">
        <div class="relative">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
            </svg>
            <input
                type="text"
                placeholder="Cari SKU, produk, PO..."
                class="w-64 rounded-lg border-0 bg-slate-100 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500"
            >
        </div>
    </div>

    <button type="button" class="relative rounded-full p-2 text-slate-500 hover:bg-slate-100">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-rose-500"></span>
    </button>

    <div x-data="{ userMenuOpen: false }" class="relative">
        <button @click="userMenuOpen = !userMenuOpen" type="button" class="flex items-center gap-2 rounded-full pl-1 pr-2 py-1 hover:bg-slate-100">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}
            </div>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden h-4 w-4 text-slate-400 sm:block">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <div
            x-show="userMenuOpen"
            x-cloak
            @click.outside="userMenuOpen = false"
            x-transition
            class="absolute right-0 z-30 mt-2 w-52 origin-top-right rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg"
        >
            <div class="px-2.5 py-2">
                <p class="truncate text-sm font-medium text-slate-900">{{ auth()->user()->name ?? 'Tamu' }}</p>
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="{{ route('profile') }}" class="block rounded-lg px-2.5 py-2 text-sm text-slate-700 hover:bg-slate-50">Profil Saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full rounded-lg px-2.5 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>

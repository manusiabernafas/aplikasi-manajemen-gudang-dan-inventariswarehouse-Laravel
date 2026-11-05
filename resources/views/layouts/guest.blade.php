<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full bg-slate-100 font-sans antialiased">
        <div class="flex min-h-full flex-col items-center justify-center px-4 py-10">
            <a href="/" class="mb-6 flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25M21 7.5v9l-9 5.25m0-9.75v9.75" />
                    </svg>
                </div>
                <span class="text-base font-bold text-slate-900">WMS Nexus</span>
            </a>

            <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-7 shadow-lg shadow-slate-200/60">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-slate-400">&copy; {{ date('Y') }} WMS Nexus. All rights reserved.</p>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' – ' : '' }}{{ config('app.name', 'WMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full font-sans antialiased bg-slate-50 text-slate-900">
        <div class="flex h-full min-h-screen" x-data="{ sidebarOpen: false }">
            <x-sidebar />

            <div class="flex min-w-0 flex-1 flex-col lg:pl-72">
                <x-topbar :title="$title ?? 'Dashboard'" />

                <main class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-[1600px]">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — Admin Bawa Beres' : 'Admin Dashboard — ' . config('app.name', 'Bawa Beres') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-slate-800 antialiased flex overflow-hidden">
    <!-- Sidebar Scaffolding -->
    <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col flex-shrink-0 border-r border-slate-800">
        <!-- Logo / Brand -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800 gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-white font-bold text-sm">
                BB
            </span>
            <div class="flex flex-col">
                <span class="font-bold text-sm text-white leading-tight">Bawa Beres</span>
                <span class="text-[10px] text-amber-400 uppercase tracking-wider font-semibold">Admin Panel</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto text-sm">
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-slate-800 text-white font-medium">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-400 hover:bg-slate-800/60 hover:text-white transition-colors">
                <span>📦</span>
                <span>Manajemen Order</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-400 hover:bg-slate-800/60 hover:text-white transition-colors">
                <span>🏢</span>
                <span>Storage & Inventory</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-400 hover:bg-slate-800/60 hover:text-white transition-colors">
                <span>🚚</span>
                <span>Jadwal & Armada</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-400 hover:bg-slate-800/60 hover:text-white transition-colors">
                <span>💳</span>
                <span>Pembayaran</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-400 hover:bg-slate-800/60 hover:text-white transition-colors">
                <span>⚙️</span>
                <span>Pengaturan</span>
            </a>
        </nav>

        <!-- Admin Profile Footer -->
        <div class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 font-bold">
                    A
                </div>
                <span>Administrator</span>
            </div>
        </div>
    </aside>

    <!-- Main Admin Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
            <div class="font-semibold text-slate-800 text-lg">
                {{ $header ?? 'Dashboard' }}
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 border border-emerald-200">
                    Sistem Online (Malang)
                </span>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>

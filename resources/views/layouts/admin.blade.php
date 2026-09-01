<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — Admin Bawa Beres' : 'Admin Panel — ' . config('app.name', 'Bawa Beres') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-slate-800 antialiased flex overflow-hidden selection:bg-amber-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Backdrop -->
    <div 
        x-show="sidebarOpen" 
        x-cloak 
        @click="sidebarOpen = false" 
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs md:hidden"
    ></div>

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-200 flex flex-col flex-shrink-0 border-r border-slate-800 transition-transform duration-200 ease-in-out md:static"
    >
        <!-- Logo / Brand -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 text-slate-950 font-bold text-base shadow-sm shadow-amber-500/30">
                    <x-icon name="box" class="w-5 h-5 text-slate-950" />
                </span>
                <div class="flex flex-col">
                    <span class="font-bold text-sm text-white leading-tight">Bawa<span class="text-amber-500">Beres</span></span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Admin Panel</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto text-sm">
            <!-- Dashboard -->
            <a 
                href="{{ route('admin.dashboard') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="chart-bar" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-slate-950' : 'text-amber-400' }}" />
                <span>Dashboard</span>
            </a>

            <!-- Orders -->
            @can('manage-orders')
            <a 
                href="{{ route('admin.orders') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.orders*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="box" class="w-5 h-5 {{ request()->routeIs('admin.orders*') ? 'text-slate-950' : 'text-blue-400' }}" />
                <span>Manajemen Order</span>
            </a>
            @endcan

            <!-- Customers -->
            @can('manage-customers')
            <a 
                href="{{ route('admin.customers') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.customers*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="users" class="w-5 h-5 {{ request()->routeIs('admin.customers*') ? 'text-slate-950' : 'text-indigo-400' }}" />
                <span>Data Pelanggan</span>
            </a>
            @endcan

            <!-- Services -->
            @can('manage-services')
            <a 
                href="{{ route('admin.services') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.services*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="tag" class="w-5 h-5 {{ request()->routeIs('admin.services*') ? 'text-slate-950' : 'text-amber-400' }}" />
                <span>Katalog Layanan</span>
            </a>
            @endcan

            <!-- Schedule -->
            @can('manage-schedule')
            <a 
                href="{{ route('admin.schedule') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.schedule*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="truck" class="w-5 h-5 {{ request()->routeIs('admin.schedule*') ? 'text-slate-950' : 'text-emerald-400' }}" />
                <span>Jadwal & Armada</span>
            </a>
            @endcan

            <!-- Inventory -->
            @can('manage-inventory')
            <a 
                href="{{ route('admin.inventory') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.inventory*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="qr" class="w-5 h-5 {{ request()->routeIs('admin.inventory*') ? 'text-slate-950' : 'text-purple-400' }}" />
                <span>Item & QR Label</span>
            </a>
            @endcan

            <!-- Storage -->
            @can('manage-storage')
            <a 
                href="{{ route('admin.storage') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.storage*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="warehouse" class="w-5 h-5 {{ request()->routeIs('admin.storage*') ? 'text-slate-950' : 'text-cyan-400' }}" />
                <span>Gudang Storage</span>
            </a>
            @endcan

            <!-- Payments -->
            @can('manage-payments')
            <a 
                href="{{ route('admin.payments') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.payments*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="credit-card" class="w-5 h-5 {{ request()->routeIs('admin.payments*') ? 'text-slate-950' : 'text-emerald-400' }}" />
                <span>Pembayaran</span>
            </a>
            @endcan

            <!-- Settings -->
            @can('manage-settings')
            <a 
                href="{{ route('admin.settings') }}" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.settings*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-sm shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                <x-icon name="cog" class="w-5 h-5 {{ request()->routeIs('admin.settings*') ? 'text-slate-950' : 'text-slate-400' }}" />
                <span>Pengaturan Sistem</span>
            </a>
            @endcan
        </nav>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-slate-800 flex flex-col gap-3">
            @auth
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-amber-400 text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-medium text-xs text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-400 truncate">{{ auth()->user()->role->label() }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-slate-800/80 hover:bg-rose-950/40 text-xs font-semibold text-slate-400 hover:text-rose-300 border border-slate-700/60 hover:border-rose-900/50 transition-all cursor-pointer">
                    <x-icon name="logout" class="w-4 h-4 text-rose-400" />
                    <span>Keluar (Logout)</span>
                </button>
            </form>
            @endauth
        </div>
    </aside>

    <!-- Main Admin Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                    <x-icon name="chart-bar" class="w-5 h-5" />
                </button>
                <h1 class="font-bold text-slate-900 text-lg">
                    {{ $title ?? 'Admin Dashboard' }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                @auth
                <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ auth()->user()->role->value }}
                </span>
                @endauth
                <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-amber-600 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-amber-300 bg-slate-50 transition-colors">
                    <x-icon name="external-link" class="w-3.5 h-3.5 text-slate-500" />
                    <span>Website Publik</span>
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>

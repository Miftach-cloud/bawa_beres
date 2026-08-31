<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' . config('app.name', 'Bawa Beres') : config('app.name', 'Bawa Beres') . ' | Moving, Storage & Delivery Kota Malang' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased flex flex-col selection:bg-amber-500 selection:text-white">
    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-xl tracking-tight text-slate-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm shadow-amber-500/30">
                        📦
                    </span>
                    <span>Bawa<span class="text-amber-600">Beres</span></span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                    <a href="{{ url('/') }}" class="hover:text-slate-900 transition-colors">Beranda</a>
                    <a href="#services" class="hover:text-slate-900 transition-colors">Layanan</a>
                    <a href="#tracking" class="hover:text-slate-900 transition-colors">Cek Status Order</a>
                    <a href="#faq" class="hover:text-slate-900 transition-colors">FAQ</a>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <a href="#booking" class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 transition-all">
                    Pesan Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>© {{ date('Y') }} {{ config('app.name', 'Bawa Beres') }} — Moving, Storage & Delivery Kota Malang. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:text-slate-700">Syarat & Ketentuan</a>
                <span>•</span>
                <a href="#" class="hover:text-slate-700">Kebijakan Privasi</a>
                <span>•</span>
                <a href="#" class="hover:text-slate-700">Bantuan WhatsApp</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>

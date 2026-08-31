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
                <a href="{{ url('/') }}" class="flex items-center gap-2 font-black text-xl tracking-tight text-slate-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black shadow-sm shadow-amber-500/30">
                        📦
                    </span>
                    <span>Bawa<span class="text-amber-600">Beres</span></span>
                </a>

                <nav class="hidden lg:flex items-center gap-6 text-xs font-bold text-slate-600">
                    <a href="{{ url('/') }}" class="hover:text-slate-900 transition-colors">Beranda</a>
                    <a href="{{ route('public.services') }}" class="hover:text-slate-900 transition-colors">Layanan</a>
                    <a href="{{ route('public.how-it-works') }}" class="hover:text-slate-900 transition-colors">Cara Kerja</a>
                    <a href="{{ route('public.storage-security') }}" class="hover:text-slate-900 transition-colors">Keamanan Storage</a>
                    <a href="{{ route('public.coverage') }}" class="hover:text-slate-900 transition-colors">Area Layanan</a>
                    <a href="{{ route('public.faq') }}" class="hover:text-slate-900 transition-colors">FAQ</a>
                    <a href="{{ route('public.track') }}" class="text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/80 hover:bg-amber-100 transition-colors">📍 Cek Resi / Order</a>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('/#booking') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-black text-slate-950 shadow-sm hover:bg-amber-400 active:scale-98 transition-all">
                    🚀 Pesan Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Rich Public Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-12 border-b border-slate-100 text-xs">
                <!-- Brand Info -->
                <div class="space-y-3 md:col-span-1">
                    <div class="flex items-center gap-2 font-black text-lg text-slate-900">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-slate-950">📦</span>
                        <span>Bawa<span class="text-amber-600">Beres</span></span>
                    </div>
                    <p class="text-slate-500 leading-relaxed">
                        Platform logistik, jasa pindahan terpercaya, dan penitipan barang mahasiswa/umum ber-QR Code di Malang Raya.
                    </p>
                    <div class="text-[11px] text-slate-400 font-mono">
                        Hub: Jl. Soekarno Hatta No. 88, Lowokwaru, Kota Malang
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="space-y-3">
                    <h4 class="font-extrabold text-slate-900 uppercase tracking-wider text-[11px]">Navigasi Utama</h4>
                    <ul class="space-y-2 text-slate-600 font-medium">
                        <li><a href="{{ url('/') }}" class="hover:text-amber-600">Beranda</a></li>
                        <li><a href="{{ route('public.services') }}" class="hover:text-amber-600">Katalog Layanan</a></li>
                        <li><a href="{{ route('public.how-it-works') }}" class="hover:text-amber-600">Cara Kerja 4 Langkah</a></li>
                        <li><a href="{{ route('public.storage-security') }}" class="hover:text-amber-600">Fasilitas & Keamanan Storage</a></li>
                        <li><a href="{{ route('public.coverage') }}" class="hover:text-amber-600">Cakupan Wilayah Malang Raya</a></li>
                    </ul>
                </div>

                <!-- Features & Support -->
                <div class="space-y-3">
                    <h4 class="font-extrabold text-slate-900 uppercase tracking-wider text-[11px]">Bantuan & Fitur</h4>
                    <ul class="space-y-2 text-slate-600 font-medium">
                        <li><a href="{{ route('public.track') }}" class="hover:text-amber-600 font-bold text-amber-700">📍 Lacak Status Pesanan</a></li>
                        <li><a href="{{ route('public.faq') }}" class="hover:text-amber-600">Tanya Jawab (FAQ)</a></li>
                        <li><a href="{{ route('public.about') }}" class="hover:text-amber-600">Tentang BawaBeres</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-amber-600">Kontak & Lokasi Hub</a></li>
                        <li><a href="{{ url('/admin/login') }}" class="text-slate-400 hover:text-slate-600">Portal Staff Admin</a></li>
                    </ul>
                </div>

                <!-- Direct WhatsApp CTA -->
                <div class="space-y-3">
                    <h4 class="font-extrabold text-slate-900 uppercase tracking-wider text-[11px]">Hubungi Kami</h4>
                    <p class="text-slate-500">
                        Butuh respon cepat atau konsultasi estimasi pindahan armada?
                    </p>
                    <a 
                        href="https://wa.me/6281234567890" 
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2.5 font-bold text-white shadow-xs transition"
                    >
                        <span>💬</span>
                        <span>WhatsApp +62 812-3456-7890</span>
                    </a>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <p>© {{ date('Y') }} {{ config('app.name', 'Bawa Beres') }} — Moving, Storage & Delivery Kota Malang. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('public.faq') }}" class="hover:text-slate-600">Syarat & Ketentuan</a>
                    <span>•</span>
                    <a href="{{ route('public.storage-security') }}" class="hover:text-slate-600">Kebijakan Privasi</a>
                    <span>•</span>
                    <a href="https://wa.me/6281234567890" target="_blank" class="hover:text-slate-600">WhatsApp Support</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>

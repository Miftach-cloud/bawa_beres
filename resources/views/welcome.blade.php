@extends('layouts.public')

@section('content')
<div class="relative overflow-hidden py-16 sm:py-24">
    <!-- Background glow -->
    <div class="absolute inset-0 -z-10 flex items-center justify-center opacity-30">
        <div class="h-[500px] w-[500px] rounded-full bg-gradient-to-tr from-amber-400 to-amber-200 blur-3xl"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Hero Title -->
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-1.5 text-xs font-semibold text-amber-700 border border-amber-200/80 mb-6">
                <span>🚀</span>
                <span>Moving, Storage & Delivery Kota Malang</span>
            </div>
            
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                Pindahan & Titip Barang Jadi <span class="text-amber-600">Beres & Praktis</span>
            </h1>
            
            <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                Platform all-in-one jasa pindahan kost/rumah, penitipan barang aman ber-QR Code, dan pengiriman barang se-Malang Raya.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="#booking" class="rounded-xl bg-amber-500 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-amber-500/25 hover:bg-amber-600 active:scale-98 transition-all">
                    Mulai Pesan Sekarang
                </a>
                <a href="#services" class="rounded-xl bg-white border border-slate-200 px-6 py-3.5 text-base font-semibold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                    Pelajari Layanan
                </a>
            </div>
        </div>

        <!-- System Foundation Check Card -->
        <div class="mt-16 max-w-2xl mx-auto">
            <livewire:public.system-status />
        </div>

        <!-- Services Preview Section -->
        <div id="services" class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="h-12 w-12 rounded-xl bg-amber-100 flex items-center justify-center text-2xl mb-6">
                    🚚
                </div>
                <h3 class="text-xl font-bold text-slate-900">Jasa Pindahan</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Pindahan kost, rumah, atau kantor dengan armada pick-up/truk dan tenaga angkut profesional.
                </p>
            </div>

            <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl mb-6">
                    🏢
                </div>
                <h3 class="text-xl font-bold text-slate-900">Storage & Titip Barang</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Simpan barang liburan semester atau renovasi dengan label QR, foto dokumentasi, dan rak teralokasi.
                </p>
            </div>

            <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="h-12 w-12 rounded-xl bg-emerald-100 flex items-center justify-center text-2xl mb-6">
                    📍
                </div>
                <h3 class="text-xl font-bold text-slate-900">Delivery & Logistik</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Pengiriman instan antar area Malang Raya dengan pelacakan status realtime dari penjemputan hingga selesai.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

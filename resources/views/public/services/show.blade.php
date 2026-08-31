@extends('layouts.public', ['title' => $service->name . ' — BawaBeres'])

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-12">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 gap-2">
            <a href="{{ url('/') }}" class="hover:text-slate-900">Beranda</a>
            <span>/</span>
            <a href="{{ route('public.services') }}" class="hover:text-slate-900">Layanan</a>
            <span>/</span>
            <span class="text-amber-600 font-bold">{{ $service->name }}</span>
        </nav>

        <!-- Main Card -->
        <div class="rounded-3xl bg-white p-8 sm:p-12 border border-slate-200 shadow-xl space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-8 border-b border-slate-100">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                        <span>⭐</span>
                        <span>Layanan Terverifikasi BawaBeres</span>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">
                        {{ $service->name }}
                    </h1>
                    <p class="text-sm text-slate-600 leading-relaxed max-w-xl">
                        {{ $service->description ?? 'Solusi pengangkutan dan penyimpanan profesional di Kota Malang dengan jaminan keamanan dan pelacakan digital.' }}
                    </p>
                </div>

                <div class="bg-amber-50 p-6 rounded-2xl border border-amber-200/80 text-center sm:text-right min-w-[200px]">
                    <span class="text-[11px] font-bold text-amber-900/60 uppercase tracking-wider block">Estimasi Tarif Awal</span>
                    <span class="text-3xl font-black text-slate-900 block mt-1">Rp {{ number_format($service->base_price, 0, ',', '.') }}</span>
                    <span class="text-[11px] text-slate-500 block mt-1">Belum termasuk item khusus</span>
                </div>
            </div>

            <!-- Features & Benefits Grid -->
            <div class="space-y-4">
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Keunggulan & Fasilitas Layanan:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="text-amber-600 text-xl font-bold">🏷️ Label QR Inventaris</div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Setiap kardus dan perabot diberi label token QR anti-rusak untuk memastikan zero item loss selama proses handling.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="text-amber-600 text-xl font-bold">📸 Foto Dokumentasi Digital</div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Kondisi fisik barang difoto saat penjemputan awal sebagai bukti otentik serah terima dan kontrol kualitas.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="text-amber-600 text-xl font-bold">🚚 Armada Bersih & Siap Angkut</div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Armada pick-up bak / box dan van tertutup yang bersih dan dilengkapi tali pengaman perabot.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="text-amber-600 text-xl font-bold">📍 Pelacakan Realtime</div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Cek status pesanan secara mandiri via fitur pelacakan publik tanpa harus login.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CTA Actions -->
            <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row gap-4">
                <a 
                    href="{{ url('/#booking') }}" 
                    class="flex-1 rounded-2xl bg-amber-500 hover:bg-amber-400 py-4 text-center text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition cursor-pointer"
                >
                    🚀 Pesan Layanan Ini Sekarang
                </a>
                <a 
                    href="https://wa.me/6281234567890?text={{ rawurlencode('Halo Admin BawaBeres, saya ingin tanya detail layanan ' . $service->name) }}" 
                    target="_blank"
                    class="flex-1 rounded-2xl border border-slate-300 hover:bg-slate-50 py-4 text-center text-sm font-bold text-slate-700 transition cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>💬</span>
                    <span>Tanya Customer Service</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

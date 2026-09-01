@php
    $title = 'Layanan Pindahan & Storage Kota Malang';
@endphp
@extends('layouts.public')

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-bold text-amber-800">
                <x-icon name="box" class="w-4 h-4 text-amber-700" />
                <span>Katalog Layanan Resmi</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Solusi Praktis untuk Setiap Kebutuhan Logistik Anda
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Pilih layanan yang sesuai kebutuhan Anda. Didukung oleh armada terawat, tenaga angkut berpengalaman, dan sistem pelacakan digital terintegrasi.
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse ($services as $service)
                <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg flex flex-col justify-between hover:border-amber-400 hover:shadow-xl transition-all duration-200">
                    <div class="space-y-4">
                        <div class="h-14 w-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shadow-xs">
                            @if (str_contains(strtolower($service->name), 'pindah'))
                                <x-icon name="truck" class="w-7 h-7 text-amber-600" />
                            @elseif (str_contains(strtolower($service->name), 'titip') || str_contains(strtolower($service->name), 'storage'))
                                <x-icon name="warehouse" class="w-7 h-7 text-blue-600" />
                            @else
                                <x-icon name="truck" class="w-7 h-7 text-emerald-600" />
                            @endif
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">{{ $service->name }}</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $service->description ?? 'Layanan profesional dengan standar kualitas tinggi, perlindungan barang, dan penjemputan tepat waktu.' }}</p>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Mulai Dari</span>
                            <div class="flex items-baseline gap-1 mt-0.5">
                                <span class="text-2xl font-black text-slate-900">Rp {{ number_format($service->base_price, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-500 font-semibold">/ estimasi awal</span>
                            </div>
                        </div>

                        <ul class="space-y-2.5 pt-3 text-xs text-slate-600">
                            <li class="flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4 text-emerald-500" />
                                <span>Label QR Code identitas unik per item</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4 text-emerald-500" />
                                <span>Dokumentasi foto serah terima</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4 text-emerald-500" />
                                <span>Pelacakan status realtime</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 space-y-2">
                        <a 
                            href="{{ route('public.services.show', $service) }}" 
                            class="w-full flex items-center justify-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 px-4 py-3 text-xs font-bold text-slate-950 shadow-md shadow-amber-500/20 transition cursor-pointer"
                        >
                            <span>Detail Layanan & Booking</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 text-slate-950" />
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-slate-400">
                    Belum ada data layanan aktif.
                </div>
            @endforelse
        </div>

        <!-- Consultation Banner -->
        <div class="rounded-3xl bg-slate-900 p-8 sm:p-12 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl">
            <div class="space-y-2 text-center md:text-left">
                <h3 class="text-2xl font-black tracking-tight">Butuh Layanan Khusus atau Paket Kantor?</h3>
                <p class="text-xs sm:text-sm text-slate-400 max-w-xl">
                    Konsultasikan langsung volume barang, jarak pengiriman, atau durasi sewa storage dengan Customer Support kami.
                </p>
            </div>
            <a 
                href="https://wa.me/6281234567890?text=Halo%20Admin%20BawaBeres,%20saya%20ingin%20konsultasi%20layanan%20khusus" 
                target="_blank"
                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 hover:bg-emerald-400 px-6 py-4 text-xs font-bold text-slate-950 shadow-lg shadow-emerald-500/20 transition cursor-pointer whitespace-nowrap"
            >
                <x-icon name="chat" class="w-4 h-4 text-slate-950" />
                <span>Hubungi WhatsApp CS</span>
            </a>
        </div>
    </div>
</div>
@endsection

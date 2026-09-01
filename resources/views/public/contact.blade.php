@php
    $title = 'Hubungi Kami — BawaBeres';
@endphp
@extends('layouts.public')

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100/80 px-3 py-1 text-xs font-bold text-emerald-800">
                <x-icon name="chat" class="w-4 h-4 text-emerald-600" />
                <span>Layanan Pelanggan & Hub Gudang</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Siap Membantu Kebutuhan Logistik Anda
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl mx-auto">
                Hubungi tim Customer Service kami atau kunjungi hub operasional kami di Kota Malang.
            </p>
        </div>

        <!-- Contact Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md space-y-3 text-center">
                <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                    <x-icon name="chat" class="w-6 h-6 text-emerald-600" />
                </div>
                <h3 class="text-base font-extrabold text-slate-900">WhatsApp Resmi</h3>
                <p class="text-xs text-slate-500">Respon cepat setiap hari:</p>
                <p class="font-mono font-bold text-slate-900 text-sm">+62 812-3456-7890</p>
                <a 
                    href="https://wa.me/6281234567890" 
                    target="_blank"
                    class="inline-block mt-2 text-xs font-bold text-emerald-600 hover:text-emerald-700"
                >
                    Chat Sekarang →
                </a>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md space-y-3 text-center">
                <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
                    <x-icon name="warehouse" class="w-6 h-6 text-amber-600" />
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Hub Storage Malang</h3>
                <p class="text-xs text-slate-500">Lokasi fasilitas gudang:</p>
                <p class="text-xs font-semibold text-slate-800">Jl. Soekarno Hatta No. 88, Lowokwaru, Kota Malang</p>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md space-y-3 text-center">
                <div class="h-12 w-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto">
                    <x-icon name="clock" class="w-6 h-6 text-blue-600" />
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Jam Operasional</h3>
                <p class="text-xs text-slate-500">Penjemputan & Layanan:</p>
                <p class="text-xs font-semibold text-slate-800">Senin – Minggu<br>07.00 – 21.00 WIB</p>
            </div>
        </div>

        <!-- Direct CTA -->
        <div class="rounded-3xl bg-white p-8 sm:p-12 border border-slate-200 shadow-xl text-center space-y-4">
            <h3 class="text-2xl font-black text-slate-900">Ingin Langsung Melakukan Pemesanan?</h3>
            <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto">
                Gunakan form pemesanan online instan kami untuk penawaran harga cepat.
            </p>
            <div class="pt-2">
                <a 
                    href="{{ url('/#booking') }}" 
                    class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 hover:bg-amber-400 px-8 py-4 text-xs font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition cursor-pointer"
                >
                    <x-icon name="sparkles" class="w-4 h-4 text-slate-950" />
                    <span>Buka Form Booking Online</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

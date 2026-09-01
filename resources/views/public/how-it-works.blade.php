@php
    $title = 'Cara Kerja Layanan — BawaBeres';
@endphp
@extends('layouts.public')

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-bold text-amber-800">
                <x-icon name="sparkles" class="w-4 h-4 text-amber-600" />
                <span>Alur Praktis 4 Langkah</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Bagaimana BawaBeres Bekerja untuk Anda
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Dari pemesanan online hingga pengantaran atau penyimpanan barang, semuanya tercatat secara transparan dan aman.
            </p>
        </div>

        <!-- 4-Step Process Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="rounded-3xl bg-white p-6 border border-slate-200 shadow-md space-y-4 relative">
                <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-black shadow-md shadow-amber-500/20">
                    1
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Pesan Online Tanpa Ribet</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Isi form booking dengan rincian barang, alamat penjemputan, dan jadwal yang diinginkan tanpa harus registrasi akun.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 border border-slate-200 shadow-md space-y-4 relative">
                <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-black shadow-md shadow-amber-500/20">
                    2
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Estimasi & Penawaran Resmi</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tim kami meninjau volume barang & jarak rute, lalu mengirimkan rincian estimasi biaya resmi langsung via WhatsApp.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 border border-slate-200 shadow-md space-y-4 relative">
                <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-black shadow-md shadow-amber-500/20">
                    3
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Penjemputan & Label QR</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Armada datang tepat waktu. Setiap barang ditempeli stiker QR Code unik dan difoto kondisinya sebelum dimuat.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 border border-slate-200 shadow-md space-y-4 relative">
                <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-black shadow-md shadow-amber-500/20">
                    4
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Antar Tujuan / Simpan Aman</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Barang diantar langsung ke lokasi baru atau disimpan rapi di rak bertingkat gudang BawaBeres. Lacak status kapan saja.
                </p>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center pt-8">
            <a 
                href="{{ url('/#booking') }}" 
                class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 hover:bg-amber-400 px-8 py-4 text-sm font-extrabold text-slate-950 shadow-xl shadow-amber-500/25 transition cursor-pointer"
            >
                <span>Mulai Booking Sekarang</span>
                <span>→</span>
            </a>
        </div>
    </div>
</div>
@endsection

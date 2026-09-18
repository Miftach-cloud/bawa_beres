@php
    $title = 'Cara Kerja Layanan — BawaBeres';
@endphp
@extends('layouts.public')

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Bagaimana BawaBeres Bekerja untuk Anda
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Dari pemesanan online hingga pengantaran atau penyimpanan barang, semuanya beres tanpa bikin repot.
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
                <h3 class="text-lg font-extrabold text-slate-900">Jemput & Angkut Barang</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Armada dan tim helper datang tepat waktu. Barang dicek dan difoto kondisinya sebelum dimuat ke armada dengan hati-hati.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 border border-slate-200 shadow-md space-y-4 relative">
                <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-black shadow-md shadow-amber-500/20">
                    4
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Antar Tujuan / Simpan Aman</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Barang diantar langsung ke lokasi baru atau disimpan rapi di gudang BawaBeres. Terima beres tanpa ribet.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

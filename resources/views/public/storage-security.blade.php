@extends('layouts.public', ['title' => 'Keamanan & Fasilitas Storage — BawaBeres'])

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100/80 px-3 py-1 text-xs font-bold text-emerald-800">
                <span>🛡️</span>
                <span>Standar Keamanan Tingkat Tinggi</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Barang Anda Aman, Terjaga & Teralokasi Rapi
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Kami memahami betapa berharganya barang pribadi Anda. Fasilitas penyimpanan BawaBeres dilengkapi sistem keamanan fisik dan digital berstandar industri.
            </p>
        </div>

        <!-- Security Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg space-y-4">
                <div class="h-14 w-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-3xl font-bold">
                    📹
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">CCTV 24/7 & Pengawasan Akses</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap sudut lorong dan rak gudang diawasi kamera CCTV beresolusi tinggi 24 jam nonstop dengan akses terbatas hanya untuk staf berwenang.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg space-y-4">
                <div class="h-14 w-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-3xl font-bold">
                    🏷️
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">SOP QR Code & Anti Tertukar</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap kardus/unit memiliki stiker QR Code unik. Staf wajib memindai QR saat perpindahan rak untuk menjamin akurasi 100%.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg space-y-4">
                <div class="h-14 w-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-3xl font-bold">
                    🔒
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Gudang Bersih, Kering & Bebas Hama</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Ruang penyimpanan dengan ventilasi udara terkontrol, bebas dari kelembapan tinggi, kebocoran air, dan bebas rayap/hama pengerat.
                </p>
            </div>
        </div>

        <!-- Warehouse Grid & Structure Info -->
        <div class="rounded-3xl bg-white p-8 sm:p-12 border border-slate-200 shadow-xl space-y-6">
            <h2 class="text-2xl font-black text-slate-900">Struktur Penempatan Bertingkat (Tiered Racking)</h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Di BawaBeres, barang Anda tidak ditumpuk sembarangan di lantai. Kami menggunakan sistem slot rak terstruktur dengan kode lokasi unik (Zone - Rack - Level) yang tercatat otomatis di sistem manajemen gudang kami.
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs font-semibold text-slate-700">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                    <span class="text-xl">📦</span>
                    <span>Rak Khusus Kardus & Box</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                    <span class="text-xl">🛏️</span>
                    <span>Pallet Kasur & Springbed Bersih</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                    <span class="text-xl">🏍️</span>
                    <span>Area Parkir Titip Motor Tertutup</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $title = 'Area Layanan Malang Raya — BawaBeres';
@endphp
@extends('layouts.public')

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Melayani Seluruh Wilayah Malang Raya
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Armada BawaBeres siap menjemput dan mengantar barang ke seluruh sudut Kota Malang, Kabupaten Malang, dan Kota Wisata Batu.
            </p>
        </div>

        <!-- Coverage Zones Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <x-icon name="building" class="w-6 h-6 text-amber-600" />
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Kota Malang (Zona Utama)</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Penjemputan cepat di seluruh kecamatan dan area kampus:
                </p>
                <ul class="text-xs text-slate-700 space-y-1.5 font-medium">
                    <li>• Lowokwaru (UB, UM, UIN, Polinema, Soehat)</li>
                    <li>• Klojen (Pusat Kota, Kayutangan, Ijen)</li>
                    <li>• Blimbing (Arjosari, Borobudur, Sulfat)</li>
                    <li>• Sukun (Sukun, Bandulan, Kebonsari)</li>
                    <li>• Kedungkandang (Sawojajar, Buring, Gribig)</li>
                </ul>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                    <x-icon name="compass" class="w-6 h-6 text-blue-600" />
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Kota Batu</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Layanan pindahan villa, homestay, rumah tangga, dan bisnis di:
                </p>
                <ul class="text-xs text-slate-700 space-y-1.5 font-medium">
                    <li>• Batu (Oro-oro Ombo, Sisir, Songgokerto)</li>
                    <li>• Junrejo (Beji, Pendem, Mojorejo)</li>
                    <li>• Bumiaji (Puspo, Punten, Tulungrejo)</li>
                </ul>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-lg space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <x-icon name="map-pin" class="w-6 h-6 text-emerald-600" />
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Kabupaten Malang</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Cakupan area perimeter dan perumahan berkembang:
                </p>
                <ul class="text-xs text-slate-700 space-y-1.5 font-medium">
                    <li>• Dau & Landungsari (UMM Kampus 3)</li>
                    <li>• Karangploso & Singosari (Akses Tol)</li>
                    <li>• Pakis (Akses Bandara Abdulrachman Saleh)</li>
                    <li>• Kepanjen & Pakisaji (Malang Selatan)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

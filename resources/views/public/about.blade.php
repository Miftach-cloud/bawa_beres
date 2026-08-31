@extends('layouts.public', ['title' => 'Tentang Kami — BawaBeres'])

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-bold text-amber-800">
                <span>🏢</span>
                <span>Tentang Platform Kami</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Mitra Terpercaya Logistik & Storage di Kota Malang
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                BawaBeres lahir untuk mengatasi stres dan keribetan saat pindahan kost/rumah dan menitipkan barang mahasiswa di Malang Raya.
            </p>
        </div>

        <!-- Vision & Mission Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md space-y-3">
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl font-bold">
                    🎯
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Visi Kami</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Menjadi platform layanan pindahan, penyimpanan aman, dan logistik lokal nomor 1 di Indonesia yang paling transparan, amanah, dan terintegrasi secara digital.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md space-y-3">
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                    🚀
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Misi Kami</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Memberikan pengalaman pindahan tanpa cemas dengan armada terawat, tenaga angkut terpercaya, sistem inventaris ber-QR Code, dan harga yang transparan tanpa biaya siluman.
                </p>
            </div>
        </div>

        <!-- Values Card -->
        <div class="rounded-3xl bg-white p-8 sm:p-12 border border-slate-200 shadow-xl space-y-6">
            <h2 class="text-2xl font-black text-slate-900">Nilai Inti BawaBeres (Core Values)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2">
                <div class="space-y-2">
                    <span class="text-2xl block">🤝</span>
                    <h4 class="font-bold text-slate-900 text-sm">Integritas & Amanah</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Menjaga setiap barang pelanggan seperti menjaga milik sendiri.</p>
                </div>
                <div class="space-y-2">
                    <span class="text-2xl block">⚡</span>
                    <h4 class="font-bold text-slate-900 text-sm">Praktis & Cepat</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Alur pemesanan tanpa registrasi ribet, respon CS dalam hitungan menit.</p>
                </div>
                <div class="space-y-2">
                    <span class="text-2xl block">🛡️</span>
                    <h4 class="font-bold text-slate-900 text-sm">Keamanan Terjamin</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Pengawasan CCTV, foto dokumentasi serah terima, dan pelacakan QR Code.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

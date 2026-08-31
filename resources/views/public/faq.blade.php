@extends('layouts.public', ['title' => 'Tanya Jawab (FAQ) — BawaBeres'])

@section('content')
<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-12">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-bold text-amber-800">
                <span>❓</span>
                <span>Pusat Bantuan & Tanya Jawab</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Pertanyaan yang Sering Diajukan
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Temukan jawaban lengkap seputar layanan pindahan, penitipan barang, tarif, dan keamanan di BawaBeres.
            </p>
        </div>

        <!-- FAQ List -->
        <div class="space-y-4">
            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Apakah saya wajib membuat akun untuk memesan?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Tidak. Sistem BawaBeres dirancang tanpa hambatan (frictionless). Anda cukup mengisi nama dan nomor WhatsApp aktif. Semua update penawaran dan status dapat diakses langsung.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Bagaimana cara menghitung biaya pindahan?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Biaya dihitung transparan berdasarkan jenis layanan, perkiraan volume muatan armada, jarak tempuh rute, dan bantuan tenaga angkut. Anda akan menerima rincian penawaran resmi sebelum menyetujui.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Bagaimana keamanan barang yang dititipkan di storage?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Gudang kami diawasi CCTV 24 jam nonstop, bebas banjir, bersih dan kering. Setiap kardus/unit barang diberi label token QR Code unik dan foto kondisi barang disimpan pada sistem kami.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Berapa lama batas minimal sewa storage?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Kami melayani durasi sewa harian, mingguan (sangat cocok untuk libur semester mahasiswa), hingga bulanan dan tahunan.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Bagaimana cara melacak barang saya?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Anda dapat mengakses halaman <a href="{{ route('public.track') }}" class="text-amber-600 font-bold underline">Lacak Status Pesanan</a> dengan memasukkan Nomor Order (contoh: <code>ORD-2026-000051</code>) dan nomor HP Anda.
                </p>
            </div>
        </div>

        <!-- Help Banner -->
        <div class="rounded-3xl bg-amber-50 border border-amber-200 p-8 text-center space-y-4">
            <h3 class="text-lg font-black text-slate-900">Punya Pertanyaan Lain yang Belum Terjawab?</h3>
            <a 
                href="https://wa.me/6281234567890?text=Halo%20Admin%20BawaBeres,%20saya%20ingin%20tanya%20seputar%20layanan" 
                target="_blank"
                class="inline-block rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-6 py-3.5 shadow-md shadow-emerald-600/10 transition"
            >
                💬 Chat Langsung dengan Admin
            </a>
        </div>
    </div>
</div>
@endsection

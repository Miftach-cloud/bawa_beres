@extends('layouts.public')

@push('schema')
@php
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'Apakah perlu membuat akun untuk booking?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Tidak perlu. Anda cukup memasukkan nomor WhatsApp untuk menerima penawaran dan memantau status pesanan.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Berapa tarif penitipan barang storage?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Tarif dihitung per item atau paket kardus/motor dengan durasi harian, mingguan, maupun bulanan yang sangat fleksibel.',
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')


<div class="relative overflow-hidden">
    <!-- Hero Section -->
    <div class="relative py-16 sm:py-24 bg-gradient-to-b from-amber-500/10 via-slate-50 to-slate-50">
        <!-- Background glow -->
        <div class="absolute inset-0 -z-10 flex items-center justify-center opacity-40">
            <div class="h-[500px] w-[500px] rounded-full bg-gradient-to-tr from-amber-400 to-amber-200 blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-500/10 px-4 py-1.5 text-xs font-bold text-amber-900 border border-amber-300/60 shadow-xs">
                    <x-icon name="sparkles" class="w-4 h-4 text-amber-600" />
                    <span>Layanan Pindahan, Storage & Logistik #1 Kota Malang</span>
                </div>
                
                <h1 class="text-4xl font-black tracking-tight text-slate-900 sm:text-6xl lg:text-7xl">
                    Pindahan & Titip Barang Jadi <span class="text-amber-600">Beres & Praktis</span>
                </h1>
                
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                    Platform all-in-one jasa angkut barang kost/rumah, penyimpanan ber-QR Code anti-rusak, dan pengiriman se-Malang Raya dengan transparansi tarif penuh.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <a href="#booking" class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 hover:bg-amber-400 px-8 py-4 text-sm font-black text-slate-950 shadow-xl shadow-amber-500/25 active:scale-98 transition-all cursor-pointer">
                        <span>Mulai Pesan Sekarang</span>
                        <x-icon name="arrow-right" class="w-5 h-5 text-slate-950" />
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="rounded-2xl bg-white border border-slate-300 px-7 py-4 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-xs flex items-center gap-2">
                        <x-icon name="chat" class="w-5 h-5 text-emerald-600" />
                        <span>Konsultasi WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- System Foundation Check -->
            <div class="mt-12 max-w-xl mx-auto">
                <livewire:public.system-status />
            </div>
        </div>
    </div>

    <!-- 1. Problem Statement Section -->
    <section class="py-16 bg-white border-y border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-600 mb-2">Masalah Yang Sering Terjadi</h2>
                <p class="text-2xl font-black text-slate-900 sm:text-3xl">Pernah Mengalami Hal Ini Saat Pindahan / Titip Barang?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-100 space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <x-icon name="truck" class="w-6 h-6 text-rose-600" />
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Capek Angkat & Armada Tidak Pasti</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Harus cari mobil pick-up manual di pinggir jalan, harga berubah-ubah tanpa kesepakatan tertulis, dan tenaga angkut tidak terlatih.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-100 space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <x-icon name="box" class="w-6 h-6 text-rose-600" />
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Barang Rusak atau Tertukar</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Penitipan barang konvensional menumpuk kardus di lantai lembap tanpa label identitas jelas, rentan basah dan tercecer.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-100 space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <x-icon name="alert-triangle" class="w-6 h-6 text-rose-600" />
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Status Pengiriman Buta</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tidak tahu posisi kurir/driver, kapan barang dijemput, atau bukti serah terima yang tidak tercatat secara sistematis.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Core Services Section -->
    <section id="services" class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                    <x-icon name="sparkles" class="w-4 h-4 text-amber-600" />
                    <span>Layanan Utama BawaBeres</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 sm:text-4xl">Solusi Lengkap untuk Malang Raya</h2>
                <p class="text-xs sm:text-sm text-slate-600">Semua proses tercatat digital dengan standar penanganan barang terbaik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="h-14 w-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                            <x-icon name="truck" class="w-7 h-7 text-amber-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Jasa Pindahan Kost & Rumah</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Layanan angkut lengkap dengan driver, armada terawat, dan tenaga angkut ramah untuk pindahan kost, kontrakan, hingga kantor se-Malang Raya.
                        </p>
                    </div>
                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <a href="{{ route('public.services') }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-700">
                            <span>Pelajari Selengkapnya</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 ml-1 text-amber-600" />
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="h-14 w-14 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                            <x-icon name="warehouse" class="w-7 h-7 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Storage & Penitipan Barang</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Gudang penyimpanan aman ber-CCTV dan ber-rak untuk mahasiswa libur semester atau renovasi rumah dengan label QR inventaris.
                        </p>
                    </div>
                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <a href="{{ route('public.services') }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-700">
                            <span>Pelajari Selengkapnya</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 ml-1 text-amber-600" />
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="h-14 w-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <x-icon name="truck" class="w-7 h-7 text-emerald-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Delivery & Logistik Instan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pengiriman kargo dan perabot cepat dari pintu ke pintu di area Kota Malang, Kota Batu, dan Kabupaten Malang.
                        </p>
                    </div>
                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <a href="{{ route('public.services') }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-700">
                            <span>Pelajari Selengkapnya</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 ml-1 text-amber-600" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. How It Works Section -->
    <section class="py-20 bg-white border-y border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                    <x-icon name="refresh" class="w-4 h-4 text-amber-600" />
                    <span>Alur Pemesanan</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 sm:text-4xl">Cara Kerja yang Super Simpel</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">1</span>
                    <h3 class="font-bold text-slate-900 text-sm">Pesan Online</h3>
                    <p class="text-xs text-slate-600">Isi formulir booking tanpa wajib registrasi akun.</p>
                </div>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">2</span>
                    <h3 class="font-bold text-slate-900 text-sm">Terima Penawaran</h3>
                    <p class="text-xs text-slate-600">Dapatkan rincian estimasi biaya resmi via WhatsApp.</p>
                </div>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">3</span>
                    <h3 class="font-bold text-slate-900 text-sm">Jemput & Ber-QR</h3>
                    <p class="text-xs text-slate-600">Barang dijemput armada dan dipasang stiker QR code.</p>
                </div>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">4</span>
                    <h3 class="font-bold text-slate-900 text-sm">Lacak & Selesai</h3>
                    <p class="text-xs text-slate-600">Lacak perkembangan status barang secara realtime.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Why Choose Us Section -->
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                    <x-icon name="shield-check" class="w-4 h-4 text-emerald-600" />
                    <span>Mengapa BawaBeres?</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 sm:text-4xl">Keunggulan Standar Layanan Kami</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <x-icon name="qr" class="w-6 h-6 text-purple-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">QR Code Anti Tertukar</h4>
                    <p class="text-xs text-slate-600">Setiap barang memiliki kode unik dan foto dokumentasi serah terima.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-icon name="tag" class="w-6 h-6 text-amber-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Harga Transparan</h4>
                    <p class="text-xs text-slate-600">Semua estimasi harga disetujui di awal tanpa biaya siluman.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-icon name="cctv" class="w-6 h-6 text-emerald-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Gudang CCTV 24 Jam</h4>
                    <p class="text-xs text-slate-600">Fasilitas penyimpanan bersih, bebas banjir, dan terpantau terus-menerus.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-icon name="map-pin" class="w-6 h-6 text-blue-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Tracking Realtime</h4>
                    <p class="text-xs text-slate-600">Cek status pesanan secara mandiri kapan saja tanpa login.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Interactive Public Booking Section -->
    <section class="py-20 bg-gradient-to-b from-slate-50 to-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:public.booking-form />
        </div>
    </section>

    <!-- 6. FAQ Section -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-2">
                <h2 class="text-2xl font-black text-slate-900 sm:text-3xl">Pertanyaan Umum (FAQ)</h2>
                <p class="text-xs sm:text-sm text-slate-500">Hal-hal yang sering ditanyakan pelanggan kami.</p>
            </div>

            <div class="space-y-3">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-xs">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Apakah perlu membuat akun untuk booking?</h4>
                    <p class="text-slate-600">Tidak perlu. Anda cukup memasukkan nomor WhatsApp untuk menerima penawaran dan memantau status pesanan.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-xs">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Berapa tarif penitipan barang storage?</h4>
                    <p class="text-slate-600">Tarif dihitung per item atau paket kardus/motor dengan durasi harian, mingguan, maupun bulanan yang sangat fleksibel.</p>
                </div>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('public.faq') }}" class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-700">
                    <span>Lihat Semua FAQ</span>
                    <x-icon name="arrow-right" class="w-3.5 h-3.5 ml-1 text-amber-600" />
                </a>
            </div>
        </div>
    </section>

    <!-- 7. Final High-Converting CTA Banner -->
    <section class="py-16 bg-slate-900 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-3xl font-black sm:text-4xl">Siap Pindahan atau Titip Barang Hari Ini?</h2>
            <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto">
                Dapatkan estimasi penawaran cepat dari tim kami dalam hitungan menit.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="#booking" class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 hover:bg-amber-400 px-8 py-4 text-xs font-black text-slate-950 shadow-lg shadow-amber-500/20 transition cursor-pointer">
                    <span>Buat Pesanan Sekarang</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-950" />
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-8 py-4 text-xs font-bold text-white shadow-lg shadow-emerald-600/20 transition cursor-pointer flex items-center gap-2">
                    <x-icon name="chat" class="w-4 h-4 text-white" />
                    <span>Chat WhatsApp</span>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

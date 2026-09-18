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
                <h1 class="text-4xl font-black tracking-tight text-slate-900 sm:text-6xl lg:text-7xl">
                    Pindahan Kost & Titip Barang Jadi <span class="text-amber-600">Beres & Praktis</span>
                </h1>
                
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                    Jasa pindahan kost, kontrakan, dan angkut barang se-Malang Raya. Lengkap dengan armada pick-up terawat, supir berpengalaman, dan bantuan tenaga angkut dengan tarif transparan.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <a href="#booking" class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 hover:bg-amber-400 px-8 py-4 text-sm font-black text-slate-950 shadow-xl shadow-amber-500/25 active:scale-98 transition-all cursor-pointer">
                        <span>Mulai Pesan Sekarang</span>
                        <x-icon name="arrow-right" class="w-5 h-5 text-slate-950" />
                    </a>
                    <a href="{{ \App\Support\BusinessProfile::whatsappUrl('Halo Admin BawaBeres, saya ingin konsultasi jasa pindahan') }}" target="_blank" class="rounded-2xl bg-white border border-slate-300 px-7 py-4 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-xs flex items-center gap-2">
                        <x-icon name="chat" class="w-5 h-5 text-emerald-600" />
                        <span>Konsultasi WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Problem Statement Section -->
    <section class="py-16 bg-white border-y border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-600 mb-2">Masalah Yang Sering Terjadi</h2>
                <p class="text-2xl font-black text-slate-900 sm:text-3xl">Pernah Mengalami Hal Ini Saat Mau Pindahan Kost?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-100 space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <x-icon name="truck" class="w-6 h-6 text-rose-600" />
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Capek Angkat Sendiri & Armada Tidak Pasti</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Harus cari mobil pick-up manual di pinggir jalan, harga berubah-ubah tanpa kepastian, dan harus angkat kasur serta lemari sendiri.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-100 space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <x-icon name="box" class="w-6 h-6 text-rose-600" />
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Khawatir Barang Rusak atau Lecet</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Barang ditumpuk sembarangan tanpa diikat aman, rawan terjatuh di jalan atau tergores saat proses bongkar muat.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-100 space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <x-icon name="alert-triangle" class="w-6 h-6 text-rose-600" />
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Biaya Tidak Jelas & Sering Nembak</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Harga awal tampak murah tapi mendadak ada biaya tambahan uang rokok, bensin, atau pungutan tak terduga di tempat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Core Services Section -->
    <section id="services" class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h2 class="text-3xl font-black text-slate-900 sm:text-4xl">Solusi Lengkap untuk Malang Raya</h2>
                <p class="text-xs sm:text-sm text-slate-600">Layanan pindahan dan pengiriman barang terpercaya untuk mahasiswa dan warga Malang Raya.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="rounded-3xl bg-white p-8 border border-slate-200 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="h-14 w-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                            <x-icon name="truck" class="w-7 h-7 text-amber-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Jasa Pindahan Kost & Rumah</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pindahan kost, kontrakan, hingga rumah lebih santai. Armada pick-up bersih, supir berpengalaman, dan siap bantu angkat kasur, lemari, hingga kardus pakaian.
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
                        <h3 class="text-xl font-black text-slate-900">Penitipan Barang Libur Kuliah</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Solusi hemat untuk mahasiswa saat libur semester. Titip kardus baju, kasur, atau motor di tempat aman dan bersih agar tidak boncos bayar sewa kost kosong.
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
                            <x-icon name="motorcycle" class="w-7 h-7 text-emerald-600" />
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Jasa Angkut & Kirim Barang</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Kirim perabot, kasur, lemari baru, atau barang bawaan besar dari pintu ke pintu di area Kota Malang, Kota Batu, dan Kabupaten Malang.
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
                <h2 class="text-3xl font-black text-slate-900 sm:text-4xl">Cara Kerja yang Super Simpel</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">1</span>
                    <h3 class="font-bold text-slate-900 text-sm">Pesan Mudah</h3>
                    <p class="text-xs text-slate-600">Isi formulir booking atau hubungi via WhatsApp tanpa ribet daftar akun.</p>
                </div>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">2</span>
                    <h3 class="font-bold text-slate-900 text-sm">Terima Penawaran</h3>
                    <p class="text-xs text-slate-600">Dapatkan rincian estimasi biaya resmi yang jelas dan disepakati di awal.</p>
                </div>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">3</span>
                    <h3 class="font-bold text-slate-900 text-sm">Jemput & Angkut Barang</h3>
                    <p class="text-xs text-slate-600">Tim dan armada datang tepat waktu ke lokasi dan bantu angkat barang Anda.</p>
                </div>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-black">4</span>
                    <h3 class="font-bold text-slate-900 text-sm">Antar Sampai Tujuan</h3>
                    <p class="text-xs text-slate-600">Barang sampai di tempat baru dengan aman dan dibantu tata kembali.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Why Choose Us Section -->
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h2 class="text-3xl font-black text-slate-900 sm:text-4xl">Keunggulan Standar Layanan Kami</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-icon name="users" class="w-6 h-6 text-blue-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Bantuan Tenaga Angkut</h4>
                    <p class="text-xs text-slate-600">Tidak perlu capek sendiri, tim kami siap bantu angkat kasur dan lemari hingga lantai kamar Anda.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-icon name="tag" class="w-6 h-6 text-amber-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Tarif Ramah Mahasiswa</h4>
                    <p class="text-xs text-slate-600">Harga bersahabat, transparan, dan disepakati di awal tanpa biaya siluman di lokasi.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-icon name="shield-check" class="w-6 h-6 text-emerald-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Barang Dijaga Hati-Hati</h4>
                    <p class="text-xs text-slate-600">Barang disusun rapi dan diikat aman di mobil agar tidak lecet atau rusak selama perjalanan.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <x-icon name="truck" class="w-6 h-6 text-purple-600" />
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Armada Bersih & Tepat Waktu</h4>
                    <p class="text-xs text-slate-600">Armada pick-up dan box terawat, siap jemput sesuai jadwal janji temu yang Anda tentukan.</p>
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
                <p class="text-xs sm:text-sm text-slate-500">Hal-hal yang sering ditanyakan seputar layanan pindahan kami.</p>
            </div>

            <div class="space-y-3">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-xs">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Apakah tim BawaBeres membantu angkut barang sampai ke kamar?</h4>
                    <p class="text-slate-600">Ya, tentu! Tim kami siap membantu proses angkut dari dalam kamar kost/rumah lama hingga dinaikkan ke armada dan diletakkan di lokasi baru.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-xs">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Berapa tarif jasa pindahan kost di Malang?</h4>
                    <p class="text-slate-600">Tarif dihitung bersahabat berdasarkan jarak tempuh dan kebutuhan tenaga angkut. Anda bisa konsultasi langsung via WhatsApp untuk kepastian total biaya.</p>
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
</div>
@endsection

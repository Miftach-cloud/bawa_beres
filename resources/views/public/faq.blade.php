@php
    $title = 'Tanya Jawab FAQ — BawaBeres';
@endphp
@extends('layouts.public')

@push('schema')
@php
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'Apakah saya wajib membuat akun untuk memesan?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Tidak. Sistem BawaBeres dirancang tanpa hambatan. Anda cukup mengisi nama dan nomor WhatsApp aktif.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Bagaimana cara menghitung biaya pindahan?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Biaya dihitung transparan berdasarkan jenis layanan, perkiraan volume muatan armada, jarak tempuh rute, dan bantuan tenaga angkut.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Bagaimana keamanan barang yang dititipkan di storage?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Gudang penyimpanan kami berlokasi aman, bebas banjir, bersih, dan kering. Setiap barang dicatat rapi dan diberi label identitas khusus agar tidak ada barang yang tertinggal atau tertukar.',
                ],
            ],
        ],
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Beranda',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'FAQ',
                'item' => route('public.faq'),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')

<div class="py-16 sm:py-24 bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-4">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Pertanyaan yang Sering Diajukan
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Temukan jawaban lengkap seputar layanan pindahan, penitipan barang, tarif, dan keamanan di BawaBeres.
            </p>
        </div>

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
                    Gudang penyimpanan kami berlokasi aman, bebas banjir, bersih, dan kering. Setiap barang dicatat rapi dan diberi label identitas khusus agar tidak ada barang yang tertinggal atau tertukar.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Berapa lama batas minimal sewa storage?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Kami melayani durasi sewa harian, mingguan (sangat cocok untuk libur semester mahasiswa), hingga bulanan dan tahunan.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-6 sm:p-8 border border-slate-200 shadow-sm space-y-2">
                <h3 class="text-base font-extrabold text-slate-900">Bagaimana cara mengecek update status pesanan saya?</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Anda dapat langsung menanyakan update penjemputan atau pengantaran barang ke tim admin BawaBeres via WhatsApp resmi dengan menyebutkan nomor order atau nama pemesan Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

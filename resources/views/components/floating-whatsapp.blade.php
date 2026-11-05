@props([
    'defaultMessage' => 'Halo Admin BawaBeres, saya ingin tanya seputar layanan pindahan & penitipan barang di Malang',
])

@php
    $phone = \App\Support\BusinessProfile::displayPhone();
    $mainUrl = \App\Support\BusinessProfile::whatsappUrl($defaultMessage);
    $operatingHours = config('business.operating_hours.display', 'Senin – Minggu: 07.00 – 21.00 WIB');
    $brandName = config('business.name', 'Bawa Beres');

    $quickOptions = [
        [
            'icon' => 'truck',
            'title' => 'Pindahan Kost / Rumah',
            'desc' => 'Cek estimasi ongkir & bantuan angkut',
            'url' => \App\Support\BusinessProfile::whatsappUrl('Halo Admin BawaBeres, saya mau tanya estimasi biaya pindahan kos/rumah di Malang'),
        ],
        [
            'icon' => 'warehouse',
            'title' => 'Penitipan Barang (Storage)',
            'desc' => 'Simpan aman harian, bulanan & semester',
            'url' => \App\Support\BusinessProfile::whatsappUrl('Halo Admin BawaBeres, saya mau tanya layanan penitipan barang / storage di Malang'),
        ],
        [
            'icon' => 'box',
            'title' => 'Sewa Pick Up & Driver',
            'desc' => 'Armada siap jalan se-Malang Raya',
            'url' => \App\Support\BusinessProfile::whatsappUrl('Halo Admin BawaBeres, saya mau tanya ketersediaan sewa armada pick up & driver'),
        ],
        [
            'icon' => 'message',
            'title' => 'Konsultasi Bebas / CS',
            'desc' => 'Tanya hal lain seputar layanan kami',
            'url' => \App\Support\BusinessProfile::whatsappUrl('Halo Admin BawaBeres, saya ingin konsultasi langsung dengan CS'),
        ],
    ];
@endphp

<!-- Modern Floating WhatsApp Corner Widget -->
<aside 
    x-data="{
        open: false,
        showTeaser: true,
        dismissTeaser() {
            this.showTeaser = false;
            try { sessionStorage.setItem('bb_wa_teaser_dismissed', '1'); } catch(e) {}
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.showTeaser = false;
            }
        },
        init() {
            try {
                if (sessionStorage.getItem('bb_wa_teaser_dismissed') === '1') {
                    this.showTeaser = false;
                }
            } catch(e) {}
        }
    }"
    class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-50 flex flex-col items-end pointer-events-none select-none"
    role="complementary"
    aria-label="WhatsApp Floating Customer Support"
>
    <!-- Proactive Teaser Speech Bubble -->
    <div 
        x-show="showTeaser && !open"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="pointer-events-auto relative mb-3 max-w-[280px] sm:max-w-xs rounded-2xl bg-white/95 backdrop-blur-md p-3.5 shadow-xl shadow-slate-900/10 border border-slate-200/90 text-slate-800 transition-all duration-200 hover:shadow-2xl hover:border-emerald-300 group cursor-pointer"
        @click="toggle()"
    >
        <div class="flex items-start gap-3">
            <div class="relative shrink-0 mt-0.5">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shadow-inner">
                    <svg class="w-4 h-4 fill-emerald-600" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.08L2 22l5.06-1.33A9.95 9.95 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm5.57 14.15c-.23.65-1.16 1.25-1.92 1.39-.52.1-1.2.17-3.47-.77-2.91-1.21-4.78-4.17-4.92-4.36-.14-.19-1.18-1.57-1.18-2.99 0-1.42.74-2.12 1-2.41.27-.29.59-.36.79-.36.2 0 .4 0 .57.01.19.01.44-.07.69.53.26.63.89 2.17.97 2.33.08.16.14.35.03.56-.11.22-.16.35-.32.54-.16.19-.34.42-.48.56-.16.16-.33.33-.14.66.19.33.85 1.4 1.82 2.27 1.25 1.11 2.3 1.45 2.63 1.61.33.16.52.14.71-.08.19-.22.82-.95 1.04-1.28.22-.33.44-.27.74-.16.3.11 1.91.9 2.24 1.06.33.16.55.25.63.38.08.14.08.8-.15 1.45Z"/>
                    </svg>
                </div>
                <span class="absolute -bottom-0.5 -right-0.5 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 border border-white"></span>
                </span>
            </div>
            <div class="flex-1 min-w-0 pr-4">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-bold text-slate-900 leading-tight">Admin BawaBeres</p>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">
                    Butuh info pindahan atau cek ongkir cepat? Chat kami yuk! 👋
                </p>
            </div>
            <!-- Dismiss button -->
            <button 
                type="button" 
                @click.stop="dismissTeaser()" 
                class="absolute top-2.5 right-2.5 text-slate-400 hover:text-slate-600 p-1 rounded-full hover:bg-slate-100 transition-colors cursor-pointer"
                aria-label="Tutup pesan bantuan"
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Pointer beak pointing to launcher -->
        <div class="absolute -bottom-1.5 right-8 w-3 h-3 bg-white rotate-45 border-r border-b border-slate-200/90 shadow-xs"></div>
    </div>

    <!-- Interactive Chat Popover Card -->
    <div 
        x-show="open"
        x-cloak
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="pointer-events-auto mb-3 w-[calc(100vw-2.5rem)] sm:w-[380px] max-w-[380px] rounded-3xl bg-white shadow-2xl shadow-emerald-950/20 border border-slate-200/90 overflow-hidden flex flex-col font-sans origin-bottom-right"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-700 p-4 text-white flex items-center justify-between relative shadow-xs">
            <div class="flex items-center gap-3">
                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner">
                        <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.08L2 22l5.06-1.33A9.95 9.95 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm5.57 14.15c-.23.65-1.16 1.25-1.92 1.39-.52.1-1.2.17-3.47-.77-2.91-1.21-4.78-4.17-4.92-4.36-.14-.19-1.18-1.57-1.18-2.99 0-1.42.74-2.12 1-2.41.27-.29.59-.36.79-.36.2 0 .4 0 .57.01.19.01.44-.07.69.53.26.63.89 2.17.97 2.33.08.16.14.35.03.56-.11.22-.16.35-.32.54-.16.19-.34.42-.48.56-.16.16-.33.33-.14.66.19.33.85 1.4 1.82 2.27 1.25 1.11 2.3 1.45 2.63 1.61.33.16.52.14.71-.08.19-.22.82-.95 1.04-1.28.22-.33.44-.27.74-.16.3.11 1.91.9 2.24 1.06.33.16.55.25.63.38.08.14.08.8-.15 1.45Z"/>
                        </svg>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400 border-2 border-emerald-700"></span>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-sm tracking-tight text-white leading-tight">Admin {{ $brandName }}</h4>
                        <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full bg-white/20 text-white text-[9px] font-bold" title="Akun Resmi Terverifikasi">✓</span>
                    </div>
                    <p class="text-[11px] text-emerald-100 flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 inline-block animate-pulse"></span>
                        <span>Online • Balas Cepat</span>
                    </p>
                </div>
            </div>
            <!-- Close button -->
            <button 
                @click="open = false" 
                type="button" 
                class="w-8 h-8 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/20 transition-all cursor-pointer"
                aria-label="Tutup jendela chat"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body: simulated conversation -->
        <div class="p-4 bg-slate-50/90 space-y-3.5 max-h-[360px] overflow-y-auto">
            <div class="flex justify-center">
                <span class="text-[10px] font-semibold tracking-wider uppercase text-slate-600 bg-white px-2.5 py-0.5 rounded-full shadow-2xs border border-slate-200/80">HARI INI</span>
            </div>

            <!-- Welcome Bubble -->
            <div class="flex flex-col items-start gap-1">
                <div class="bg-white rounded-2xl rounded-tl-xs p-3.5 shadow-xs border border-slate-200/70 text-slate-800 text-xs sm:text-[13px] leading-relaxed max-w-[94%]">
                    <p class="font-medium text-slate-900">Halo! Selamat datang di <span class="font-bold text-emerald-700">{{ $brandName }}</span> 🚚✨</p>
                    <p class="mt-1 text-slate-600">Ada kebutuhan pindahan kos, kirim barang, atau sewa storage di Malang? Pilih topik cepat di bawah untuk langsung terhubung ke WhatsApp:</p>
                    <div class="mt-2 flex items-center justify-end gap-1 text-[10px] text-slate-600">
                        <span>{{ now()->format('H:i') }}</span>
                        <svg class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 6L7 17l-5-5"/>
                            <path d="M22 10l-7.5 7.5L13 16"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Quick Option Shortcuts -->
            <div class="space-y-2 pt-1">
                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-wider px-1">Topik Konsultasi Cepat:</p>
                @foreach ($quickOptions as $option)
                    <a 
                        href="{{ $option['url'] }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="flex items-center justify-between gap-3 p-2.5 rounded-xl bg-white hover:bg-emerald-50/90 border border-slate-200/80 hover:border-emerald-300 text-slate-800 transition-all duration-150 shadow-2xs group cursor-pointer"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center shrink-0 transition-colors">
                                <x-icon :name="$option['icon']" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-xs font-bold text-slate-800 group-hover:text-emerald-900 truncate leading-tight">{{ $option['title'] }}</h5>
                                <p class="text-[10px] text-slate-600 truncate mt-0.5">{{ $option['desc'] }}</p>
                            </div>
                        </div>
                        <x-icon name="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all shrink-0" />
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Footer -->
        <div class="p-3.5 bg-white border-t border-slate-100 space-y-2.5">
            <a 
                href="{{ $mainUrl }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/25 hover:shadow-lg hover:shadow-emerald-600/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 group cursor-pointer"
            >
                <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.08L2 22l5.06-1.33A9.95 9.95 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm5.57 14.15c-.23.65-1.16 1.25-1.92 1.39-.52.1-1.2.17-3.47-.77-2.91-1.21-4.78-4.17-4.92-4.36-.14-.19-1.18-1.57-1.18-2.99 0-1.42.74-2.12 1-2.41.27-.29.59-.36.79-.36.2 0 .4 0 .57.01.19.01.44-.07.69.53.26.63.89 2.17.97 2.33.08.16.14.35.03.56-.11.22-.16.35-.32.54-.16.19-.34.42-.48.56-.16.16-.33.33-.14.66.19.33.85 1.4 1.82 2.27 1.25 1.11 2.3 1.45 2.63 1.61.33.16.52.14.71-.08.19-.22.82-.95 1.04-1.28.22-.33.44-.27.74-.16.3.11 1.91.9 2.24 1.06.33.16.55.25.63.38.08.14.08.8-.15 1.45Z"/>
                </svg>
                <span>Mulai Chat di WhatsApp</span>
                <svg class="w-3.5 h-3.5 text-emerald-100 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>

            <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-600 font-medium">
                <span>⚡ Jam Operasional: {{ $operatingHours }}</span>
            </div>
        </div>
    </div>

    <!-- Main Floating Trigger Button -->
    <button 
        @click="toggle()"
        type="button"
        :aria-expanded="open.toString()"
        aria-label="Buka Chat WhatsApp Resmi BawaBeres"
        class="pointer-events-auto relative focus:outline-none focus:ring-4 focus:ring-emerald-400/30 rounded-full transition-transform active:scale-95 cursor-pointer"
    >
        <!-- Closed State Capsule -->
        <div 
            x-show="!open"
            class="flex items-center gap-2.5 sm:gap-3 pl-2.5 pr-4 sm:pr-5 py-2 sm:py-2.5 rounded-full bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-700 text-white shadow-xl shadow-emerald-950/25 hover:shadow-2xl hover:shadow-emerald-600/40 border border-white/25 hover:scale-105 transition-all duration-300 group"
        >
            <!-- WhatsApp Icon Circle with Online Radar -->
            <div class="relative flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white text-emerald-600 shadow-md group-hover:scale-105 transition-transform shrink-0">
                <svg class="w-6 h-6 fill-current text-emerald-600" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.08L2 22l5.06-1.33A9.95 9.95 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm5.57 14.15c-.23.65-1.16 1.25-1.92 1.39-.52.1-1.2.17-3.47-.77-2.91-1.21-4.78-4.17-4.92-4.36-.14-.19-1.18-1.57-1.18-2.99 0-1.42.74-2.12 1-2.41.27-.29.59-.36.79-.36.2 0 .4 0 .57.01.19.01.44-.07.69.53.26.63.89 2.17.97 2.33.08.16.14.35.03.56-.11.22-.16.35-.32.54-.16.19-.34.42-.48.56-.16.16-.33.33-.14.66.19.33.85 1.4 1.82 2.27 1.25 1.11 2.3 1.45 2.63 1.61.33.16.52.14.71-.08.19-.22.82-.95 1.04-1.28.22-.33.44-.27.74-.16.3.11 1.91.9 2.24 1.06.33.16.55.25.63.38.08.14.08.8-.15 1.45Z"/>
                </svg>
                <!-- Radar Pulsing Ring -->
                <span class="absolute -top-0.5 -right-0.5 flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white"></span>
                </span>
            </div>

            <!-- Text Labels -->
            <div class="flex flex-col text-left">
                <div class="flex items-center gap-1.5">
                    <span class="font-bold text-xs sm:text-sm tracking-tight text-white leading-tight">Chat WhatsApp</span>
                    <span class="hidden sm:inline-block w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-medium text-emerald-100 leading-tight">Online • Balas Cepat</span>
            </div>

            <!-- Notification Pill Badge (1) -->
            <span 
                x-show="showTeaser"
                x-transition
                class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold flex items-center justify-center shadow-md border-2 border-white animate-bounce"
            >
                1
            </span>
        </div>

        <!-- Open State (Close Capsule) -->
        <div 
            x-show="open"
            x-cloak
            class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-900 text-white shadow-xl border border-slate-700/80 hover:bg-slate-800 hover:scale-105 transition-all duration-200 group"
        >
            <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-slate-300 group-hover:text-white">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <span class="font-bold text-xs tracking-wide">Tutup Chat</span>
        </div>
    </button>
</aside>

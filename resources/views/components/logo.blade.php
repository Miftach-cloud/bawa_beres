@props([
    'variant' => 'full', // 'full', 'mark', 'horizontal', 'vertical'
    'theme' => 'dark', // 'dark' (for dark background), 'light' (for white background), 'auto'
    'size' => 'md', // 'xs', 'sm', 'md', 'lg', 'xl', '2xl'
    'showTagline' => false,
])

@php
    $markSizes = [
        'xs' => 'w-7 h-5',
        'sm' => 'w-9 h-6',
        'md' => 'w-11 h-7',
        'lg' => 'w-14 h-9',
        'xl' => 'w-18 h-12',
        '2xl' => 'w-24 h-16',
    ];

    $textSizes = [
        'xs' => 'text-sm',
        'sm' => 'text-base',
        'md' => 'text-lg',
        'lg' => 'text-xl',
        'xl' => 'text-2xl',
        '2xl' => 'text-3xl',
    ];

    $taglineSizes = [
        'xs' => 'text-[8px]',
        'sm' => 'text-[9px]',
        'md' => 'text-[10px]',
        'lg' => 'text-xs',
        'xl' => 'text-xs',
        '2xl' => 'text-sm',
    ];

    $markClass = $markSizes[$size] ?? 'w-11 h-7';
    $textClass = $textSizes[$size] ?? 'text-lg';
    $taglineClass = $taglineSizes[$size] ?? 'text-[10px]';

    $textColor = match($theme) {
        'light' => 'text-slate-900',
        'dark' => 'text-white',
        default => 'text-slate-900 dark:text-white',
    };

    $taglineColor = match($theme) {
        'light' => 'text-slate-500',
        'dark' => 'text-slate-400',
        default => 'text-slate-500 dark:text-slate-400',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 selection:bg-amber-500']) }}>
    <!-- SVG Vector BB Monogram Truck & Map-Pin Logomark -->
    <div class="relative shrink-0 {{ $markClass }}">
        <svg viewBox="0 0 130 84" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-sm">
            <defs>
                <!-- Amber Gold to Deep Orange Gradient -->
                <linearGradient id="bb-rev-amber" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FBBF24" />
                    <stop offset="60%" stop-color="#F59E0B" />
                    <stop offset="100%" stop-color="#D97706" />
                </linearGradient>

                <!-- Deep Slate Navy for Cab/Body Contrast -->
                <linearGradient id="bb-rev-navy" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#1E293B" />
                    <stop offset="100%" stop-color="#0F172A" />
                </linearGradient>
            </defs>

            <!-- 1. First 'B' Letterform (Amber Gold Body) -->
            <path 
                d="M12 16 H36 C45 16 52 22 52 30 C52 35 48 40 43 42 C50 44 55 50 55 59 C55 69 46 76 35 76 H12 Z" 
                fill="url(#bb-rev-amber)" 
            />
            <!-- First B Top Cutout -->
            <path d="M22 26 H34 C38 26 41 28 41 32 C41 36 38 38 34 38 H22 Z" fill="#FFFFFF" opacity="{{ $theme === 'dark' ? '0.08' : '0.95' }}" />
            <!-- First B Bottom Cutout -->
            <path d="M22 48 H34 C39 48 43 51 43 56 C43 61 39 64 34 64 H22 Z" fill="#FFFFFF" opacity="{{ $theme === 'dark' ? '0.08' : '0.95' }}" />

            <!-- 2. Second 'B' Letterform / Truck Cargo Section -->
            <path 
                d="M52 16 H76 C85 16 92 22 92 30 C92 35 88 40 83 42 C90 44 95 50 95 59 C95 69 86 76 75 76 H52 Z" 
                fill="{{ $theme === 'dark' ? '#F8FAFC' : '#0F172A' }}" 
            />
            <!-- Second B Top Cutout -->
            <path d="M62 26 H74 C78 26 81 28 81 32 C81 36 78 38 74 38 H62 Z" fill="{{ $theme === 'dark' ? '#0F172A' : '#FFFFFF' }}" />
            <!-- Second B Bottom Cutout -->
            <path d="M62 48 H74 C79 48 83 51 83 56 C83 61 79 64 74 64 H62 Z" fill="{{ $theme === 'dark' ? '#0F172A' : '#FFFFFF' }}" />

            <!-- 3. Integrated Location Map-Pin Point (Between the BB Monograms) -->
            <path 
                d="M49 26 C45 26 42 29 42 33 C42 38 49 46 49 46 C49 46 56 38 56 33 C56 29 53 26 49 26 Z" 
                fill="url(#bb-rev-amber)" 
                stroke="{{ $theme === 'dark' ? '#0F172A' : '#FFFFFF' }}" 
                stroke-width="2"
            />
            <circle cx="49" cy="32" r="2.2" fill="{{ $theme === 'dark' ? '#0F172A' : '#FFFFFF' }}" />

            <!-- 4. Truck Front Cab & Windshield -->
            <path 
                d="M93 38 L108 38 L122 52 L122 72 L93 72 Z" 
                fill="{{ $theme === 'dark' ? '#F8FAFC' : '#0F172A' }}" 
            />
            <!-- Windshield Glass -->
            <path 
                d="M97 43 L107 43 L116 54 L97 54 Z" 
                fill="url(#bb-rev-amber)" 
            />

            <!-- 5. Wheels & Road Contact (Clean 2-Wheel Wheelbase) -->
            <!-- Front Wheel -->
            <circle cx="106" cy="74" r="9" fill="{{ $theme === 'dark' ? '#F8FAFC' : '#0F172A' }}" stroke="url(#bb-rev-amber)" stroke-width="3" />
            <circle cx="106" cy="74" r="3" fill="url(#bb-rev-amber)" />

            <!-- Rear Wheel -->
            <circle cx="28" cy="74" r="9" fill="{{ $theme === 'dark' ? '#F8FAFC' : '#0F172A' }}" stroke="url(#bb-rev-amber)" stroke-width="3" />
            <circle cx="28" cy="74" r="3" fill="url(#bb-rev-amber)" />
        </svg>
    </div>

    @if ($variant !== 'mark')
        <!-- Wordmark & Tagline -->
        <div class="flex flex-col">
            <span class="font-black tracking-tight {{ $textColor }} {{ $textClass }} leading-tight">
                Bawa<span class="text-amber-500">Beres</span>
            </span>
            @if ($showTagline)
                <span class="font-bold tracking-wider uppercase {{ $taglineColor }} {{ $taglineClass }}">
                    Logistik • Pindahan • Storage
                </span>
            @endif
        </div>
    @endif
</div>

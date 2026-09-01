<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-8 font-sans">
    <!-- Header Section -->
    <div class="text-center space-y-2 max-w-xl mx-auto">
        <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-200">
            <x-icon name="map-pin" class="w-3.5 h-3.5 text-amber-600" />
            <span>Realtime Order Tracking</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
            Lacak Status Pesanan Anda
        </h1>
        <p class="text-xs sm:text-sm text-slate-500">
            Cek progres pindahan, jadwal penjemputan armada, atau status penyimpanan barang fisik Anda secara realtime tanpa perlu login.
        </p>
    </div>

    <!-- Tracking Lookup Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl max-w-2xl mx-auto space-y-5">
        <form wire:submit="track" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Nomor Pesanan (Order Code) *</label>
                    <input 
                        type="text" 
                        wire:model="orderCode" 
                        placeholder="Contoh: ORD-2026-000051" 
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-xs font-mono font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                    >
                    @error('orderCode') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Nomor WhatsApp / HP Verifikasi *</label>
                    <input 
                        type="text" 
                        wire:model="phone" 
                        placeholder="Nomor HP saat memesan (e.g. 0812...)" 
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                    >
                    @error('phone') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="w-full rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-sm py-3.5 shadow-lg shadow-amber-500/20 transition cursor-pointer flex items-center justify-center gap-2"
            >
                <span wire:loading.remove class="inline-flex items-center gap-2">
                    <x-icon name="search" class="w-4 h-4 text-slate-950" />
                    <span>Lacak Pesanan Saya</span>
                </span>
                <span wire:loading class="inline-flex items-center gap-2">
                    <x-icon name="refresh" class="w-4 h-4 animate-spin text-slate-950" />
                    <span>Mencari Data Pesanan...</span>
                </span>
            </button>
        </form>

        @if ($errorMessage)
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2 animate-fade-in">
                <x-icon name="alert-circle" class="w-4 h-4 text-rose-600 shrink-0" />
                <span>{{ $errorMessage }}</span>
            </div>
        @endif
    </div>

    @if ($order)
        <!-- Order Tracking Detail Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden max-w-3xl mx-auto space-y-6 animate-fade-in">
            <!-- Order Header Banner -->
            <div class="bg-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-black text-amber-400 bg-amber-400/10 px-3 py-1 rounded-xl border border-amber-400/20">
                            {{ $order->order_code }}
                        </span>
                        <span class="text-xs text-slate-400">
                            Dibuat: {{ $order->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <h2 class="text-xl font-extrabold text-white mt-2">{{ $order->service->name }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pemesan: <strong class="text-slate-200">{{ Str::mask($order->customer->name, '*', 3) }}</strong>
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <span class="inline-flex items-center rounded-xl px-3 py-1.5 text-xs font-bold uppercase tracking-wider border {{ $order->status->badgeColor() }}">
                        {{ $order->status->label() }}
                    </span>
                    @if ($order->preferred_date)
                        <span class="block text-xs text-amber-300 font-semibold mt-1.5">
                            Target: {{ $order->preferred_date->translatedFormat('d F Y') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Milestone Progress Tracker Timeline -->
            <div class="p-6 sm:p-8 space-y-6">
                <h3 class="font-black text-base text-slate-900 tracking-tight">
                    Perjalanan Status Pesanan
                </h3>

                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach ($milestones as $index => $m)
                            <li class="relative pb-8">
                                @if (!$loop->last)
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 {{ $m['is_completed'] ? 'bg-amber-500' : 'bg-slate-200' }}" aria-hidden="true"></span>
                                @endif

                                <div class="relative flex items-start space-x-4">
                                    <div class="relative flex h-10 w-10 flex-none items-center justify-center rounded-2xl {{ $m['is_completed'] ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : ($m['is_active'] ? 'bg-amber-100 text-amber-800 ring-4 ring-amber-200 animate-pulse' : 'bg-slate-100 text-slate-400 border border-slate-200') }}">
                                        <x-icon :name="$m['icon']" class="w-5 h-5 {{ $m['is_completed'] ? 'text-slate-950' : ($m['is_active'] ? 'text-amber-700' : 'text-slate-400') }}" />
                                    </div>

                                    <div class="min-w-0 flex-1 pt-1.5">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-bold {{ $m['is_active'] ? 'text-amber-700' : ($m['is_completed'] ? 'text-slate-900' : 'text-slate-400') }}">
                                                {{ $m['title'] }}
                                            </h4>
                                            @if ($m['is_active'])
                                                <span class="rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold px-2.5 py-0.5">
                                                    Sedang Berlangsung
                                                </span>
                                            @elseif ($m['is_completed'])
                                                <span class="inline-flex items-center gap-1 text-emerald-600 text-xs font-bold">
                                                    <x-icon name="check" class="w-3.5 h-3.5 text-emerald-600" />
                                                    <span>Selesai</span>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs {{ $m['is_completed'] || $m['is_active'] ? 'text-slate-600' : 'text-slate-400' }} mt-1 leading-relaxed">
                                            {{ $m['description'] }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Public-Safe Order Detail Cards -->
            <div class="px-6 pb-8 sm:px-8 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Location Safe Info -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Rute Layanan</span>
                        <div class="space-y-1.5">
                            <div>
                                <span class="text-slate-500 font-semibold">Penjemputan:</span>
                                <p class="font-bold text-slate-900 mt-0.5">{{ $order->pickupAddress?->city ?: 'Kota Malang' }} ({{ $order->pickupAddress?->address }})</p>
                            </div>
                            @if ($order->destinationAddress)
                                <div>
                                    <span class="text-slate-500 font-semibold">Tujuan:</span>
                                    <p class="font-bold text-slate-900 mt-0.5">{{ $order->destinationAddress->city }} ({{ $order->destinationAddress->address }})</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Declared Items Safe Info -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Daftar Barang ({{ $order->items->count() }})</span>
                        <ul class="divide-y divide-slate-200/60 max-h-28 overflow-y-auto">
                            @foreach ($order->items as $it)
                                <li class="py-1 flex items-center justify-between">
                                    <span class="font-medium text-slate-800">{{ $it->name }}</span>
                                    <span class="font-bold text-slate-600">x{{ $it->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- WhatsApp Support CTA -->
                @php
                    $waHelpMessage = rawurlencode("Halo Admin BawaBeres, saya ingin menanyakan perkembangan pesanan saya dengan nomor {$order->order_code}.");
                @endphp
                <div class="pt-2">
                    <a 
                        href="https://wa.me/6281234567890?text={{ $waHelpMessage }}" 
                        target="_blank"
                        class="w-full flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-6 py-3.5 text-xs font-bold text-white shadow-md shadow-emerald-600/10 transition cursor-pointer"
                    >
                        <x-icon name="chat" class="w-4 h-4 text-white" />
                        <span>Tanya Admin via WhatsApp (Ref: {{ $order->order_code }})</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

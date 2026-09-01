<div class="min-h-screen bg-slate-900 py-6 px-4 sm:px-6 lg:px-8 text-slate-100 font-sans">
    <div class="max-w-xl mx-auto space-y-5">
        <!-- Brand Header & Scanner Banner -->
        <div class="flex items-center justify-between bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-slate-700 shadow-xl">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg shadow-lg shadow-amber-500/20">
                    BB
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white tracking-wide uppercase">BawaBeres QR Scanner</h1>
                    <p class="text-[11px] text-slate-400">Sistem Pengenalan Fisik Barang Gudang</p>
                </div>
            </div>

            @if ($isInternalStaff)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold text-emerald-400 border border-emerald-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Petugas Aktif</span>
                </span>
            @else
                <a href="{{ route('admin.login') }}" class="rounded-xl bg-slate-700 hover:bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white transition">
                    Login Petugas
                </a>
            @endif
        </div>

        @if (session()->has('scan_message'))
            <div class="p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center justify-between">
                <span class="inline-flex items-center gap-2">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-400" />
                    <span>{{ session('scan_message') }}</span>
                </span>
            </div>
        @endif

        @if (!$item)
            <!-- Item Not Found Card -->
            <div class="bg-slate-800/60 rounded-3xl p-8 text-center border border-slate-700/80 shadow-2xl space-y-3">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-500 mx-auto">
                    <x-icon name="x-circle" class="w-8 h-8 text-rose-500" />
                </div>
                <h2 class="text-base font-bold text-white">Barang Fisik Tidak Ditemukan</h2>
                <p class="text-xs text-slate-400 max-w-xs mx-auto">
                    Kode QR <code class="font-mono text-amber-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-700">{{ $code }}</code> tidak terdaftar di sistem BawaBeres.
                </p>
                <div class="pt-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-400">
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Item Header Card -->
            <div class="bg-slate-800/90 rounded-3xl p-5 border border-slate-700/80 shadow-2xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/20">
                                {{ $item->inventory_code }}
                            </span>
                            <span class="font-mono text-[11px] text-slate-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-700">
                                #{{ $item->qr_code }}
                            </span>
                        </div>
                        <h2 class="text-lg font-bold text-white mt-1.5">{{ $item->name }}</h2>
                        <p class="text-xs text-slate-400">Kategori: {{ $item->category ?: 'Standar' }}</p>
                    </div>

                    <div class="text-right space-y-1">
                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider border {{ $item->status->badgeColor() }}">
                            {{ $item->status->label() }}
                        </span>
                        <span class="block text-[11px] font-semibold text-slate-300">
                            Kondisi: <strong class="text-amber-400">{{ $item->condition->label() }}</strong>
                        </span>
                    </div>
                </div>

                <!-- Location & Custody Box -->
                <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-900/80 rounded-2xl border border-slate-700/50 text-xs">
                    @if ($isInternalStaff)
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Posisi Rak Penyimpanan</span>
                            <span class="font-mono font-bold text-amber-400 text-sm mt-0.5 inline-flex items-center gap-1 truncate">
                                <x-icon name="map-pin" class="w-3.5 h-3.5 text-amber-400" />
                                <span>{{ $item->storage_location ?: 'Area Transit / Belum di Rak' }}</span>
                            </span>
                        </div>
                    @else
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Lokasi Fasilitas</span>
                            <span class="font-bold text-slate-200 text-xs mt-0.5 inline-flex items-center gap-1">
                                <x-icon name="map-pin" class="w-3.5 h-3.5 text-emerald-400" />
                                <span>Gudang Resmi BawaBeres</span>
                            </span>
                        </div>
                    @endif
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Status Keaslian</span>
                        <span class="font-semibold text-emerald-400 mt-0.5 inline-flex items-center gap-1">
                            <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-400" />
                            <span>Terverifikasi</span>
                        </span>
                    </div>
                </div>

                @if ($isInternalStaff)
                    <!-- Internal Operational Actions -->
                    <div class="pt-2 border-t border-slate-700 space-y-2">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Aksi Cepat Petugas:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @if ($item->status->value === 'EXPECTED')
                                <button type="button" wire:click="receive" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-cyan-600 px-3 py-2 text-xs font-bold text-white hover:bg-cyan-500 cursor-pointer shadow-sm">
                                    <x-icon name="download" class="w-3.5 h-3.5 text-white" />
                                    <span>Terima Fisik</span>
                                </button>
                            @endif

                            <button type="button" wire:click="openCheckModal" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-500 cursor-pointer shadow-sm">
                                <x-icon name="search" class="w-3.5 h-3.5 text-white" />
                                <span>QC & Cek</span>
                            </button>

                            @if ($item->status->value === 'CHECKED')
                                <button type="button" wire:click="openStoreModal" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-purple-600 px-3 py-2 text-xs font-bold text-white hover:bg-purple-500 cursor-pointer shadow-sm">
                                    <x-icon name="warehouse" class="w-3.5 h-3.5 text-white" />
                                    <span>Simpan Rak</span>
                                </button>
                            @elseif ($item->status->value === 'STORED')
                                <button type="button" wire:click="openRelocateModal" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-500 cursor-pointer shadow-sm">
                                    <x-icon name="refresh" class="w-3.5 h-3.5 text-white" />
                                    <span>Pindah Rak</span>
                                </button>
                            @endif

                            @if (in_array($item->status->value, ['STORED', 'OUTBOUND'], true))
                                <button type="button" wire:click="release" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-500 cursor-pointer shadow-sm">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-white" />
                                    <span>Serah Terima</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            @if ($isInternalStaff)
                <!-- Internal Order & Customer Details -->
                <div class="bg-slate-800/90 rounded-3xl p-5 border border-slate-700/80 shadow-2xl space-y-3 text-xs">
                    <h3 class="font-bold text-white text-sm flex items-center justify-between">
                        <span>Informasi Order & Customer</span>
                        <a href="{{ route('admin.orders.show', $item->order) }}" class="inline-flex items-center gap-1 text-xs text-amber-400 hover:text-amber-300">
                            <span>Buka Order #{{ $item->order->order_code }}</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 text-amber-400" />
                        </a>
                    </h3>

                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-900/60 rounded-xl border border-slate-700/50">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nama Customer</span>
                            <span class="font-bold text-slate-100 text-sm mt-0.5 block">{{ $item->order->customer->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ $item->order->customer->phone }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block">Status Order</span>
                            <span class="font-bold text-amber-400 mt-0.5 block">{{ $item->order->status->label() }}</span>
                            <span class="text-[10px] text-slate-500">{{ $item->order->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Photos Section -->
                @if ($item->photos->isNotEmpty())
                    <div class="bg-slate-800/90 rounded-3xl p-5 border border-slate-700/80 shadow-2xl space-y-3">
                        <h3 class="font-bold text-white text-sm">
                            Dokumentasi Foto Fisik ({{ $item->photos->count() }})
                        </h3>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($item->photos as $photo)
                                <div class="relative group rounded-xl overflow-hidden aspect-square bg-slate-950 border border-slate-700">
                                    <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 bg-slate-900/80 text-[9px] font-mono text-slate-300 px-1.5 py-0.5 rounded">
                                        {{ $photo->type->label() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Movement History Section -->
                <div class="bg-slate-800/90 rounded-3xl p-5 border border-slate-700/80 shadow-2xl space-y-3 text-xs">
                    <h3 class="font-bold text-white text-sm">
                        Histori Mutasi / Pergerakan ({{ $item->movements->count() }})
                    </h3>
                    <ul class="space-y-2">
                        @forelse ($item->movements as $m)
                            <li class="p-3 bg-slate-900/70 rounded-xl border border-slate-700/50 space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-200">{{ $m->movement_type->label() }}</span>
                                    <span class="font-mono text-[10px] text-slate-400">{{ $m->moved_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="font-mono font-bold text-amber-400 text-xs inline-flex items-center gap-1.5">
                                    <span>{{ $m->from_location_code ?: 'Receiving' }}</span>
                                    <x-icon name="arrow-right" class="w-3 h-3 text-amber-400" />
                                    <span>{{ $m->to_location_code ?: 'Outbound' }}</span>
                                </div>
                                @if ($m->notes)
                                    <p class="text-slate-400 italic text-[10px]">"{{ $m->notes }}"</p>
                                @endif
                            </li>
                        @empty
                            <li class="text-slate-500 italic py-2 text-center">Belum ada mutasi tercatat.</li>
                        @endforelse
                    </ul>
                </div>
            @else
                <!-- Public Visitor Authenticity Card -->
                <div class="bg-slate-800/60 rounded-3xl p-6 text-center border border-slate-700/80 shadow-2xl space-y-3 text-xs">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400 mx-auto">
                        <x-icon name="shield" class="w-8 h-8 text-amber-400" />
                    </div>
                    <h3 class="font-bold text-white text-sm">Segel Keaslian Fisik BawaBeres</h3>
                    <p class="text-slate-400 max-w-sm mx-auto text-[11px] leading-relaxed">
                        Barang fisik ini terdaftar dan tersimpan di jaringan pergudangan resmi BawaBeres. Untuk melihat informasi lengkap dan melakukan pergerakan barang, silakan login dengan akun staf operasional.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('admin.login') }}" class="inline-flex rounded-xl bg-amber-500 px-5 py-2.5 text-xs font-bold text-slate-950 hover:bg-amber-400 shadow-md">
                            Login Petugas Lapangan
                        </a>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Check Modal -->
    @if ($showCheckModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-slate-800 p-6 border border-slate-700 text-slate-100 space-y-4">
                <h3 class="font-bold text-white text-base">Pemeriksaan QC Kondisi Fisik</h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Kondisi Fisik</label>
                        <select wire:model="condition" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-slate-100">
                            @foreach ($conditions as $c)
                                <option value="{{ $c->value }}">{{ $c->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Catatan QC</label>
                        <textarea wire:model="checkNotes" rows="3" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-slate-100" placeholder="Kondisi barang saat scan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" wire:click="closeCheckModal" class="rounded-xl border border-slate-700 px-4 py-2 text-xs text-slate-300">Batal</button>
                    <button type="button" wire:click="confirmCheck" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white">Simpan QC</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Store Modal -->
    @if ($showStoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-slate-800 p-6 border border-slate-700 text-slate-100 space-y-4">
                <h3 class="font-bold text-white text-base">Alokasi Rak Gudang</h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Pilih Slot Rak *</label>
                        <select wire:model="selectedLocationId" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 font-mono text-slate-100">
                            <option value="">-- Pilih Master Slot Rak --</option>
                            @foreach ($availableLocations as $loc)
                                <option value="{{ $loc->id }}">
                                    {{ $loc->code }} ({{ $loc->warehouse }}) [Sisa: {{ $loc->remainingCapacity() }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" wire:click="closeStoreModal" class="rounded-xl border border-slate-700 px-4 py-2 text-xs text-slate-300">Batal</button>
                    <button type="button" wire:click="confirmStore" class="rounded-xl bg-purple-600 px-4 py-2 text-xs font-bold text-white">Simpan di Rak</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Relocate Modal -->
    @if ($showRelocateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-slate-800 p-6 border border-slate-700 text-slate-100 space-y-4">
                <h3 class="font-bold text-white text-base">Pindah Posisi Rak (Relocate)</h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Pilih Slot Rak Tujuan Baru *</label>
                        <select wire:model="relocateLocationId" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 font-mono text-slate-100">
                            <option value="">-- Pilih Slot Rak Tujuan --</option>
                            @foreach ($availableLocations as $loc)
                                <option value="{{ $loc->id }}">
                                    {{ $loc->code }} ({{ $loc->warehouse }}) [Sisa: {{ $loc->remainingCapacity() }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Catatan Pemindahan</label>
                        <textarea wire:model="relocateNotes" rows="2" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-slate-100" placeholder="Alasan pemindahan rak..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" wire:click="closeRelocateModal" class="rounded-xl border border-slate-700 px-4 py-2 text-xs text-slate-300">Batal</button>
                    <button type="button" wire:click="confirmRelocate" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white">Konfirmasi Pindah</button>
                </div>
            </div>
        </div>
    @endif
</div>

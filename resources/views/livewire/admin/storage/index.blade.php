<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Manajemen Gudang & Lokasi Rak Storage</h2>
            <p class="text-xs text-slate-500 mt-0.5">Struktur hierarki lokasi: Warehouse &rsaquo; Zone &rsaquo; Rack &rsaquo; Level (e.g. MLG01-A-R02-L03)</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <x-icon name="plus" class="w-4 h-4 text-slate-950" />
            <span>Tambah Slot Rak Baru</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <x-icon name="check-circle" class="w-5 h-5 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Occupancy Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Slot Rak</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_slots'] }}</div>
            <span class="text-[11px] text-slate-500">Kapasitas tercatat</span>
        </div>

        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 flex items-center gap-1">
                <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600" />
                <span>Slot Tersedia</span>
            </span>
            <div class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['available_slots'] }}</div>
            <span class="text-[11px] text-slate-500">Siap dialokasikan</span>
        </div>

        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 flex items-center gap-1">
                <x-icon name="warehouse" class="w-3.5 h-3.5 text-blue-600" />
                <span>Slot Terpakai / Penuh</span>
            </span>
            <div class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['occupied_slots'] }}</div>
            <span class="text-[11px] text-slate-500">Terisi barang</span>
        </div>

        <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 flex items-center gap-1">
                <x-icon name="box" class="w-3.5 h-3.5 text-purple-600" />
                <span>Barang Fisik Tersimpan</span>
            </span>
            <div class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['stored_items'] }}</div>
            <span class="text-[11px] text-slate-500">Dalam penguasaan gudang</span>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative sm:col-span-1">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari kode slot (MLG01-A-R01)..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                <span class="absolute left-3 top-2.5 text-xs text-slate-400">
                    <x-icon name="search" class="w-3.5 h-3.5 text-slate-400" />
                </span>
            </div>

            <!-- Warehouse Filter -->
            <div>
                <select 
                    wire:model.live="warehouseFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Warehouse (Gudang)</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh }}">{{ $wh }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Zone Filter -->
            <div>
                <select 
                    wire:model.live="zoneFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Zona Area</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z }}">{{ $z }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select 
                    wire:model.live="statusFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Status Slot</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 text-slate-500">
            <div>
                Menampilkan <span class="font-bold text-slate-900">{{ $locations->total() }}</span> lokasi slot rak gudang
            </div>
            @if ($search || $warehouseFilter || $zoneFilter || $statusFilter)
                <button 
                    type="button" 
                    wire:click="resetFilters" 
                    class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-700 font-medium cursor-pointer"
                >
                    <x-icon name="refresh" class="w-3 h-3 text-amber-600" />
                    <span>Reset Filter</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Storage Slots Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($locations as $loc)
            <div 
                wire:click="viewLocation({{ $loc->id }})"
                class="group rounded-2xl border {{ $loc->isFull() ? 'border-rose-200 bg-rose-50/10' : ($loc->occupiedCount() > 0 ? 'border-blue-200 bg-blue-50/10' : 'border-slate-200 bg-white') }} p-4 shadow-xs hover:shadow-md hover:border-amber-400 transition-all cursor-pointer flex flex-col justify-between space-y-3"
            >
                <div class="space-y-2">
                    <!-- Top Bar: Type Icon & Status Dot -->
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700">
                            <x-icon name="warehouse" class="w-3.5 h-3.5 text-slate-600" />
                            <span class="text-[11px] truncate max-w-[140px]">{{ $loc->type->label() }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold border {{ $loc->status->badgeColor() }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $loc->status->indicatorDot() }}"></span>
                            <span>{{ $loc->status->value }}</span>
                        </span>
                    </div>

                    <!-- Location Code Hierarchy -->
                    <div>
                        <div class="font-mono text-sm font-bold text-slate-900 group-hover:text-amber-600 transition-colors">
                            {{ $loc->code }}
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            {{ $loc->warehouse }} • {{ $loc->zone }}
                        </div>
                    </div>

                    <!-- Occupancy Progress Bar -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Kapasitas:</span>
                            <span class="font-bold {{ $loc->isFull() ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $loc->occupiedCount() }} / {{ $loc->capacity }} unit
                            </span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div 
                                class="h-full rounded-full {{ $loc->isFull() ? 'bg-rose-500' : ($loc->occupiedCount() > 0 ? 'bg-blue-500' : 'bg-emerald-500') }}"
                                style="width: {{ min(100, ($loc->occupiedCount() / max(1, $loc->capacity)) * 100) }}%"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Rack: <strong class="text-slate-700">{{ $loc->rack }}</strong> • Lvl: <strong class="text-slate-700">{{ $loc->level }}</strong></span>
                    <span class="inline-flex items-center gap-0.5 text-amber-600 font-bold group-hover:translate-x-0.5 transition-transform">
                        <span>Detail</span>
                        <x-icon name="arrow-right" class="w-3 h-3 text-amber-600" />
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl bg-white border border-slate-200 p-12 text-center text-slate-400 space-y-2">
                <x-icon name="warehouse" class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                <p class="text-sm font-semibold text-slate-700">Belum ada lokasi rak yang terdaftar sesuai filter.</p>
                <p class="text-xs text-slate-400">Klik "Tambah Slot Rak Baru" untuk mulai membuat struktur lokasi rak gudang.</p>
            </div>
        @endforelse
    </div>

    @if ($locations->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $locations->links() }}
        </div>
    @endif

    <!-- Create Slot Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4 my-8">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Tambah Slot Rak Gudang Baru
                    </h3>
                    <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <form wire:submit="saveLocation" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Warehouse (Gudang) *</label>
                            <input type="text" wire:model="warehouse" placeholder="Contoh: MLG01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 uppercase">
                            @error('warehouse') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Zona Area *</label>
                            <input type="text" wire:model="zone" placeholder="Contoh: A" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 uppercase">
                            @error('zone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Rak (Rack) *</label>
                            <input type="text" wire:model="rack" placeholder="Contoh: R01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 uppercase">
                            @error('rack') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tingkat Rak (Level) *</label>
                            <input type="text" wire:model="level" placeholder="Contoh: L01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 uppercase">
                            @error('level') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="bg-amber-50/70 p-3 rounded-xl border border-amber-200/70 text-amber-900 flex items-center justify-between">
                        <span class="text-[11px] font-semibold">Format Kode Otomatis:</span>
                        <span class="font-mono font-bold text-xs">
                            {{ strtoupper($warehouse) }}-{{ strtoupper($zone) }}-{{ strtoupper($rack) }}-{{ strtoupper($level) }}
                        </span>
                    </div>
                    @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tipe Slot Rak *</label>
                            <select wire:model="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                @foreach ($types as $t)
                                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kapasitas Maks Unit Barang *</label>
                            <input type="number" wire:model="capacity" min="1" max="100" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan Lokasi</label>
                        <textarea wire:model="notes" rows="2" placeholder="Dekat pintu masuk timur, bebas lembab..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeCreateModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 cursor-pointer">
                            Simpan Slot Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Slot Detail Drawer -->
    @if ($showDetailDrawer && $selectedLocation)
        <div class="fixed inset-0 z-50 flex justify-end bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg bg-white h-full shadow-2xl p-6 overflow-y-auto space-y-6 flex flex-col justify-between">
                <div class="space-y-4">
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-base font-bold text-slate-900">{{ $selectedLocation->code }}</span>
                                <span class="rounded px-2 py-0.5 text-[10px] font-bold border {{ $selectedLocation->status->badgeColor() }}">
                                    {{ $selectedLocation->status->value }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $selectedLocation->warehouse }} • {{ $selectedLocation->zone }} • Rack {{ $selectedLocation->rack }} Level {{ $selectedLocation->level }}
                            </p>
                        </div>

                        <button type="button" wire:click="closeDetailDrawer" class="text-slate-400 hover:text-slate-600 font-bold p-1 cursor-pointer">
                            <x-icon name="x" class="w-5 h-5 text-slate-500" />
                        </button>
                    </div>

                    <!-- Summary Info -->
                    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Tipe Lokasi</span>
                            <span class="font-semibold text-slate-800">{{ $selectedLocation->type->label() }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Kapasitas Slot</span>
                            <span class="font-semibold text-slate-800">{{ $selectedLocation->occupiedCount() }} / {{ $selectedLocation->capacity }} unit</span>
                        </div>
                    </div>

                    <!-- Stored Items in this Slot -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                            <x-icon name="box" class="w-4 h-4 text-amber-600" />
                            <span>Daftar Barang Fisik yang Tersimpan ({{ $selectedLocation->storedItems->count() }})</span>
                        </h4>

                        <div class="space-y-2">
                            @forelse ($selectedLocation->storedItems as $item)
                                <div class="rounded-xl border border-slate-200 p-3 bg-white space-y-2 shadow-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="font-mono text-xs font-bold text-amber-600">
                                            {{ $item->inventory_code }}
                                        </div>
                                        <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold border {{ $item->condition->badgeColor() }}">
                                            {{ $item->condition->label() }}
                                        </span>
                                    </div>

                                    <div class="font-bold text-xs text-slate-900">{{ $item->name }}</div>

                                    <div class="text-[11px] text-slate-500 flex items-center justify-between">
                                        <span>Order: <strong class="text-slate-700">{{ $item->order->order_code }}</strong></span>
                                        <span>Cust: <strong class="text-slate-700">{{ $item->order->customer->name }}</strong></span>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                                    <x-icon name="box" class="w-8 h-8 text-slate-300 mx-auto mb-1.5" />
                                    Slot rak ini saat ini kosong (belum ada barang tersimpan).
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="button" wire:click="closeDetailDrawer" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

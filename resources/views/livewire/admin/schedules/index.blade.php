<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Jadwal Operasional & Armada</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penugasan rute pickup, delivery, dan koordinasi tim lapangan</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <span>➕ Atur Jadwal Misi Baru</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <span class="text-base">✅</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Operational View Tabs (Section 22 - Today / Tomorrow / Upcoming / All) -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <button 
            type="button" 
            wire:click="setTab('today')"
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all cursor-pointer {{ $activeTab === 'today' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}"
        >
            <span>☀️ Hari Ini</span>
            <span class="rounded-full px-2 py-0.5 text-[10px] {{ $activeTab === 'today' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $counts['today'] }}
            </span>
        </button>

        <button 
            type="button" 
            wire:click="setTab('tomorrow')"
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all cursor-pointer {{ $activeTab === 'tomorrow' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}"
        >
            <span>🌅 Besok</span>
            <span class="rounded-full px-2 py-0.5 text-[10px] {{ $activeTab === 'tomorrow' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $counts['tomorrow'] }}
            </span>
        </button>

        <button 
            type="button" 
            wire:click="setTab('upcoming')"
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all cursor-pointer {{ $activeTab === 'upcoming' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}"
        >
            <span>🗓️ Mendatang</span>
            <span class="rounded-full px-2 py-0.5 text-[10px] {{ $activeTab === 'upcoming' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $counts['upcoming'] }}
            </span>
        </button>

        <button 
            type="button" 
            wire:click="setTab('all')"
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all cursor-pointer {{ $activeTab === 'all' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}"
        >
            <span>📋 Semua / Riwayat</span>
            <span class="rounded-full px-2 py-0.5 text-[10px] {{ $activeTab === 'all' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $counts['all'] }}
            </span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-3">
        <!-- Search -->
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari order, customer, tim, armada..."
                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            >
            <span class="absolute left-3 top-2.5 text-xs text-slate-400">🔍</span>
        </div>

        <!-- Type Filter -->
        <div>
            <select 
                wire:model.live="typeFilter"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            >
                <option value="">Semua Tipe Operasi</option>
                @foreach ($types as $t)
                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <select 
                wire:model.live="statusFilter"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            >
                <option value="">Semua Status Operasi</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Operational Schedule Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($schedules as $sch)
            <div class="rounded-2xl border {{ $sch->isCompleted() ? 'border-emerald-200 bg-emerald-50/10' : ($sch->status->value === 'IN_PROGRESS' ? 'border-amber-400 bg-amber-50/20 ring-1 ring-amber-300' : 'border-slate-200 bg-white') }} p-5 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <!-- Top Card Bar -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-bold border {{ $sch->type->badgeColor() }}">
                            <span>{{ $sch->type->icon() }}</span>
                            <span>{{ $sch->type->label() }}</span>
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border {{ $sch->status->badgeColor() }}">
                            {{ $sch->status->label() }}
                        </span>
                    </div>

                    <!-- Date & Time Window -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Jadwal Misi</span>
                            <span class="font-bold text-slate-900 text-xs mt-0.5 block">
                                📅 {{ $sch->scheduled_date->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Jam Operasi</span>
                            <span class="font-mono font-semibold text-slate-800 text-xs mt-0.5 block">
                                ⏰ {{ $sch->start_time ? substr($sch->start_time, 0, 5) . ' - ' . substr($sch->end_time, 0, 5) : 'Fleksibel' }}
                            </span>
                        </div>
                    </div>

                    <!-- Order & Customer Details -->
                    <div>
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.orders.show', $sch->order) }}" class="font-mono font-bold text-amber-600 text-xs hover:underline">
                                {{ $sch->order->order_code }}
                            </a>
                            <span class="text-[11px] text-slate-400">{{ $sch->order->service->name }}</span>
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1">
                            {{ $sch->order->customer->name }}
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sch->order->customer->phone) }}" target="_blank" class="text-xs text-slate-500 font-mono hover:text-amber-600 block">
                            📞 {{ $sch->order->customer->phone }}
                        </a>
                    </div>

                    <!-- Location info -->
                    <div class="text-xs text-slate-600 space-y-1">
                        <div class="flex items-start gap-1.5">
                            <span class="text-amber-600">📍</span>
                            <span class="text-[11px] leading-tight line-clamp-2">
                                {{ $sch->order->pickupAddress?->address ?? 'Alamat pickup belum diisi' }}
                            </span>
                        </div>
                        @if ($sch->type->value !== 'PICKUP' && $sch->order->destinationAddress)
                            <div class="flex items-start gap-1.5 pt-1">
                                <span class="text-blue-600">🏁</span>
                                <span class="text-[11px] leading-tight line-clamp-2">
                                    {{ $sch->order->destinationAddress->address }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Team & Vehicle -->
                    <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500 space-y-0.5">
                        <div>👥 Petugas: <span class="font-semibold text-slate-800">{{ $sch->assigned_team ?: '-' }}</span></div>
                        <div>🚛 Armada: <span class="font-semibold text-slate-800">{{ $sch->vehicle ?: '-' }}</span></div>
                    </div>
                </div>

                <!-- Action Button in Footer -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.orders.show', $sch->order) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                        Buka Order ➔
                    </a>

                    @if ($sch->status->value === 'SCHEDULED')
                        <button 
                            type="button" 
                            wire:click="startMission({{ $sch->id }})"
                            class="inline-flex items-center gap-1 rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-bold text-slate-950 hover:bg-amber-400 cursor-pointer"
                        >
                            <span>🚀 Mulai Pengerjaan</span>
                        </button>
                    @elseif ($sch->status->value === 'IN_PROGRESS')
                        <button 
                            type="button" 
                            wire:click="completeMission({{ $sch->id }})"
                            class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-500 cursor-pointer"
                        >
                            <span>✅ Selesai</span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl bg-white border border-slate-200 p-12 text-center text-slate-400 space-y-2">
                <span class="text-3xl block">🚚</span>
                <p class="text-sm font-semibold text-slate-700">Tidak ada jadwal operasional pada kategori ini.</p>
                <p class="text-xs text-slate-400">Silakan pilih tab lain atau atur jadwal misi baru untuk order aktif.</p>
            </div>
        @endforelse
    </div>

    @if ($schedules->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $schedules->links() }}
        </div>
    @endif

    <!-- Create Schedule Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Atur Jadwal Misi Operasional Baru
                    </h3>
                    <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit="saveSchedule" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Order Pelanggan *</label>
                        <select wire:model="selectedOrderId" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                            <option value="">-- Pilih Order Aktif --</option>
                            @foreach ($orders as $ord)
                                <option value="{{ $ord->id }}">
                                    {{ $ord->order_code }} - {{ $ord->customer->name }} ({{ $ord->service->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('selectedOrderId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Operasi *</label>
                        <select wire:model="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                            @foreach ($types as $t)
                                <option value="{{ $t->value }}">{{ $t->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal *</label>
                            <input type="date" wire:model="scheduledDate" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                            @error('scheduledDate') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai</label>
                            <input type="time" wire:model="startTime" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai</label>
                            <input type="time" wire:model="endTime" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tim Petugas Lapangan</label>
                        <input type="text" wire:model="assignedTeam" placeholder="Contoh: Tim Lapangan 1 (Budi & Eko)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Armada Kendaraan</label>
                        <input type="text" wire:model="vehicle" placeholder="Contoh: Daihatsu GranMax Pick-up N 1234 AB" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Tim</label>
                        <textarea wire:model="notes" rows="2" placeholder="Bawa tali tambang ekstra dan kardus cadangan..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeCreateModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer">
                            Simpan Jadwal Operasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

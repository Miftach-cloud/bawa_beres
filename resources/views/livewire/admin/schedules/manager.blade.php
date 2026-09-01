<div class="space-y-4">
    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div>
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <x-icon name="truck" class="w-4 h-4 text-amber-600" />
                <span>Jadwal Operasi (Pickup & Delivery)</span>
            </h3>
            <p class="text-xs text-slate-500">Penugasan jadwal armada, driver, dan tim angkut</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal('PICKUP')"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <x-icon name="plus" class="w-3.5 h-3.5 text-slate-950" />
            <span>Atur Jadwal Operasional</span>
        </button>
    </div>

    <!-- Flash message -->
    @if (session()->has('schedule_message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 flex items-center gap-2 text-xs font-medium text-emerald-800">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            <span>{{ session('schedule_message') }}</span>
        </div>
    @endif

    <!-- Schedules List -->
    <div class="space-y-3">
        @forelse ($schedules as $sch)
            <div class="rounded-xl border {{ $sch->isCompleted() ? 'border-emerald-200 bg-emerald-50/10' : ($sch->status->value === 'IN_PROGRESS' ? 'border-amber-300 bg-amber-50/20 ring-1 ring-amber-200' : 'border-slate-200 bg-white') }} p-4 shadow-xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-bold border {{ $sch->type->badgeColor() }}">
                            <x-icon name="truck" class="w-3.5 h-3.5" />
                            <span>{{ $sch->type->label() }}</span>
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border {{ $sch->status->badgeColor() }}">
                            {{ $sch->status->label() }}
                        </span>
                    </div>

                    <div class="text-xs font-bold font-mono text-slate-900 flex items-center gap-1.5">
                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-600" />
                        <span>{{ $sch->scheduled_date->translatedFormat('d F Y') }}</span>
                        @if ($sch->start_time)
                            <span class="text-slate-500 font-normal">({{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB)</span>
                        @endif
                    </div>
                </div>

                <!-- Assignment details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold flex items-center gap-1 mb-0.5">
                            <x-icon name="users" class="w-3 h-3 text-slate-400" />
                            <span>Tim Petugas Lapangan:</span>
                        </span>
                        <span class="font-semibold text-slate-800">{{ $sch->assigned_team ?: 'Belum ditentukan' }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold flex items-center gap-1 mb-0.5">
                            <x-icon name="truck" class="w-3 h-3 text-slate-400" />
                            <span>Armada Kendaraan:</span>
                        </span>
                        <span class="font-semibold text-slate-800">{{ $sch->vehicle ?: 'Belum ditentukan' }}</span>
                    </div>

                    @if ($sch->notes)
                        <div class="sm:col-span-2 text-[11px] text-slate-500 italic mt-1 pt-1 border-t border-slate-200/50">
                            Catatan Operasional: {{ $sch->notes }}
                        </div>
                    @endif
                </div>

                <!-- Action Controls -->
                @if ($sch->status->value === 'SCHEDULED')
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="startMission({{ $sch->id }})"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1 text-xs font-semibold text-slate-950 hover:bg-amber-400 cursor-pointer shadow-xs"
                        >
                            <x-icon name="truck" class="w-3.5 h-3.5 text-slate-950" />
                            <span>Mulai Pengerjaan (On The Way)</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="cancelMission({{ $sch->id }})"
                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer"
                        >
                            <x-icon name="x" class="w-3 h-3 text-slate-500" />
                            <span>Batalkan</span>
                        </button>
                    </div>
                @elseif ($sch->status->value === 'IN_PROGRESS')
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="completeMission({{ $sch->id }})"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 cursor-pointer"
                        >
                            <x-icon name="check" class="w-3.5 h-3.5 text-white" />
                            <span>Tandai Misi Selesai</span>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-xs">
                <x-icon name="truck" class="w-8 h-8 text-slate-300 mx-auto mb-1.5" />
                Belum ada jadwal penjemputan atau pengantaran yang diatur.
            </div>
        @endforelse
    </div>

    <!-- Create Schedule Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Atur Jadwal Operasional
                    </h3>
                    <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <form wire:submit="saveSchedule" class="space-y-4">
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

<div>
    @if ($show && $item)
        <div class="fixed inset-0 z-50 flex justify-end bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg bg-white h-full shadow-2xl p-6 overflow-y-auto space-y-6 flex flex-col justify-between">
                <div class="space-y-4">
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base">⏱️</span>
                                <h3 class="font-bold text-slate-900 text-base">
                                    Histori Perpindahan (Movement Audit Trail)
                                </h3>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-mono text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    {{ $item->inventory_code }}
                                </span>
                                <span class="font-bold text-xs text-slate-800">{{ $item->name }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Order #{{ $item->order->order_code }} • Customer: {{ $item->order->customer->name }}
                            </p>
                        </div>

                        <button type="button" wire:click="close" class="text-slate-400 hover:text-slate-600 font-bold p-1 cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Current Position Card -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Posisi Saat Ini:</span>
                            <span class="font-mono font-bold text-slate-900 mt-0.5 block">
                                📍 {{ $item->storage_location ?: 'Belum di rak / Transit' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Status:</span>
                            <span class="font-semibold text-slate-700 mt-0.5 block">
                                {{ $item->status->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Movement Audit Trail Timeline -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-xs text-slate-900">
                            Log Riwayat Mutasi ({{ $movements->count() }} kejadian)
                        </h4>

                        <div class="flow-root">
                            <ul class="-mb-6 space-y-4">
                                @forelse ($movements as $m)
                                    <li class="relative flex gap-x-3 pb-4">
                                        <div class="relative flex h-6 w-6 flex-none items-center justify-center bg-white">
                                            <div class="h-2 w-2 rounded-full bg-blue-500 ring-2 ring-blue-200"></div>
                                        </div>

                                        <div class="flex-auto rounded-xl bg-slate-50 p-3.5 text-xs border border-slate-200 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-bold border {{ $m->movement_type->badgeColor() }}">
                                                    <span>{{ $m->movement_type->icon() }}</span>
                                                    <span>{{ $m->movement_type->label() }}</span>
                                                </span>
                                                <span class="font-mono text-[10px] text-slate-400">
                                                    {{ $m->moved_at->translatedFormat('d M Y, H:i') }} WIB
                                                </span>
                                            </div>

                                            <!-- From -> To Route -->
                                            <div class="flex items-center gap-2 font-mono font-bold text-xs bg-white p-2 rounded-lg border border-slate-100">
                                                <span class="text-slate-600 truncate max-w-[140px]">{{ $m->from_location_code ?: 'Receiving' }}</span>
                                                <span class="text-amber-500">➔</span>
                                                <span class="text-slate-900 truncate max-w-[140px]">{{ $m->to_location_code ?: 'Outbound' }}</span>
                                            </div>

                                            @if ($m->notes)
                                                <p class="text-slate-600 italic text-[11px] leading-relaxed">
                                                    "{{ $m->notes }}"
                                                </p>
                                            @endif

                                            @if ($m->performer)
                                                <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-200/50">
                                                    Petugas: <strong class="text-slate-600">{{ $m->performer->name }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                                        <span class="text-2xl block mb-1">⏱️</span>
                                        Belum ada riwayat perpindahan tercatat untuk barang ini.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="button" wire:click="close" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

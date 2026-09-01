<div>
    @if ($show && $item)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🏷️</span>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">
                                QR Code & Label Fisik Barang
                            </h3>
                            <span class="font-mono text-[11px] font-bold text-amber-600">
                                {{ $item->inventory_code }}
                            </span>
                        </div>
                    </div>
                    <button type="button" wire:click="close" class="text-slate-400 hover:text-slate-600 font-bold p-1 cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Label Preview Card -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-slate-900 text-xs">BAWABERES</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">CUSTODY BADGE</span>
                        </div>
                        <span class="font-mono font-bold text-xs bg-white px-2 py-0.5 rounded border border-slate-200">
                            #{{ $item->qr_code }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-xs flex-shrink-0 flex items-center justify-center">
                            {!! $item->getQrSvg(120) !!}
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">Nama Barang</span>
                                <span class="font-bold text-slate-900 text-sm block leading-tight">{{ $item->name }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">Lokasi Rak</span>
                                <span class="font-mono font-bold text-amber-600 block">
                                    📍 {{ $item->storage_location ?: 'Belum di rak' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">Kondisi</span>
                                <span class="font-semibold text-slate-700 block">{{ $item->condition->label() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Target URL -->
                    <div class="bg-white p-2 rounded-lg border border-slate-200 text-[10px] font-mono text-slate-500 truncate">
                        🔗 {{ $item->scan_url }}
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <a 
                        href="{{ route('admin.inventory.label', $item) }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 cursor-pointer"
                    >
                        <span>🖨️ Buka Tab Cetak (Print)</span>
                    </a>

                    <button type="button" wire:click="close" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

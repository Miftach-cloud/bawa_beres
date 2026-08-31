<div class="space-y-4">
    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div>
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <span>🏷️</span> Manajemen Fisik Barang (Physical Inventory)
            </h3>
            <p class="text-xs text-slate-500">Barang fisik nyata dalam penguasaan perusahaan (Kode unik INV-XXXXXX)</p>
        </div>

        <div class="flex items-center gap-2">
            @if ($items->isEmpty() && $order->items->isNotEmpty())
                <button 
                    type="button" 
                    wire:click="generateExpected"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
                >
                    <span>⚡ Generate dari Deklarasi Order</span>
                </button>
            @endif

            <button 
                type="button" 
                wire:click="openAddModal"
                class="inline-flex items-center gap-1 rounded-xl bg-white border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer"
            >
                <span>➕ Tambah Barang Fisik</span>
            </button>
        </div>
    </div>

    <!-- Flash message -->
    @if (session()->has('inventory_message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 flex items-center gap-2 text-xs font-medium text-emerald-800">
            <span>✅</span>
            <span>{{ session('inventory_message') }}</span>
        </div>
    @endif

    <!-- Physical Inventory Table -->
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Kode Inventaris</th>
                        <th class="px-4 py-3">Nama Barang Fisik</th>
                        <th class="px-4 py-3">Kondisi Fisik</th>
                        <th class="px-4 py-3">Status Penguasaan</th>
                        <th class="px-4 py-3">Lokasi Gudang</th>
                        <th class="px-4 py-3 text-right">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-amber-600 text-xs">
                                {{ $item->inventory_code }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $item->name }}</div>
                                @if ($item->notes)
                                    <p class="text-[11px] text-slate-400 italic mt-0.5">{{ $item->notes }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold border {{ $item->condition->badgeColor() }}">
                                    {{ $item->condition->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border {{ $item->status->badgeColor() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 font-mono">
                                {{ $item->storage_location ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($item->status->value === 'EXPECTED')
                                        <button 
                                            type="button" 
                                            wire:click="receive({{ $item->id }})"
                                            class="inline-flex items-center rounded-lg bg-cyan-600 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-cyan-500 cursor-pointer"
                                        >
                                            <span>📥 Terima</span>
                                        </button>
                                    @elseif ($item->status->value === 'RECEIVED')
                                        <button 
                                            type="button" 
                                            wire:click="openCheckModal({{ $item->id }})"
                                            class="inline-flex items-center rounded-lg bg-blue-600 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-blue-500 cursor-pointer"
                                        >
                                            <span>🔍 QC & Cek</span>
                                        </button>
                                    @elseif ($item->status->value === 'CHECKED')
                                        <button 
                                            type="button" 
                                            wire:click="openStoreModal({{ $item->id }})"
                                            class="inline-flex items-center rounded-lg bg-purple-600 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-purple-500 cursor-pointer"
                                        >
                                            <span>🏢 Simpan ke Rak</span>
                                        </button>
                                    @elseif ($item->status->value === 'STORED' || $item->status->value === 'OUTBOUND')
                                        <button 
                                            type="button" 
                                            wire:click="release({{ $item->id }})"
                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-emerald-500 cursor-pointer"
                                        >
                                            <span>🤝 Serah Terima</span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                Belum ada barang fisik yang terdaftar dalam inventaris order ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Check & QC Modal -->
    @if ($showCheckModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Quality Control & Pemeriksaan Kondisi
                    </h3>
                    <button type="button" wire:click="closeCheckModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kondisi Fisik Barang *</label>
                        <select wire:model="condition" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                            @foreach ($conditions as $c)
                                <option value="{{ $c->value }}">{{ $c->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Kondisi / Kerusakan Bawaan</label>
                        <textarea wire:model="checkNotes" rows="3" placeholder="Contoh: Ada goresan kecil di sudut kanan atas sebelum diangkut..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeCheckModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmCheck" class="rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-500 cursor-pointer">
                        Simpan Hasil QC
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Store Modal -->
    @if ($showStoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Alokasi Lokasi Rak Storage Gudang
                    </h3>
                    <button type="button" wire:click="closeStoreModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lokasi Rak / Gudang *</label>
                        <input type="text" wire:model="storageLocation" placeholder="Contoh: Rak A-02 / Gudang Dinoyo Lt. 1" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeStoreModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmStore" class="rounded-xl bg-purple-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-purple-500 cursor-pointer">
                        Simpan ke Lokasi Rak
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Item Modal -->
    @if ($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Tambah Barang Fisik di Lokasi
                    </h3>
                    <button type="button" wire:click="closeAddModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit="saveNewItem" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Barang Fisik *</label>
                        <input type="text" wire:model="newItemName" placeholder="Contoh: Kipas Angin Berdiri" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900">
                        @error('newItemName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kategori / Ukuran</label>
                            <input type="text" wire:model="newItemCategory" placeholder="Sedang" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kondisi Fisik *</label>
                            <select wire:model="newItemCondition" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                @foreach ($conditions as $c)
                                    <option value="{{ $c->value }}">{{ $c->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <textarea wire:model="newItemNotes" rows="2" placeholder="Barang tambahan tidak tercantum di booking awal..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeAddModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 cursor-pointer">
                            Simpan Barang Fisik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

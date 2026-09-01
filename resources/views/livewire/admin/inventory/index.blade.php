<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Registrasi Inventaris Fisik (Inventory Items)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar fisik barang dalam custody perusahaan, kode INV unik, dan pelacakan QC</p>
        </div>
    </div>

    @if ($ordersReadyForInventory->isNotEmpty())
        <div class="rounded-2xl bg-amber-50 p-4 border border-amber-200 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-amber-900 mb-1">Buat inventaris dari order yang sudah dijemput</label>
                    <select wire:model="selectedOrderId" class="w-full rounded-xl border border-amber-300 bg-white px-3 py-2 text-xs text-slate-900">
                        <option value="">Pilih order</option>
                        @foreach ($ordersReadyForInventory as $readyOrder)
                            <option value="{{ $readyOrder->id }}">{{ $readyOrder->order_code }} — {{ $readyOrder->customer->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedOrderId') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="button" wire:click="generateExpected" class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-400">
                    Generate Inventory
                </button>
            </div>
        </div>
    @endif

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <x-icon name="check-circle" class="w-5 h-5 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari kode INV, nama barang, order, rak..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                <span class="absolute left-3 top-2.5 text-xs text-slate-400">
                    <x-icon name="search" class="w-3.5 h-3.5 text-slate-400" />
                </span>
            </div>

            <!-- Status Filter -->
            <div>
                <select 
                    wire:model.live="statusFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Status Penguasaan</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Condition Filter -->
            <div>
                <select 
                    wire:model.live="conditionFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Kondisi Fisik</option>
                    @foreach ($conditions as $c)
                        <option value="{{ $c->value }}">{{ $c->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 text-slate-500">
            <div>
                Ditemukan <span class="font-bold text-slate-900">{{ $items->total() }}</span> item inventaris fisik
            </div>
            @if ($search || $statusFilter || $conditionFilter)
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

    <!-- Inventory Table Container -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode Inventaris</th>
                        <th class="px-6 py-3.5">Nama Fisik Barang</th>
                        <th class="px-6 py-3.5">Kode Order & Customer</th>
                        <th class="px-6 py-3.5">Kondisi Fisik</th>
                        <th class="px-6 py-3.5">Status Penguasaan</th>
                        <th class="px-6 py-3.5">Lokasi Gudang</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 text-xs">
                                {{ $item->inventory_code }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $item->name }}</div>
                                @if ($item->notes)
                                    <p class="text-[11px] text-slate-400 italic mt-0.5">{{ $item->notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $item->order) }}" class="font-mono font-bold text-slate-900 hover:text-amber-600 block text-xs">
                                    {{ $item->order->order_code }}
                                </a>
                                <div class="text-xs text-slate-500">{{ $item->order->customer->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold border {{ $item->condition->badgeColor() }}">
                                    {{ $item->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider border {{ $item->status->badgeColor() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $item->storage_location ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="$dispatch('openQrLabel', { itemId: {{ $item->id }} })"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer shadow-2xs"
                                        title="QR Code & Cetak Label"
                                    >
                                        <x-icon name="qr" class="w-3.5 h-3.5 text-amber-600" />
                                        <span>QR</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="$dispatch('openPhotoGallery', { itemId: {{ $item->id }} })"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer shadow-2xs"
                                        title="Dokumentasi Foto"
                                    >
                                        <x-icon name="camera" class="w-3.5 h-3.5 text-blue-600" />
                                        <span>{{ $item->photos_count }}</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="$dispatch('openMovementTimeline', { itemId: {{ $item->id }} })"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer shadow-2xs"
                                        title="Histori Mutasi Perpindahan"
                                    >
                                        <x-icon name="clock" class="w-3.5 h-3.5 text-purple-600" />
                                        <span>{{ $item->movements_count }}</span>
                                    </button>

                                    @if ($item->status->value === 'EXPECTED')
                                        <button 
                                            type="button" 
                                            wire:click="receive({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-cyan-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-cyan-500 cursor-pointer shadow-2xs"
                                        >
                                            <x-icon name="download" class="w-3 h-3 text-white" />
                                            <span>Terima</span>
                                        </button>
                                    @elseif ($item->status->value === 'RECEIVED')
                                        <button 
                                            type="button" 
                                            wire:click="openCheckModal({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-blue-500 cursor-pointer shadow-2xs"
                                        >
                                            <x-icon name="search" class="w-3 h-3 text-white" />
                                            <span>QC</span>
                                        </button>
                                    @elseif ($item->status->value === 'CHECKED')
                                        <button 
                                            type="button" 
                                            wire:click="openStoreModal({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-purple-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-purple-500 cursor-pointer shadow-2xs"
                                        >
                                            <x-icon name="warehouse" class="w-3 h-3 text-white" />
                                            <span>Rak</span>
                                        </button>
                                    @elseif ($item->status->value === 'STORED')
                                        <button 
                                            type="button" 
                                            wire:click="openRelocateModal({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-indigo-500 cursor-pointer shadow-2xs"
                                            title="Pindahkan ke Rak Lain"
                                        >
                                            <x-icon name="refresh" class="w-3 h-3 text-white" />
                                            <span>Pindah</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            wire:click="outbound({{ $item->id }})"
                                            wire:confirm="Pindahkan barang ini ke area outbound?"
                                            class="inline-flex items-center gap-1 rounded-lg bg-amber-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-amber-500 cursor-pointer shadow-2xs"
                                        >
                                            <x-icon name="truck" class="w-3 h-3 text-white" />
                                            <span>Outbound</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            wire:click="release({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-500 cursor-pointer shadow-2xs"
                                        >
                                            <x-icon name="check-circle" class="w-3 h-3 text-white" />
                                            <span>Release</span>
                                        </button>
                                    @elseif ($item->status->value === 'OUTBOUND')
                                        <button 
                                            type="button" 
                                            wire:click="release({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-500 cursor-pointer shadow-2xs"
                                        >
                                            <x-icon name="check-circle" class="w-3 h-3 text-white" />
                                            <span>Release</span>
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.orders.show', $item->order) }}" class="text-xs text-slate-400 hover:text-slate-700">
                                        ➔
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <x-icon name="tag" class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                                <p class="text-sm">Tidak ada barang inventaris fisik yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <!-- Check & QC Modal -->
    @if ($showCheckModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Quality Control & Cek Kondisi Fisik
                    </h3>
                    <button type="button" wire:click="closeCheckModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
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
                        <textarea wire:model="checkNotes" rows="3" placeholder="Catatan goresan / pecah / cacat sebelum diangkut..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900"></textarea>
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
                    <button type="button" wire:click="closeStoreModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    @if ($availableLocations->isNotEmpty())
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Slot Rak Master *</label>
                            <select wire:model="selectedLocationId" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono">
                                <option value="">-- Pilih dari Master Slot Rak --</option>
                                @foreach ($availableLocations as $loc)
                                    <option value="{{ $loc->id }}">
                                        {{ $loc->code }} ({{ $loc->warehouse }} - {{ $loc->type->label() }}) [Sisa: {{ $loc->remainingCapacity() }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Label Lokasi Manual (Opsional / Custom)</label>
                        <input type="text" wire:model="storageLocation" placeholder="Contoh: MLG01-A-R02-L03" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 font-mono">
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


    <!-- Relocate Modal -->
    @if ($showRelocateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Pindahkan Posisi / Rak Barang (Relocate)
                    </h3>
                    <button type="button" wire:click="closeRelocateModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Slot Rak Tujuan Baru *</label>
                        <select wire:model="relocateLocationId" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono">
                            <option value="">-- Pilih Slot Rak Tujuan --</option>
                            @foreach ($availableLocations as $loc)
                                <option value="{{ $loc->id }}">
                                    {{ $loc->code }} ({{ $loc->warehouse }} - {{ $loc->type->label() }}) [Sisa: {{ $loc->remainingCapacity() }}]
                                </option>
                            @endforeach
                        </select>
                        @error('relocateLocationId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alasan / Catatan Pemindahan</label>
                        <textarea wire:model="relocateNotes" rows="2" placeholder="Contoh: Reorganisasi rak penyimpanan..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeRelocateModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmRelocate" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 cursor-pointer">
                        Konfirmasi Pindah Rak
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Embedded Photos Gallery Modal -->
    <livewire:admin.inventory.photos-modal />

    <!-- Embedded Movement Timeline Modal -->
    <livewire:admin.inventory.movement-timeline-modal />

    <!-- Embedded QR Label Modal -->
    <livewire:admin.inventory.qr-label-modal />
</div>



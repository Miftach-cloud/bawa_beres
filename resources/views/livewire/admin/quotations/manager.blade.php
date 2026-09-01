<div class="space-y-4">
    <!-- Header & Create Button -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <span>📑</span> Sistem Penawaran Harga (Quotations)
            </h3>
            <p class="text-xs text-slate-500">Rincian biaya resmi dan riwayat revisi penawaran</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <span>➕ Buat Penawaran Baru</span>
        </button>
    </div>

    <!-- Flash message -->
    @if (session()->has('quotation_message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 flex items-center gap-2 text-xs font-medium text-emerald-800">
            <span>✅</span>
            <span>{{ session('quotation_message') }}</span>
        </div>
    @endif

    <!-- Quotation Version List (Section 6.3) -->
    <div class="space-y-4">
        @forelse ($quotations as $quo)
            <div class="rounded-xl border {{ $quo->isAccepted() ? 'border-emerald-300 bg-emerald-50/20 ring-1 ring-emerald-200' : ($quo->status->value === 'REJECTED' ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-white') }} p-4 shadow-xs space-y-3">
                <!-- Quotation Card Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2.5">
                        <span class="font-mono font-bold text-slate-900 text-xs">
                            {{ $quo->quotation_number }}
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border {{ $quo->status->badgeColor() }}">
                            {{ $quo->status->label() }}
                        </span>
                        <span class="text-[11px] text-slate-400">
                            Versi {{ $quo->version }}
                        </span>
                    </div>

                    <div class="text-[11px] text-slate-400">
                        {{ $quo->created_at->translatedFormat('d M Y, H:i') }}
                    </div>
                </div>

                <!-- Items Breakdown (Section 6.2) -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-xs text-left">
                        <thead class="text-slate-400 font-semibold">
                            <tr>
                                <th class="py-1.5">Deskripsi Komponen Biaya</th>
                                <th class="py-1.5 text-center">Qty</th>
                                <th class="py-1.5 text-right">Tarif Satuan</th>
                                <th class="py-1.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700">
                            @foreach ($quo->items as $item)
                                <tr>
                                    <td class="py-1.5 font-medium text-slate-800">
                                        {{ $item->name }}
                                        @if ($item->description)
                                            <span class="text-[10px] text-slate-400 block font-normal">{{ $item->description }}</span>
                                        @endif
                                    </td>
                                    <td class="py-1.5 text-center font-mono">{{ $item->quantity }}</td>
                                    <td class="py-1.5 text-right font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-1.5 text-right font-mono font-semibold text-slate-900">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Calculation Summary -->
                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="space-y-0.5 text-slate-500 text-[11px]">
                        <div>Subtotal: <span class="font-mono font-medium text-slate-700">Rp {{ number_format($quo->subtotal, 0, ',', '.') }}</span></div>
                        @if ($quo->discount > 0)
                            <div class="text-emerald-600">Diskon Khusus: <span class="font-mono font-bold">- Rp {{ number_format($quo->discount, 0, ',', '.') }}</span></div>
                        @endif
                        @if ($quo->tax > 0)
                            <div>Pajak: <span class="font-mono font-medium text-slate-700">+ Rp {{ number_format($quo->tax, 0, ',', '.') }}</span></div>
                        @endif
                        @if ($quo->valid_until)
                            <div class="text-amber-700">Berlaku s/d: {{ $quo->valid_until->translatedFormat('d M Y') }}</div>
                        @endif
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Total Biaya Penawaran</span>
                        <span class="font-mono font-extrabold text-base {{ $quo->isAccepted() ? 'text-emerald-600' : 'text-slate-900' }}">
                            Rp {{ number_format($quo->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if ($quo->notes)
                    <div class="text-[11px] text-slate-500 bg-white p-2 rounded border border-slate-100">
                        <span class="font-bold text-slate-600">Catatan:</span> {{ $quo->notes }}
                    </div>
                @endif

                <!-- Action Toolbar per Quotation -->
                <div class="flex flex-wrap items-center justify-end gap-2 pt-1 border-t border-slate-100">
                    @if ($quo->isDraft())
                        <button 
                            type="button" 
                            wire:click="send({{ $quo->id }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-500 cursor-pointer"
                        >
                            <span>📤 Kirim ke Customer</span>
                        </button>
                    @endif

                    @if ($quo->isSent())
                        <button 
                            type="button" 
                            wire:click="accept({{ $quo->id }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-500 cursor-pointer"
                        >
                            <span>✅ Setujui (Accept)</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="openRejectModal({{ $quo->id }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 cursor-pointer"
                        >
                            <span>✕ Tolak / Butuh Revisi</span>
                        </button>
                    @endif

                    <!-- Revision Button (Section 6.3) -->
                    <button 
                        type="button" 
                        wire:click="openRevisionModal({{ $quo->id }})"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer"
                    >
                        <span>🔄 Buat Revisi Baru</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-xs">
                <span class="text-2xl block mb-1">📑</span>
                Belum ada penawaran harga (quotation) yang dibuat untuk pesanan ini.
            </div>
        @endforelse
    </div>

    <!-- Create / Revision Modal (Section 6.2 & 6.3) -->
    @if ($showQuotationModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        {{ $isRevision ? 'Buat Revisi Penawaran Harga' : 'Buat Penawaran Harga Baru' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 font-bold">
                        ✕
                    </button>
                </div>

                <form wire:submit="saveQuotation" class="space-y-4">
                    <!-- Line Items Table -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Rincian Komponen Biaya (Items)
                            </label>
                            <button type="button" wire:click="addItemRow" class="text-xs text-amber-600 font-semibold hover:underline cursor-pointer">
                                ➕ Tambah Komponen Biaya
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach ($items as $idx => $item)
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200 items-center text-xs">
                                    <div class="sm:col-span-5">
                                        <input type="text" wire:model="items.{{ $idx }}.name" placeholder="Nama Komponen (cth: Biaya Pickup)" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-900">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input type="number" wire:model.live="items.{{ $idx }}.quantity" min="1" placeholder="Qty" class="w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 text-center font-mono">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <input type="number" wire:model.live="items.{{ $idx }}.unit_price" min="0" step="5000" placeholder="Tarif Satuan (Rp)" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-900 font-mono">
                                    </div>
                                    <div class="sm:col-span-2 text-right">
                                        <button type="button" wire:click="removeItemRow({{ $idx }})" class="text-rose-500 hover:text-rose-700 text-xs font-semibold cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Discount, Tax & Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Diskon / Potongan (Rp)</label>
                            <input type="number" wire:model.live="discount" min="0" step="1000" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 font-mono text-slate-900">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Pajak / Biaya Lain (Rp)</label>
                            <input type="number" wire:model.live="tax" min="0" step="1000" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 font-mono text-slate-900">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Berlaku Sampai Tanggal</label>
                            <input type="date" wire:model="validUntil" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-slate-900">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="text-xs">
                        <label class="block font-medium text-slate-700 mb-1">Catatan Tambahan untuk Pelanggan</label>
                        <textarea wire:model="notes" rows="2" placeholder="Ketentuan biaya bongkar pasang lemari, jaminan keamanan..." class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-slate-900"></textarea>
                    </div>

                    <!-- Realtime Calculation Box -->
                    <div class="rounded-xl bg-slate-900 p-4 text-white flex items-center justify-between">
                        <div class="text-xs space-y-0.5 text-slate-400">
                            <div>Subtotal: <span class="font-mono text-white">Rp {{ number_format($this->calculateSubtotal, 0, ',', '.') }}</span></div>
                            @if ((float)$discount > 0)
                                <div class="text-emerald-400">Diskon: <span class="font-mono">- Rp {{ number_format((float)$discount, 0, ',', '.') }}</span></div>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Total Biaya Final</span>
                            <span class="text-xl font-mono font-bold text-amber-400">
                                Rp {{ number_format($this->calculateTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer">
                            {{ $isRevision ? 'Simpan Revisi Penawaran' : 'Simpan Penawaran' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Rejection Modal -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-rose-700 text-base flex items-center gap-2">
                        <span>⚠️</span> Tolak / Permintaan Revisi Penawaran
                    </h3>
                    <button type="button" wire:click="closeRejectModal" class="text-slate-400 hover:text-slate-600 font-bold">
                        ✕
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Penolakan / Catatan Revisi *
                    </label>
                    <textarea 
                        wire:model="rejectionReason" 
                        rows="3" 
                        placeholder="Contoh: Customer meminta diskon tambahan untuk armada..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-rose-500 focus:ring-1 focus:ring-rose-500"
                    ></textarea>
                    @error('rejectionReason') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeRejectModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmReject" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-500 cursor-pointer">
                        Konfirmasi Penolakan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

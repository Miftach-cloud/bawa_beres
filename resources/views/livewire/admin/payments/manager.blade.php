<div class="space-y-4">
    <!-- Header & Balance Summary (Important: multi-payment support) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div>
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <x-icon name="credit-card" class="w-4 h-4 text-amber-600" />
                <span>Riwayat Pembayaran (Payments MVP)</span>
            </h3>
            <p class="text-xs text-slate-500">Pencatatan pembayaran transfer manual, QRIS, dan uang tunai</p>
        </div>

        <button 
            type="button" 
            wire:click="openRecordModal"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <x-icon name="plus" class="w-3.5 h-3.5 text-slate-950" />
            <span>Catat Pembayaran</span>
        </button>
    </div>

    <!-- Financial Progress Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Biaya Order</span>
            <span class="text-base font-bold font-mono text-slate-900 mt-0.5 block">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
        </div>

        <div class="bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
            <span class="text-[10px] uppercase font-bold text-emerald-600 block">Total Terbayar (Lunas)</span>
            <span class="text-base font-bold font-mono text-emerald-700 mt-0.5 block">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </span>
        </div>

        <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-100">
            <span class="text-[10px] uppercase font-bold text-amber-700 block">Sisa Tagihan</span>
            <span class="text-base font-bold font-mono text-amber-800 mt-0.5 block">
                Rp {{ number_format($remainingBalance, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Flash message -->
    @if (session()->has('payment_message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 flex items-center gap-2 text-xs font-medium text-emerald-800">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            <span>{{ session('payment_message') }}</span>
        </div>
    @endif

    <!-- Payment Transactions List -->
    <div class="space-y-3">
        @forelse ($payments as $pay)
            <div class="rounded-xl border {{ $pay->isVerified() ? 'border-emerald-200 bg-emerald-50/10' : ($pay->status->value === 'REJECTED' ? 'border-rose-200 bg-rose-50/10' : 'border-slate-200 bg-white') }} p-3.5 shadow-xs space-y-2.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-base">
                            @if ($pay->method->value === 'BANK_TRANSFER')
                                <x-icon name="credit-card" class="w-4 h-4 text-blue-600" />
                            @elseif ($pay->method->value === 'QRIS')
                                <x-icon name="qr" class="w-4 h-4 text-purple-600" />
                            @else
                                <x-icon name="banknote" class="w-4 h-4 text-emerald-600" />
                            @endif
                        </span>
                        <div>
                            <span class="font-mono font-bold text-slate-900 text-xs">{{ $pay->payment_number }}</span>
                            <span class="text-xs text-slate-500 block">{{ $pay->method->label() }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="font-mono font-extrabold text-sm {{ $pay->isVerified() ? 'text-emerald-600' : 'text-slate-900' }}">
                            Rp {{ number_format($pay->amount, 0, ',', '.') }}
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border {{ $pay->status->badgeColor() }}">
                            {{ $pay->status->label() }}
                        </span>
                    </div>
                </div>

                <!-- Payment Details (Bank/Account & Proof) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                    <div class="space-y-0.5 text-[11px]">
                        @if ($pay->bank_name || $pay->account_name)
                            <div>Bank / Rekening: <span class="font-semibold text-slate-800">{{ $pay->bank_name }} (a/n {{ $pay->account_name }})</span></div>
                        @endif
                        <div>Waktu Bayar: <span class="text-slate-700">{{ $pay->paid_at ? $pay->paid_at->translatedFormat('d M Y, H:i') : '-' }}</span></div>
                        @if ($pay->verifier)
                            <div class="text-[10px] text-slate-400">Diverifikasi oleh: {{ $pay->verifier->name }}</div>
                        @endif
                        @if ($pay->rejection_reason)
                            <div class="text-rose-600 font-medium">Alasan Ditolak: {{ $pay->rejection_reason }}</div>
                        @endif
                    </div>

                    <!-- Proof Thumbnail / Link -->
                    <div>
                        @if ($pay->proof_url)
                            <button 
                                type="button" 
                                wire:click="viewProof('{{ $pay->proof_url }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 cursor-pointer"
                            >
                                <x-icon name="eye" class="w-3.5 h-3.5 text-slate-600" />
                                <span>Lihat Bukti Transfer</span>
                            </button>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Tanpa lampiran bukti</span>
                        @endif
                    </div>
                </div>

                <!-- Actions: Verify or Reject -->
                @if ($pay->isWaitingVerification())
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="verify({{ $pay->id }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-500 cursor-pointer"
                        >
                            <x-icon name="check" class="w-3.5 h-3.5 text-white" />
                            <span>Verifikasi (Tandai Lunas)</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="openRejectModal({{ $pay->id }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 cursor-pointer"
                        >
                            <x-icon name="x" class="w-3.5 h-3.5 text-rose-600" />
                            <span>Tolak Bukti</span>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-xs">
                <x-icon name="credit-card" class="w-8 h-8 text-slate-300 mx-auto mb-1" />
                Belum ada transaksi pembayaran yang dicatat untuk order ini.
            </div>
        @endforelse
    </div>

    <!-- Record Payment Modal -->
    @if ($showRecordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Catat Pembayaran Order
                    </h3>
                    <button type="button" wire:click="closeRecordModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <form wire:submit="savePayment" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran *</label>
                            <select wire:model="method" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                                @foreach ($methods as $m)
                                    <option value="{{ $m->value }}">{{ $m->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Pembayaran (Rp) *</label>
                            <input type="number" wire:model="amount" min="1000" step="5000" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono font-bold text-slate-900">
                            @error('amount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bank / Channel</label>
                            <input type="text" wire:model="bankName" placeholder="BCA / Mandiri / QRIS BCA" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Atas Nama Pengirim (A/N)</label>
                            <input type="text" wire:model="accountName" placeholder="Nama pemilik rekening" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Bukti Transfer / Resi</label>
                        <input type="file" wire:model="proofFile" accept="image/*" class="w-full rounded-xl border border-slate-300 p-2 text-xs text-slate-700">
                        <div wire:loading wire:target="proofFile" class="text-xs text-amber-600 mt-1">Mengunggah file bukti...</div>
                        @error('proofFile') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <textarea wire:model="notes" rows="2" placeholder="Catatan DP 50% atau pelunasan di lokasi..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeRecordModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer">
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-rose-700 text-base flex items-center gap-2">
                        <x-icon name="alert-circle" class="w-5 h-5 text-rose-600" />
                        <span>Tolak Bukti Pembayaran</span>
                    </h3>
                    <button type="button" wire:click="closeRejectModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Penolakan Bukti Transfer *
                    </label>
                    <textarea 
                        wire:model="rejectionReason" 
                        rows="3" 
                        placeholder="Contoh: Bukti buram / mutasi bank belum masuk / nominal tidak sesuai..."
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

    <!-- Proof Preview Modal -->
    @if ($showProofModal && $previewProofUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs" wire:click.self="closeProofModal">
            <div class="w-full max-w-xl rounded-2xl bg-white p-4 shadow-2xl space-y-3 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h4 class="font-bold text-xs text-slate-900">Lampiran Bukti Transfer</h4>
                    <button type="button" wire:click="closeProofModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer p-1">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>
                <div class="overflow-hidden rounded-xl bg-slate-100 max-h-[70vh] flex items-center justify-center">
                    <img src="{{ $previewProofUrl }}" alt="Bukti Transfer" class="max-h-[68vh] object-contain rounded-lg">
                </div>
            </div>
        </div>
    @endif
</div>

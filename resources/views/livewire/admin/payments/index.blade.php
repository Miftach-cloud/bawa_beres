<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Pusat Transaksi & Pembayaran</h2>
            <p class="text-xs text-slate-500 mt-0.5">Audit mutasi pembayaran, verifikasi bukti transfer manual & QRIS</p>
        </div>

        @if ($pendingCount > 0)
            <div class="inline-flex items-center gap-2 rounded-xl bg-amber-500/10 border border-amber-500/30 px-3.5 py-2 text-xs font-bold text-amber-700">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <span>{{ $pendingCount }} Pembayaran Butuh Verifikasi</span>
            </div>
        @endif
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <x-icon name="check-circle" class="w-5 h-5 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Filter Bar Card -->
    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari no bayar, kode order, nama customer..."
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
                    <option value="">Semua Status Pembayaran</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Method Filter -->
            <div>
                <select 
                    wire:model.live="methodFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Metode Pembayaran</option>
                    @foreach ($methods as $m)
                        <option value="{{ $m->value }}">{{ $m->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Preset Filter -->
            <div>
                <select 
                    wire:model.live="dateFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="all">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="custom">Kustom Rentang Tanggal</option>
                </select>
            </div>
        </div>

        <!-- Custom Date Range Picker -->
        @if ($dateFilter === 'custom')
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100 text-xs">
                <span class="text-slate-500 font-medium">Dari:</span>
                <input type="date" wire:model.live="startDate" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-800">
                <span class="text-slate-500 font-medium">Sampai:</span>
                <input type="date" wire:model.live="endDate" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-800">
            </div>
        @endif

        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 text-slate-500">
            <div>
                Ditemukan <span class="font-bold text-slate-900">{{ $payments->total() }}</span> mutasi transaksi
            </div>
            @if ($search || $statusFilter || $methodFilter || $dateFilter !== 'all')
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

    <!-- Payments Table Container -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No. Transaksi</th>
                        <th class="px-6 py-3.5">Kode Order & Customer</th>
                        <th class="px-6 py-3.5">Metode Bayar</th>
                        <th class="px-6 py-3.5">Nominal (Rp)</th>
                        <th class="px-6 py-3.5">Bukti Transfer</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Waktu</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 text-xs">
                                {{ $payment->payment_number }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-mono font-bold text-slate-900 hover:text-amber-600 block text-xs">
                                    {{ $payment->order->order_code }}
                                </a>
                                <div class="text-xs text-slate-500">{{ $payment->order->customer->name }} ({{ $payment->order->customer->phone }})</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                <div class="flex items-center gap-1.5">
                                    @if ($payment->method->value === 'BANK_TRANSFER')
                                        <x-icon name="credit-card" class="w-3.5 h-3.5 text-blue-600" />
                                    @elseif ($payment->method->value === 'QRIS')
                                        <x-icon name="qr" class="w-3.5 h-3.5 text-purple-600" />
                                    @else
                                        <x-icon name="banknote" class="w-3.5 h-3.5 text-emerald-600" />
                                    @endif
                                    <span>{{ $payment->method->label() }}</span>
                                </div>
                                @if ($payment->bank_name || $payment->account_name)
                                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ $payment->bank_name }} a/n {{ $payment->account_name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 text-xs">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($payment->proof_url)
                                    <button 
                                        type="button" 
                                        wire:click="viewProof('{{ $payment->proof_url }}')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 cursor-pointer"
                                    >
                                        <x-icon name="eye" class="w-3.5 h-3.5 text-slate-600" />
                                        <span>Lihat Bukti</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tanpa File</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold border {{ $payment->status->badgeColor() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $payment->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($payment->isWaitingVerification())
                                    <div class="inline-flex items-center gap-1.5">
                                        <button 
                                            type="button" 
                                            wire:click="verify({{ $payment->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-500 cursor-pointer"
                                        >
                                            <x-icon name="check" class="w-3.5 h-3.5 text-white" />
                                            <span>Verifikasi</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            wire:click="openRejectModal({{ $payment->id }})"
                                            class="inline-flex items-center gap-1 rounded-lg bg-rose-50 border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 cursor-pointer"
                                        >
                                            <x-icon name="x" class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Tolak</span>
                                        </button>
                                    </div>
                                @else
                                    <a 
                                        href="{{ route('admin.orders.show', $payment->order) }}" 
                                        class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-slate-800 font-medium"
                                    >
                                        <span>Buka Order</span>
                                        <x-icon name="arrow-right" class="w-3 h-3 text-slate-400" />
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <x-icon name="credit-card" class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                                <p class="text-sm">Tidak ada transaksi pembayaran yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

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
                        placeholder="Contoh: Bukti buram / mutasi bank belum masuk..."
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

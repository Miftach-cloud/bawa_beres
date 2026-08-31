<div class="space-y-6">
    <!-- Back Link & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors font-bold text-lg">
                ←
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-mono font-extrabold text-slate-900 tracking-tight">
                        {{ $order->order_code }}
                    </h2>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold border {{ $order->status->badgeColor() }}">
                        {{ $order->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Dibuat pada {{ $order->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                </p>
            </div>
        </div>

        <!-- Next Status Action Buttons (State Machine Guard) -->
        <div class="flex flex-wrap items-center gap-2">
            @if (!$order->status->isFinal())
                @foreach ($allowedNextStatuses as $next)
                    @if ($next !== \App\Enums\OrderStatus::CANCELLED)
                        <button 
                            type="button" 
                            wire:click="openTransitionModal('{{ $next->value }}')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
                        >
                            <span>Ubah ke: {{ $next->label() }} ➔</span>
                        </button>
                    @endif
                @endforeach

                <button 
                    type="button" 
                    wire:click="openCancelModal"
                    class="inline-flex items-center gap-1 rounded-xl bg-rose-50 border border-rose-200 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition-all cursor-pointer"
                >
                    <span>✕ Batalkan Pesanan</span>
                </button>
            @else
                <span class="text-xs font-medium text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl">
                    Status Final (Tidak dapat diubah)
                </span>
            @endif
        </div>
    </div>

    <!-- Flash Alerts -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <span class="text-base">✅</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-center gap-3 text-xs font-medium text-rose-800">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Order Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Order Content & Entities (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Customer & Service Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Customer Card -->
                <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">👤 Pelanggan</span>
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-[11px] text-amber-600 hover:underline font-semibold">
                            Buka Profil ➔
                        </a>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $order->customer->name }}</div>
                        <div class="font-mono text-xs text-amber-600 mt-0.5">{{ $order->customer->customer_code }}</div>
                        <div class="mt-2 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center gap-2">
                                <span>📞</span>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->phone) }}" target="_blank" class="font-mono hover:text-amber-600 underline">
                                    {{ $order->customer->phone }}
                                </a>
                            </div>
                            @if ($order->customer->email)
                                <div class="flex items-center gap-2">
                                    <span>✉️</span>
                                    <span>{{ $order->customer->email }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Service Card -->
                <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">🛠️ Layanan Terpilih</span>
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                            {{ $order->service->pricing_type->label() }}
                        </span>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $order->service->name }}</div>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $order->service->description }}</p>
                        <div class="mt-3 text-xs text-slate-700 font-medium">
                            Tarif Dasar: <span class="font-mono font-bold text-slate-900">Rp {{ number_format($order->service->base_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Address Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Pickup Address -->
                <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs space-y-2">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider flex items-center gap-1.5">
                        <span>📍</span> Lokasi Penjemputan (Pickup)
                    </span>
                    @if ($order->pickupAddress)
                        <div class="text-xs text-slate-900 font-medium leading-relaxed">
                            {{ $order->pickupAddress->address }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Kecamatan {{ $order->pickupAddress->district ?? '-' }}, {{ $order->pickupAddress->city }}
                        </div>
                        @if ($order->pickupAddress->notes)
                            <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded-lg mt-2">
                                Catatan: {{ $order->pickupAddress->notes }}
                            </p>
                        @endif
                    @else
                        <p class="text-xs text-slate-400">Belum ada alamat penjemputan.</p>
                    @endif
                </div>

                <!-- Destination Address -->
                <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs space-y-2">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1.5">
                        <span>🏁</span> Lokasi Tujuan / Pengantaran
                    </span>
                    @if ($order->destinationAddress)
                        <div class="text-xs text-slate-900 font-medium leading-relaxed">
                            {{ $order->destinationAddress->address }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Kecamatan {{ $order->destinationAddress->district ?? '-' }}, {{ $order->destinationAddress->city }}
                        </div>
                        @if ($order->destinationAddress->notes)
                            <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded-lg mt-2">
                                Catatan: {{ $order->destinationAddress->notes }}
                            </p>
                        @endif
                    @else
                        <div class="text-xs text-slate-400 py-1">
                            Tidak membutuhkan pengantaran saat ini (Layanan Storage / Gudang).
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Declared Items Table (Section 2.5 & 5.2) -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span>📦</span> Daftar Barang Deklarasi Customer
                    </h3>
                    <span class="text-xs font-semibold text-slate-600 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                        {{ $order->items->count() }} Macam Barang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Nama Barang</th>
                                <th class="px-6 py-3">Estimasi Ukuran</th>
                                <th class="px-6 py-3">Jumlah (Qty)</th>
                                <th class="px-6 py-3">Catatan Kondisi/Spesifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($order->items as $item)
                                <tr>
                                    <td class="px-6 py-3.5 font-medium text-slate-900">
                                        {{ $item->name }}
                                        @if ($item->description)
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-700">
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 font-medium text-slate-600">
                                            {{ $item->estimated_size ?? 'Standar' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 font-bold text-slate-900">
                                        {{ $item->quantity }} unit
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500 italic">
                                        {{ $item->notes ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada barang yang dideklarasikan dalam order ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Notes & Pricing Admin Editor -->
            <div class="rounded-2xl bg-white p-6 border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">
                    📝 Catatan & Penetapan Biaya Order
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-semibold block mb-1">Catatan dari Pelanggan:</span>
                        <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-200/50 text-slate-700 leading-relaxed">
                            {{ $order->customer_notes ?: 'Tidak ada catatan dari pelanggan.' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-700 font-semibold block mb-1">Total Biaya Transaksi (Rp):</span>
                        <input 
                            type="number" 
                            wire:model="totalAmount" 
                            min="0" 
                            step="1000"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-bold text-slate-900 font-mono focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Internal Admin / Tim Lapangan</label>
                    <textarea 
                        wire:model="adminNotes" 
                        rows="3" 
                        placeholder="Contoh: Sudah dikonfirmasi via WA, butuh armada pickup grandmax pick-up, jam 09.00 WIB..."
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                    ></textarea>
                </div>

                <div class="flex justify-end">
                    <button 
                        type="button" 
                        wire:click="saveAdminNotes" 
                        class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition-all cursor-pointer"
                    >
                        💾 Simpan Catatan & Tarif
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Status Timeline & Modular Sections (1 col) -->
        <div class="space-y-6">
            <!-- 1. Audit History Log (Section 5.2 & 2.7) -->
            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">
                        ⏱️ Riwayat Perubahan Status
                    </h3>
                    <span class="text-[11px] text-slate-400">
                        {{ $order->statusHistories->count() }} log
                    </span>
                </div>

                <div class="flow-root">
                    <ul class="-mb-6 space-y-4">
                        @foreach ($order->statusHistories as $history)
                            <li class="relative flex gap-x-3 pb-4">
                                <div class="relative flex h-6 w-6 flex-none items-center justify-center bg-white">
                                    <div class="h-2 w-2 rounded-full bg-amber-500 ring-2 ring-amber-200"></div>
                                </div>
                                <div class="flex-auto rounded-xl bg-slate-50 p-3 text-xs border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-900">
                                            {{ $history->to_status->label() }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $history->created_at->translatedFormat('d M, H:i') }}
                                        </span>
                                    </div>
                                    @if ($history->from_status)
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            Dari: <span class="font-medium">{{ $history->from_status->label() }}</span>
                                        </div>
                                    @endif
                                    @if ($history->notes)
                                        <p class="mt-1 text-slate-600 italic bg-white p-2 rounded border border-slate-100">
                                            "{{ $history->notes }}"
                                        </p>
                                    @endif
                                    @if ($history->user)
                                        <div class="text-[10px] text-slate-400 mt-1">
                                            Oleh: {{ $history->user->name }}
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- 2. Quotation Management Module (Phase 6) -->
            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs">
                <livewire:admin.quotations.manager :order="$order" :key="'quotations-'.$order->id" />
            </div>


            <!-- 3. Payment Management Module (Phase 7) -->
            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs">
                <livewire:admin.payments.manager :order="$order" :key="'payments-'.$order->id" />
            </div>


            <!-- Schedule Module Scaffolding (Phase 8) -->
            <div class="rounded-2xl bg-white p-5 border border-dashed border-slate-300 shadow-xs space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                    <span>🚚 Jadwal & Alokasi Driver</span>
                    <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500">Fase 8</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Modul penentuan tanggal/jam penjemputan dan penugasan armada driver di Kota Malang.
                </p>
            </div>

            <!-- Inventory Module Scaffolding (Phase 9 & 10) -->
            <div class="rounded-2xl bg-white p-5 border border-dashed border-slate-300 shadow-xs space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                    <span>🏷️ Inventory, QR & Storage</span>
                    <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500">Fase 9-10</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Modul verifikasi fisik barang, cetak label QR, dokumentasi foto, dan alokasi rak storage gudang.
                </p>
            </div>
        </div>
    </div>

    <!-- Status Transition Modal -->
    @if ($showTransitionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">
                        Konfirmasi Perubahan Status
                    </h3>
                    <button type="button" wire:click="closeTransitionModal" class="text-slate-400 hover:text-slate-600 font-bold">
                        ✕
                    </button>
                </div>

                <div class="text-xs text-slate-600 space-y-2">
                    <p>
                        Ubah status pesanan <span class="font-mono font-bold text-slate-900">{{ $order->order_code }}</span> menjadi:
                    </p>
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 font-bold text-sm">
                        {{ \App\Enums\OrderStatus::tryFrom($targetStatus)?->label() }}
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Catatan Transisi Status (Opsional)
                    </label>
                    <textarea 
                        wire:model="transitionNotes" 
                        rows="3" 
                        placeholder="Contoh: Estimasi harga telah dikirimkan ke pelanggan via WhatsApp..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeTransitionModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmTransition" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer">
                        Konfirmasi Ubah Status
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Cancel Order Modal -->
    @if ($showCancelModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-rose-700 text-base flex items-center gap-2">
                        <span>⚠️</span> Batalkan Pesanan
                    </h3>
                    <button type="button" wire:click="closeCancelModal" class="text-slate-400 hover:text-slate-600 font-bold">
                        ✕
                    </button>
                </div>

                <p class="text-xs text-slate-600">
                    Pesanan yang dibatalkan tidak dapat dilanjutkan ke proses operasional berikutnya. Harap isi alasan pembatalan.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Pembatalan *
                    </label>
                    <textarea 
                        wire:model="cancelReason" 
                        rows="3" 
                        placeholder="Contoh: Customer membatalkan pindahan karena jadwal berubah..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-rose-500 focus:ring-1 focus:ring-rose-500"
                    ></textarea>
                    @error('cancelReason') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeCancelModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" wire:click="confirmCancel" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-500 active:scale-95 transition-all cursor-pointer">
                        Konfirmasi Batalkan Pesanan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="space-y-6">
    <!-- Back Link & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 shadow-xs transition-colors">
                ←
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900">{{ $customer->name }}</h2>
                    <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-0.5 text-xs font-mono font-bold text-amber-700 border border-amber-200">
                        {{ $customer->customer_code }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Terdaftar sejak {{ $customer->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
        </div>

        <button 
            type="button" 
            wire:click="openEditModal"
            class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-colors cursor-pointer"
        >
            <span>✏️ Edit Data Pelanggan</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <span class="text-base">✅</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Profile & Statistics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Info Card -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                Informasi Kontak
            </h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block mb-0.5">Nomor Telepon / WA:</span>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="font-semibold text-slate-900 hover:text-amber-600 flex items-center gap-1">
                        <span>📞 {{ $customer->phone }}</span>
                        <span class="text-[10px] text-emerald-600 font-normal">(Hubungi WA)</span>
                    </a>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Email:</span>
                    <span class="font-semibold text-slate-800">{{ $customer->email ?? 'Tidak dicantumkan' }}</span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Akun Login User:</span>
                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">
                        {{ $customer->user_id ? 'Terkait User ID #' . $customer->user_id : 'Guest / Tanpa Akun' }}
                    </span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Catatan Tambahan:</span>
                    <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 italic">
                        {{ $customer->notes ?: 'Tidak ada catatan khusus.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 3 Metric Cards for this Customer -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Transaksi</span>
                <span class="text-3xl font-extrabold text-slate-900 mt-2 block">{{ $stats['total_orders'] }}</span>
                <span class="text-[11px] text-slate-400 mt-1 block">Pesanan terdaftar</span>
            </div>

            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pesanan Selesai</span>
                <span class="text-3xl font-extrabold text-emerald-600 mt-2 block">{{ $stats['completed_orders'] }}</span>
                <span class="text-[11px] text-slate-400 mt-1 block">Transaksi sukses</span>
            </div>

            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Active Storage</span>
                <span class="text-3xl font-extrabold text-purple-600 mt-2 block">{{ $stats['active_storage'] }}</span>
                <span class="text-[11px] text-slate-400 mt-1 block">Barang di gudang</span>
            </div>

            <div class="sm:col-span-3 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 p-5 text-white shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Pengeluaran Pelanggan</span>
                        <div class="text-2xl font-bold font-mono text-amber-400 mt-1">
                            Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-3xl">💳</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Section (Section 4.3) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Riwayat Pesanan Pelanggan</h3>
                <p class="text-xs text-slate-500">Semua order yang pernah dibuat oleh {{ $customer->name }}</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                {{ $orders->count() }} Order
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode Order</th>
                        <th class="px-6 py-3.5">Layanan</th>
                        <th class="px-6 py-3.5">Status Pesanan</th>
                        <th class="px-6 py-3.5">Lokasi Penjemputan</th>
                        <th class="px-6 py-3.5">Total Biaya</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 text-xs">
                                {{ $order->order_code }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900">{{ $order->service->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border {{ $order->status->badgeColor() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 max-w-xs truncate">
                                {{ $order->pickupAddress?->address ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-semibold text-slate-800 text-xs">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📦</div>
                                <p class="text-sm">Pelanggan ini belum memiliki riwayat transaksi order.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-lg">
                        Edit Data Pelanggan
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 font-bold text-lg">
                        ✕
                    </button>
                </div>

                <form wire:submit="updateCustomer" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Pelanggan
                        </label>
                        <input 
                            type="text" 
                            wire:model="name"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Nomor Telepon / WA
                            </label>
                            <input 
                                type="text" 
                                wire:model="phone"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Email
                            </label>
                            <input 
                                type="email" 
                                wire:model="email"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan Khusus Pelanggan
                        </label>
                        <textarea 
                            wire:model="notes"
                            rows="3"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        ></textarea>
                        @error('notes') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="closeModal"
                            class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

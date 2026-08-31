<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Manajemen Pesanan (Orders)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pusat operasional transaksi layanan moving, storage, dan delivery</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <span>➕ Buat Pesanan Baru</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <span class="text-base">✅</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Filter Bar Card (Section 5.1) -->
    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari kode order / pelanggan / no HP..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                <span class="absolute left-3 top-2.5 text-xs text-slate-400">🔍</span>
            </div>

            <!-- Status Filter -->
            <div>
                <select 
                    wire:model.live="statusFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Status Pesanan</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Service Filter -->
            <div>
                <select 
                    wire:model.live="serviceFilter"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                    <option value="">Semua Jenis Layanan</option>
                    @foreach ($services as $srv)
                        <option value="{{ $srv->id }}">{{ $srv->name }}</option>
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

        <!-- Custom Date Range Picker (shown when custom is selected) -->
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
                Ditemukan <span class="font-bold text-slate-900">{{ $orders->total() }}</span> pesanan
            </div>
            @if ($search || $statusFilter || $serviceFilter || $dateFilter !== 'all')
                <button 
                    type="button" 
                    wire:click="resetFilters" 
                    class="text-amber-600 hover:text-amber-700 font-medium cursor-pointer"
                >
                    🔄 Reset Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Orders Table Container -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode Order</th>
                        <th class="px-6 py-3.5">Pelanggan</th>
                        <th class="px-6 py-3.5">Layanan</th>
                        <th class="px-6 py-3.5">Status Pesanan</th>
                        <th class="px-6 py-3.5">Lokasi Penjemputan</th>
                        <th class="px-6 py-3.5">Total Biaya</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 text-xs">
                                <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">
                                    {{ $order->order_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.customers.show', $order->customer) }}" class="font-semibold text-slate-900 hover:text-amber-600 block">
                                    {{ $order->customer->name }}
                                </a>
                                <div class="text-xs text-slate-500 font-mono">{{ $order->customer->phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                {{ $order->service->name }}
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
                            <td class="px-6 py-4 text-right">
                                <a 
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-colors"
                                >
                                    <span>👁️ Buka Order</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📦</div>
                                <p class="text-sm">Tidak ada pesanan yang sesuai dengan filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Create Order Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-lg">
                        Buat Pesanan Baru (Create Order)
                    </h3>
                    <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit="createOrder" class="space-y-6">
                    <!-- 1. Customer Section -->
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">1. Data Pelanggan</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Pilih Pelanggan Terdaftar (Opsional)</label>
                                <select wire:model.live="selectedCustomerId" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900">
                                    <option value="">-- Buat Pelanggan Baru --</option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                                    @endforeach
                                </select>
                            </div>

                            @if (!$selectedCustomerId)
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                                    <input type="text" wire:model="newCustomerName" placeholder="Contoh: Rian Anggara" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900">
                                    @error('newCustomerName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">No WhatsApp / Telepon *</label>
                                    <input type="text" wire:model="newCustomerPhone" placeholder="081234567890" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900">
                                    @error('newCustomerPhone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Email (Opsional)</label>
                                    <input type="email" wire:model="newCustomerEmail" placeholder="rian@example.com" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Service Selection -->
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">2. Pilih Layanan</h4>

                        <div>
                            <select wire:model="selectedServiceId" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900">
                                <option value="">-- Pilih Jenis Layanan --</option>
                                @foreach ($services as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->pricing_type->label() }} - Rp {{ number_format($srv->base_price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('selectedServiceId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 3. Address Details -->
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">3. Alamat Lokasi (Kota Malang)</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-700 mb-1">Alamat Penjemputan (Pickup) *</label>
                                <input type="text" wire:model="pickupAddress" placeholder="Jl. Bendungan Sigura-gura No. 10" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900">
                                @error('pickupAddress') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Kecamatan Pickup</label>
                                <select wire:model="pickupDistrict" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900">
                                    <option value="Lowokwaru">Lowokwaru</option>
                                    <option value="Klojen">Klojen</option>
                                    <option value="Blimbing">Blimbing</option>
                                    <option value="Sukun">Sukun</option>
                                    <option value="Kedungkandang">Kedungkandang</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-700 mb-1">Alamat Tujuan (Optional jika Storage)</label>
                                <input type="text" wire:model="destinationAddress" placeholder="Jl. Ijen No. 12" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Kecamatan Tujuan</label>
                                <select wire:model="destinationDistrict" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900">
                                    <option value="Lowokwaru">Lowokwaru</option>
                                    <option value="Klojen">Klojen</option>
                                    <option value="Blimbing">Blimbing</option>
                                    <option value="Sukun">Sukun</option>
                                    <option value="Kedungkandang">Kedungkandang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Items Declaration -->
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">4. Deklarasi Barang (Items)</h4>
                            <button type="button" wire:click="addItemRow" class="text-xs text-amber-600 font-semibold hover:underline cursor-pointer">
                                ➕ Tambah Baris Barang
                            </button>
                        </div>

                        @foreach ($items as $index => $item)
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 bg-white p-3 rounded-lg border border-slate-200 items-center">
                                <div class="sm:col-span-5">
                                    <input type="text" wire:model="items.{{ $index }}.name" placeholder="Nama Barang (contoh: Kasur Single, Kardus Baju)" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900">
                                </div>
                                <div class="sm:col-span-2">
                                    <input type="number" wire:model="items.{{ $index }}.quantity" min="1" placeholder="Qty" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900">
                                </div>
                                <div class="sm:col-span-3">
                                    <select wire:model="items.{{ $index }}.estimated_size" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs text-slate-900">
                                        <option value="Kecil">Kecil</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Besar">Besar</option>
                                        <option value="Fragile">Fragile / Pecah Belah</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2 text-right">
                                    <button type="button" wire:click="removeItemRow({{ $index }})" class="text-rose-500 hover:text-rose-700 text-xs font-semibold cursor-pointer">
                                        🗑️ Hapus
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        @error('items.0.name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 5. Notes -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Catatan Khusus dari Customer</label>
                        <textarea wire:model="customerNotes" rows="2" placeholder="Contoh: Kost lantai 2, butuh bantuan angkut lemari..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeCreateModal" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-amber-400 active:scale-95 transition-all cursor-pointer">
                            Buat Pesanan & Buka Detail
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

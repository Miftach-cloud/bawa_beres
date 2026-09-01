<div class="space-y-8">
    <!-- Welcome Greeting & Quick Status Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 p-6 sm:p-8 text-white shadow-sm border border-slate-700/50">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-3 py-1 text-xs font-semibold text-amber-400 border border-amber-500/30 mb-2">
                    <span>📍</span> Operasional Kota Malang
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Halo, {{ auth()->user()->name }}! 👋
                </h2>
                <p class="text-sm text-slate-300 mt-1">
                    Ringkasan aktivitas operasional moving, storage & delivery hari ini.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 5 Core Metric Cards (Section 3.5) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. New Orders -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">New Orders</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 text-amber-700 text-sm">
                    🆕
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $metrics['new_orders'] }}</span>
                <p class="text-[11px] text-slate-500 mt-1">Menunggu review admin</p>
            </div>
        </div>

        <!-- 2. Orders Today -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Orders Today</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-700 text-sm">
                    📅
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $metrics['orders_today'] }}</span>
                <p class="text-[11px] text-slate-500 mt-1">Dibuat hari ini</p>
            </div>
        </div>

        <!-- 3. Scheduled Pickups -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Scheduled</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700 text-sm">
                    🚚
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $metrics['scheduled_pickups'] }}</span>
                <p class="text-[11px] text-slate-500 mt-1">Jadwal pickup & delivery</p>
            </div>
        </div>

        <!-- 4. Active Storage -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Storage</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-100 text-purple-700 text-sm">
                    🏢
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $metrics['active_storage'] }}</span>
                <p class="text-[11px] text-slate-500 mt-1">Barang di gudang</p>
            </div>
        </div>

        <!-- 5. Pending Payment -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Pay</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-sm">
                    💳
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $metrics['pending_payment'] }}</span>
                <p class="text-[11px] text-slate-500 mt-1">Quotation terkirim</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Pesanan Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar transaksi masuk dan status terkini</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode Order</th>
                        <th class="px-6 py-3.5">Pelanggan</th>
                        <th class="px-6 py-3.5">Layanan</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Lokasi Pickup</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-semibold text-amber-600">
                                {{ $order->order_code }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $order->customer->name }}</div>
                                <div class="text-xs text-slate-500">{{ $order->customer->phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
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
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📭</div>
                                <p class="text-sm">Belum ada pesanan masuk dalam sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

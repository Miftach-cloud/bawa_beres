<div class="space-y-8">
    {{-- Header Banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-violet-900 to-indigo-900 p-6 sm:p-8 text-white shadow-sm border border-violet-700/50">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-500/20 px-3 py-1 text-xs font-semibold text-violet-300 border border-violet-500/30 mb-2">
                    <x-icon name="chart-bar" class="w-3.5 h-3.5 text-violet-300" />
                    <span>Dashboard Analisa — Owner Only</span>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Laporan Bisnis
                </h2>
                <p class="text-sm text-violet-200 mt-1">
                    Ringkasan kinerja keuangan & operasional Bawa Beres.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-violet-300 bg-violet-800/80 px-3 py-1.5 rounded-xl border border-violet-700">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- KPI Row 1: Revenue & Orders Growth --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Revenue This Month --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Revenue Bulan Ini</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100">
                    <x-icon name="credit-card" class="w-4 h-4 text-emerald-600" />
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900">
                    Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                </span>
                <div class="flex items-center gap-1 mt-1">
                    @if ($revenueGrowth >= 0)
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600">
                            <x-icon name="chevron-up" class="w-3 h-3" />
                            +{{ $revenueGrowth }}%
                        </span>
                    @else
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-rose-600">
                            <x-icon name="chevron-down" class="w-3 h-3" />
                            {{ $revenueGrowth }}%
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400">vs bulan lalu</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Bulan lalu: Rp {{ number_format($revenueLastMonth, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Orders This Month --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Order Bulan Ini</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100">
                    <x-icon name="box" class="w-4 h-4 text-blue-600" />
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $ordersThisMonth }}</span>
                <div class="flex items-center gap-1 mt-1">
                    @if ($ordersGrowth >= 0)
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600">
                            <x-icon name="chevron-up" class="w-3 h-3" />
                            +{{ $ordersGrowth }}%
                        </span>
                    @else
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-rose-600">
                            <x-icon name="chevron-down" class="w-3 h-3" />
                            {{ $ordersGrowth }}%
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400">vs bulan lalu</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Bulan lalu: {{ $ordersLastMonth }} order
                </p>
            </div>
        </div>

        {{-- New Customers --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan Baru</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-100">
                    <x-icon name="users" class="w-4 h-4 text-indigo-600" />
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $newCustomersThisMonth }}</span>
                <p class="text-[11px] text-slate-400 mt-1">
                    Bulan lalu: {{ $newCustomersLastMonth }} pelanggan baru
                </p>
            </div>
        </div>

        {{-- Completion Rate --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completion Rate</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100">
                    <x-icon name="tag" class="w-4 h-4 text-amber-600" />
                </span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $completionRate }}%</span>
                <p class="text-[11px] text-slate-400 mt-1">
                    {{ $completedThisMonth }} order selesai bulan ini
                </p>
            </div>
        </div>
    </div>

    {{-- Revenue Trend 6 Months --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">Tren Revenue 6 Bulan Terakhir</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pendapatan dari pembayaran terverifikasi per bulan</p>
        </div>
        <div class="p-6">
            @php
                $maxRevenue = $revenueTrend->max('revenue') ?: 1;
            @endphp
            <div class="flex items-end gap-3 h-40">
                @foreach ($revenueTrend as $month)
                    @php
                        $heightPct = $maxRevenue > 0 ? max(4, round(($month['revenue'] / $maxRevenue) * 100)) : 4;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full flex flex-col items-center justify-end" style="height: 120px;">
                            <div
                                class="w-full rounded-t-lg bg-gradient-to-t from-violet-600 to-indigo-400 transition-all"
                                style="height: {{ $heightPct }}%"
                                title="Rp {{ number_format($month['revenue'], 0, ',', '.') }}"
                            ></div>
                        </div>
                        <span class="text-[10px] font-medium text-slate-500 text-center leading-tight">{{ $month['label'] }}</span>
                        <span class="text-[10px] font-bold text-slate-700">
                            {{ $month['revenue'] > 0 ? 'Rp '.number_format($month['revenue'] / 1000000, 1, ',', '.').'jt' : '-' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bottom Row: Service Distribution & Top Customers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Order by Service --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Distribusi Order per Layanan</h3>
                <p class="text-xs text-slate-500 mt-0.5">6 bulan terakhir, tidak termasuk order dibatalkan & draft</p>
            </div>
            <div class="p-6">
                @if ($ordersByService->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-4">Belum ada data order.</p>
                @else
                    @php $totalOrders = $ordersByService->sum('total'); @endphp
                    <div class="space-y-3">
                        @foreach ($ordersByService->sortByDesc('total') as $row)
                            @php
                                $pct = $totalOrders > 0 ? round(($row['total'] / $totalOrders) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700">{{ $row['service'] }}</span>
                                    <span class="text-xs font-bold text-slate-500">{{ $row['total'] }} order ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div
                                        class="h-2 rounded-full bg-gradient-to-r from-violet-500 to-indigo-400"
                                        style="width: {{ $pct }}%"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Top 5 Pelanggan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Berdasarkan jumlah order aktif (sepanjang waktu)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Pelanggan</th>
                            <th class="px-6 py-3 text-right">Order</th>
                            <th class="px-6 py-3 text-right">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($topCustomers as $i => $customer)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $i + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $customer->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $customer->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-700">
                                    {{ $customer->completed_orders }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-600">
                                    @if ($customer->total_revenue)
                                        Rp {{ number_format($customer->total_revenue, 0, ',', '.') }}
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                    <p class="text-sm">Belum ada data pelanggan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

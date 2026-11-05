<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard Analisa — Bawa Beres')]
class DashboardAnalisa extends Component
{
    public function mount(): void
    {
        Gate::authorize('view-analytics');
    }

    public function render()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Revenue this month vs last month
        $revenueThisMonth = Payment::query()
            ->where('status', PaymentStatus::PAID)
            ->whereBetween('paid_at', [$startOfMonth, $now])
            ->sum('amount');

        $revenueLastMonth = Payment::query()
            ->where('status', PaymentStatus::PAID)
            ->whereBetween('paid_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // Orders this month vs last month
        $ordersThisMonth = Order::query()
            ->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::DRAFT])
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->count();

        $ordersLastMonth = Order::query()
            ->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::DRAFT])
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $ordersGrowth = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : ($ordersThisMonth > 0 ? 100 : 0);

        // Total customers this month
        $newCustomersThisMonth = Customer::query()
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->count();

        $newCustomersLastMonth = Customer::query()
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        // Completion rate (COMPLETED orders / total non-draft, non-cancelled this month)
        $completedThisMonth = Order::query()
            ->where('status', OrderStatus::COMPLETED)
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->count();

        $completionRate = $ordersThisMonth > 0
            ? round(($completedThisMonth / $ordersThisMonth) * 100, 1)
            : 0;

        // Distribution by service
        $ordersByService = Order::query()
            ->with('service')
            ->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::DRAFT])
            ->whereBetween('created_at', [$startOfMonth->copy()->subMonths(5)->startOfMonth(), $now])
            ->selectRaw('service_id, COUNT(*) as total')
            ->groupBy('service_id')
            ->with('service:id,name')
            ->get()
            ->map(fn ($row) => [
                'service' => $row->service?->name ?? 'Unknown',
                'total' => $row->total,
            ]);

        // Top 5 customers by total orders (all time)
        $topCustomers = Customer::query()
            ->withCount(['orders as completed_orders' => function ($q) {
                $q->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::DRAFT]);
            }])
            ->withSum(['orders as total_revenue' => function ($q) {
                $q->whereNotNull('total_amount');
            }], 'total_amount')
            ->orderByDesc('completed_orders')
            ->take(5)
            ->get();

        // Revenue trend — last 6 months
        $revenueTrend = collect(range(5, 0))->map(function (int $monthsAgo) {
            $month = Carbon::now()->subMonths($monthsAgo);

            return [
                'label' => $month->translatedFormat('M Y'),
                'revenue' => (float) Payment::query()
                    ->where('status', PaymentStatus::PAID)
                    ->whereBetween('paid_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->sum('amount'),
                'orders' => Order::query()
                    ->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::DRAFT])
                    ->whereBetween('created_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->count(),
            ];
        });

        return view('livewire.admin.dashboard-analisa', [
            'revenueThisMonth' => (float) $revenueThisMonth,
            'revenueLastMonth' => (float) $revenueLastMonth,
            'revenueGrowth' => $revenueGrowth,
            'ordersThisMonth' => $ordersThisMonth,
            'ordersLastMonth' => $ordersLastMonth,
            'ordersGrowth' => $ordersGrowth,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'newCustomersLastMonth' => $newCustomersLastMonth,
            'completionRate' => $completionRate,
            'completedThisMonth' => $completedThisMonth,
            'ordersByService' => $ordersByService,
            'topCustomers' => $topCustomers,
            'revenueTrend' => $revenueTrend,
        ]);
    }
}

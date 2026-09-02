<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard Operasional — Bawa Beres')]
class Dashboard extends Component
{
    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function render()
    {
        $today = Carbon::today();

        $metrics = [
            'new_orders' => Order::query()
                ->whereIn('status', [OrderStatus::PENDING_REVIEW, OrderStatus::SUBMITTED, OrderStatus::DRAFT])
                ->count(),

            'orders_today' => Order::query()
                ->whereDate('created_at', $today)
                ->count(),

            'scheduled_pickups' => Order::query()
                ->whereIn('status', [OrderStatus::SCHEDULED, OrderStatus::CONFIRMED])
                ->count(),

            'active_storage' => Order::query()
                ->where('status', OrderStatus::STORED)
                ->count(),

            'pending_payment' => Order::query()
                ->whereIn('status', [OrderStatus::QUOTED, OrderStatus::CONFIRMED])
                ->count(),
        ];

        $recentOrders = Order::query()
            ->with(['customer', 'service', 'pickupAddress', 'destinationAddress'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('livewire.admin.dashboard', [
            'metrics' => $metrics,
            'recentOrders' => $recentOrders,
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Orders;

use App\Actions\Orders\CreateOrder;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manajemen Order — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';

    public string $statusFilter = '';

    public string $serviceFilter = '';

    public string $dateFilter = 'all'; // all, today, this_week, this_month, custom

    public ?string $startDate = null;

    public ?string $endDate = null;

    // Create Order Modal State
    public bool $showCreateModal = false;

    public ?int $selectedCustomerId = null;

    public string $newCustomerName = '';

    public string $newCustomerPhone = '';

    public string $newCustomerEmail = '';

    public ?int $selectedServiceId = null;

    public string $customerNotes = '';

    public string $pickupAddress = '';

    public string $pickupDistrict = 'Lowokwaru';

    public string $destinationAddress = '';

    public string $destinationDistrict = 'Klojen';

    public array $items = [];

    public function mount(): void
    {
        Gate::authorize('manage-orders');
        $this->pickupDistrict = (string) config('business.operations.default_district', 'Lowokwaru');
        $this->destinationDistrict = (string) config('business.operations.default_district', 'Lowokwaru');
        $this->addItemRow();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingServiceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function addItemRow(): void
    {
        $this->items[] = [
            'name' => '',
            'description' => '',
            'quantity' => 1,
            'estimated_size' => 'Sedang',
            'notes' => '',
        ];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItemRow();
        }
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'selectedCustomerId', 'newCustomerName', 'newCustomerPhone', 'newCustomerEmail',
            'selectedServiceId', 'customerNotes', 'pickupAddress', 'destinationAddress',
        ]);
        $this->items = [];
        $this->addItemRow();
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createOrder(CreateOrder $createOrderAction)
    {
        Gate::authorize('manage-orders');

        $this->validate([
            'selectedServiceId' => 'required|exists:services,id',
            'selectedCustomerId' => 'nullable|exists:customers,id',
            'newCustomerName' => 'required_without:selectedCustomerId|string|max:255',
            'newCustomerPhone' => 'required_without:selectedCustomerId|string|max:30',
            'pickupAddress' => 'required|string|max:500',
            'pickupDistrict' => 'required|string|max:100',
            'items.0.name' => 'required|string|max:255',
        ]);

        $payload = [
            'service_id' => $this->selectedServiceId,
            'customer_id' => $this->selectedCustomerId,
            'customer_name' => $this->newCustomerName,
            'customer_phone' => $this->newCustomerPhone,
            'customer_email' => $this->newCustomerEmail ?: null,
            'customer_notes' => $this->customerNotes ?: null,
            'status' => OrderStatus::PENDING_REVIEW,
            'pickup_address' => [
                'address' => $this->pickupAddress,
                'city' => config('business.address.city', 'Kota Malang'),
                'district' => $this->pickupDistrict,
            ],
            'destination_address' => $this->destinationAddress ? [
                'address' => $this->destinationAddress,
                'city' => config('business.address.city', 'Kota Malang'),
                'district' => $this->destinationDistrict,
            ] : null,
            'items' => array_filter($this->items, fn ($i) => ! empty($i['name'])),
        ];

        $order = $createOrderAction->execute($payload);

        $this->showCreateModal = false;
        session()->flash('message', "Pesanan baru #{$order->order_code} berhasil dibuat.");

        return redirect()->route('admin.orders.show', $order);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'serviceFilter', 'dateFilter', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::query()
            ->with(['customer', 'service', 'pickupAddress', 'destinationAddress']);

        // Search Filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_code', 'LIKE', "%{$this->search}%")
                    ->orWhereHas('customer', function ($cq) {
                        $cq->where('name', 'LIKE', "%{$this->search}%")
                            ->orWhere('phone', 'LIKE', "%{$this->search}%")
                            ->orWhere('customer_code', 'LIKE', "%{$this->search}%");
                    });
            });
        }

        // Status Filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Service Filter
        if ($this->serviceFilter) {
            $query->where('service_id', $this->serviceFilter);
        }

        // Date Filter
        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->dateFilter === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->dateFilter === 'this_month') {
            $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($this->dateFilter === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $orders = $query->latest('id')->paginate(10);

        return view('livewire.admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
            'services' => Service::query()->active()->get(),
            'customers' => Customer::query()->latest('id')->take(50)->get(),
        ]);
    }
}

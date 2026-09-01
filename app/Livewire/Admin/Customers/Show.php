<?php

namespace App\Livewire\Admin\Customers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Detail Pelanggan — Admin Bawa Beres')]
class Show extends Component
{
    public Customer $customer;
    public bool $showEditModal = false;

    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(Customer $customer): void
    {
        Gate::authorize('manage-customers');
        $this->customer = $customer->loadMissing(['orders.service', 'orders.pickupAddress', 'orders.destinationAddress']);
    }

    public function openEditModal(): void
    {
        $this->name = $this->customer->name;
        $this->phone = $this->customer->phone;
        $this->email = $this->customer->email ?? '';
        $this->notes = $this->customer->notes ?? '';
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function updateCustomer(): void
    {
        Gate::authorize('manage-customers');

        $validated = $this->validate();
        $this->customer->update($validated);

        $this->showEditModal = false;
        session()->flash('message', 'Data pelanggan berhasil diperbarui.');
    }

    public function render()
    {
        $orders = $this->customer->orders()->with(['service', 'pickupAddress', 'destinationAddress'])->latest('id')->get();

        $stats = [
            'total_orders' => $orders->count(),
            'completed_orders' => $orders->where('status', OrderStatus::COMPLETED)->count(),
            'active_storage' => $orders->where('status', OrderStatus::STORED)->count(),
            'total_spent' => $orders->sum('total_amount'),
        ];

        return view('livewire.admin.customers.show', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Data Pelanggan — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

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

    public function mount(): void
    {
        Gate::authorize('manage-customers');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'name', 'phone', 'email', 'notes']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(Customer $customer): void
    {
        $this->editingId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->email = $customer->email ?? '';
        $this->notes = $customer->notes ?? '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        Gate::authorize('manage-customers');

        $validated = $this->validate();

        if ($this->editingId) {
            $customer = Customer::findOrFail($this->editingId);
            $customer->update($validated);
            session()->flash('message', "Data pelanggan {$customer->name} berhasil diperbarui.");
        } else {
            $customer = Customer::create($validated);
            session()->flash('message', "Pelanggan baru {$customer->name} ({$customer->customer_code}) berhasil ditambahkan.");
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'phone', 'email', 'notes']);
    }

    public function render()
    {
        $customers = Customer::query()
            ->withCount('orders')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'LIKE', "%{$this->search}%")
                        ->orWhere('customer_code', 'LIKE', "%{$this->search}%")
                        ->orWhere('phone', 'LIKE', "%{$this->search}%")
                        ->orWhere('email', 'LIKE', "%{$this->search}%");
                });
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.admin.customers.index', [
            'customers' => $customers,
        ]);
    }
}

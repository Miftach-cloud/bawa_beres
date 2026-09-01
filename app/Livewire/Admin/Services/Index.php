<?php

namespace App\Livewire\Admin\Services;

use App\Enums\PricingType;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Katalog Layanan — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $pricing_type = 'QUOTATION';
    public float|string $base_price = 0;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'slug')->ignore($this->editingId),
            ],
            'description' => 'nullable|string',
            'pricing_type' => ['required', Rule::enum(PricingType::class)],
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('manage-services');
    }

    public function updatedName($value): void
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'name', 'slug', 'description', 'base_price']);
        $this->pricing_type = PricingType::QUOTATION->value;
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(Service $service): void
    {
        $this->editingId = $service->id;
        $this->name = $service->name;
        $this->slug = $service->slug;
        $this->description = $service->description ?? '';
        $this->pricing_type = $service->pricing_type->value;
        $this->base_price = (float) $service->base_price;
        $this->is_active = (bool) $service->is_active;
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
        Gate::authorize('manage-services');

        $validated = $this->validate();

        if ($this->editingId) {
            $service = Service::findOrFail($this->editingId);
            $service->update($validated);
            session()->flash('message', 'Layanan berhasil diperbarui.');
        } else {
            Service::create($validated);
            session()->flash('message', 'Layanan baru berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'slug', 'description', 'base_price']);
    }

    public function toggleStatus(int $serviceId): void
    {
        Gate::authorize('manage-services');

        $service = Service::findOrFail($serviceId);
        $service->update([
            'is_active' => !$service->is_active,
        ]);

        $statusText = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Layanan {$service->name} berhasil {$statusText}.");
    }

    public function render()
    {
        $services = Service::query()
            ->withCount('orders')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'LIKE', "%{$this->search}%")
                        ->orWhere('slug', 'LIKE', "%{$this->search}%")
                        ->orWhere('description', 'LIKE', "%{$this->search}%");
                });
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.admin.services.index', [
            'services' => $services,
            'pricingTypes' => PricingType::cases(),
        ]);
    }
}

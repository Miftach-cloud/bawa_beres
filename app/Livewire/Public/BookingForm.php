<?php

namespace App\Livewire\Public;

use App\Actions\Orders\CreateOrder;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookingForm extends Component
{
    use WithFileUploads;

    // Customer info (No registration required - frictionless)
    public string $customerName = '';

    public string $customerPhone = '';

    public ?string $customerEmail = '';

    // Service selection
    public ?int $serviceId = null;

    // Pickup address
    public string $pickupAddress = '';

    public string $pickupCity = 'Kota Malang';

    public string $pickupNotes = '';

    // Destination address
    public string $destinationAddress = '';

    public string $destinationCity = 'Kota Malang';

    public string $destinationNotes = '';

    // Item List
    public array $items = [
        ['name' => '', 'category' => 'Sedang', 'quantity' => 1, 'notes' => ''],
    ];

    // Photos
    public $photos = [];

    // Preferred Date & Notes
    public ?string $preferredDate = '';

    public string $customerNotes = '';

    // Submission outcome
    public bool $isSubmitted = false;

    public ?Order $createdOrder = null;

    public function mount(): void
    {
        $defaultService = Service::where('is_active', true)->first();
        if ($defaultService) {
            $this->serviceId = $defaultService->id;
        }
        $this->preferredDate = now()->addDay()->format('Y-m-d');
    }

    public function addItem(): void
    {
        $this->items[] = [
            'name' => '',
            'category' => 'Sedang',
            'quantity' => 1,
            'notes' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    protected function rules(): array
    {
        $selectedService = Service::find($this->serviceId);
        $requiresDestination = $selectedService && ! str_contains(strtolower($selectedService->name), 'storage') && ! str_contains(strtolower($selectedService->name), 'titip');

        return [
            'customerName' => 'required|string|min:3|max:100',
            'customerPhone' => 'required|string|min:8|max:20',
            'customerEmail' => 'nullable|email|max:100',
            'serviceId' => 'required|exists:services,id',
            'pickupAddress' => 'required|string|min:5|max:255',
            'pickupCity' => 'required|string|max:50',
            'destinationAddress' => $requiresDestination ? 'required|string|min:5|max:255' : 'nullable|string|max:255',
            'destinationCity' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|min:2|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'preferredDate' => 'required|date|after_or_equal:today',
            'customerNotes' => 'nullable|string|max:500',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'customerName.required' => 'Nama lengkap pemesan wajib diisi.',
            'customerPhone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'serviceId.required' => 'Silakan pilih layanan yang diinginkan.',
            'pickupAddress.required' => 'Alamat penjemputan / asal wajib diisi.',
            'destinationAddress.required' => 'Alamat tujuan wajib diisi untuk layanan pindahan/delivery.',
            'items.*.name.required' => 'Nama barang deklarasi tidak boleh kosong.',
            'preferredDate.required' => 'Tanggal penjemputan yang diinginkan wajib dipilih.',
            'preferredDate.after_or_equal' => 'Tanggal penjemputan minimal hari ini.',
        ];
    }

    public function submit(CreateOrder $createOrderAction): void
    {
        $this->validate();

        // 1. Prepare Order Payload
        $payload = [
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'customer_email' => $this->customerEmail ?: null,
            'service_id' => $this->serviceId,
            'status' => OrderStatus::PENDING_REVIEW,
            'preferred_date' => $this->preferredDate,
            'customer_notes' => $this->customerNotes ?: null,
            'items' => $this->items,
            'pickup_address' => [
                'address' => $this->pickupAddress,
                'city' => $this->pickupCity,
                'notes' => $this->pickupNotes ?: null,
            ],
        ];

        if (! empty($this->destinationAddress)) {
            $payload['destination_address'] = [
                'address' => $this->destinationAddress,
                'city' => $this->destinationCity,
                'notes' => $this->destinationNotes ?: null,
            ];
        }

        // 2. Execute Order Creation Action
        $order = $createOrderAction->execute($payload);

        // 3. Process uploaded photos if any
        if (! empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store("orders/{$order->id}/estimation", 'local');
            }
        }

        $this->createdOrder = $order;
        $this->isSubmitted = true;
    }

    public function resetBooking(): void
    {
        $this->isSubmitted = false;
        $this->createdOrder = null;
        $this->items = [
            ['name' => '', 'category' => 'Sedang', 'quantity' => 1, 'notes' => ''],
        ];
        $this->photos = [];
        $this->pickupAddress = '';
        $this->destinationAddress = '';
        $this->customerNotes = '';
    }

    public function render()
    {
        $services = Service::where('is_active', true)->get();
        $selectedService = Service::find($this->serviceId);
        $isStorageService = $selectedService && (str_contains(strtolower($selectedService->name), 'storage') || str_contains(strtolower($selectedService->name), 'titip'));

        return view('livewire.public.booking-form', [
            'services' => $services,
            'selectedService' => $selectedService,
            'isStorageService' => $isStorageService,
        ]);
    }
}

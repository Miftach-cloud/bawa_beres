<?php

namespace Tests\Feature;

use App\Livewire\Public\BookingForm;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServiceCapabilityConfigurationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function moving_service_requires_destination_address(): void
    {
        $movingService = Service::factory()->moving()->create([
            'name' => 'Layanan Angkut Barang',
        ]);

        Livewire::test(BookingForm::class)
            ->set('customerName', 'Budi Santoso')
            ->set('customerPhone', '081234567890')
            ->set('serviceId', $movingService->id)
            ->set('pickupAddress', 'Jl. Sukarno Hatta No. 10')
            ->set('pickupCity', 'Kota Malang')
            ->set('destinationAddress', '') // Missing
            ->set('preferredDate', now()->addDay()->toDateString())
            ->set('items', [
                ['name' => 'Kasur Springbed', 'category' => 'Besar', 'quantity' => 1, 'notes' => ''],
            ])
            ->call('submit')
            ->assertHasErrors(['destinationAddress']);
    }

    #[Test]
    public function delivery_service_requires_destination_address(): void
    {
        $deliveryService = Service::factory()->delivery()->create([
            'name' => 'Ekspedisi Kilat Lokal',
        ]);

        Livewire::test(BookingForm::class)
            ->set('customerName', 'Siti Rahma')
            ->set('customerPhone', '081987654321')
            ->set('serviceId', $deliveryService->id)
            ->set('pickupAddress', 'Jl. Ijen No. 5')
            ->set('pickupCity', 'Kota Malang')
            ->set('destinationAddress', '') // Missing
            ->set('preferredDate', now()->addDay()->toDateString())
            ->set('items', [
                ['name' => 'Dokumen Penting', 'category' => 'Kecil', 'quantity' => 1, 'notes' => ''],
            ])
            ->call('submit')
            ->assertHasErrors(['destinationAddress']);
    }

    #[Test]
    public function storage_service_does_not_require_destination_address(): void
    {
        $storageService = Service::factory()->storage()->create([
            'name' => 'Loker Box Kampus', // Service name without "storage" or "titip" keywords
        ]);

        Livewire::test(BookingForm::class)
            ->set('customerName', 'Dewi Lestari')
            ->set('customerPhone', '085712345678')
            ->set('serviceId', $storageService->id)
            ->set('pickupAddress', 'Jl. MT Haryono No. 100')
            ->set('pickupCity', 'Kota Malang')
            ->set('destinationAddress', '') // Omitted intentionally for storage
            ->set('preferredDate', now()->addDay()->toDateString())
            ->set('items', [
                ['name' => 'Box Pakaian 1', 'category' => 'Sedang', 'quantity' => 1, 'notes' => ''],
            ])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('orders', [
            'service_id' => $storageService->id,
        ]);
        $this->assertDatabaseHas('customers', [
            'name' => 'Dewi Lestari',
        ]);
    }

    #[Test]
    public function renaming_service_does_not_break_configured_business_behavior(): void
    {
        // Create storage service with arbitrary name
        $service = Service::factory()->storage()->create([
            'name' => 'Solusi Simpan Gudang',
        ]);

        // Rename service to something completely different
        $service->update(['name' => 'Paket Premium Mahasiswa Unik']);

        // Assert booking validation still honors requires_destination = false
        Livewire::test(BookingForm::class)
            ->set('customerName', 'Rizky Pratama')
            ->set('customerPhone', '081223344556')
            ->set('serviceId', $service->id)
            ->set('pickupAddress', 'Jl. Borobudur No. 25')
            ->set('pickupCity', 'Kota Malang')
            ->set('destinationAddress', '') // Allowed because requires_destination = false
            ->set('preferredDate', now()->addDay()->toDateString())
            ->set('items', [
                ['name' => 'Kardus Buku', 'category' => 'Sedang', 'quantity' => 1, 'notes' => ''],
            ])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);
    }
}

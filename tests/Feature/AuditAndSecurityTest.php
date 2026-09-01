<?php

namespace Tests\Feature;

use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Orders\ChangeOrderStatus;
use App\Actions\Orders\CreateOrder;
use App\Actions\Payments\VerifyPayment;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Enums\InventoryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Enums\UserRole;
use App\Livewire\Public\BookingForm;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuditAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;
    protected User $operation;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'name' => 'Owner BawaBeres',
            'email' => 'owner@bawaberes.id',
            'password' => 'secret123',
            'role' => UserRole::OWNER,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Staff',
            'email' => 'admin@bawaberes.id',
            'password' => 'secret123',
            'role' => UserRole::ADMIN,
        ]);

        $this->operation = User::factory()->create([
            'name' => 'Field Operation Staff',
            'email' => 'operation@bawaberes.id',
            'password' => 'secret123',
            'role' => UserRole::OPERATION,
        ]);

        $this->service = Service::create([
            'name' => 'Jasa Pindahan Rumah',
            'description' => 'Layanan pindahan lengkap dengan armada.',
            'base_price' => 500000,
            'is_active' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function audit_trail_records_actor_and_timestamp_for_order_status_changes(): void
    {
        $customer = Customer::create(['name' => 'Budi Santoso', 'phone' => '081234567890']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        $changeStatus = app(ChangeOrderStatus::class);
        $changeStatus->execute($order, OrderStatus::CONFIRMED, 'Dikonfirmasi admin.', $this->admin);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => OrderStatus::PENDING_REVIEW->value,
            'to_status' => OrderStatus::CONFIRMED->value,
            'changed_by' => $this->admin->id,
            'notes' => 'Dikonfirmasi admin.',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function audit_trail_records_actor_and_timestamp_for_payment_verifications(): void
    {
        $customer = Customer::create(['name' => 'Siti Rahma', 'phone' => '081987654321']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::QUOTED,
            'total_amount' => 500000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => Payment::generateNumber($order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 500000,
            'status' => PaymentStatus::PENDING,
        ]);

        $verifyAction = app(VerifyPayment::class);
        $verifyAction->execute($payment, $this->admin, 'Mutasi BCA cocok');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::PAID->value,
            'verified_by' => $this->admin->id,
        ]);

        $paymentFresh = $payment->fresh();
        $this->assertNotNull($paymentFresh->verified_at);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function audit_trail_records_actor_and_locations_for_inventory_movements(): void
    {
        $customer = Customer::create(['name' => 'Rian Pratama', 'phone' => '08155667788']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::PROCESSING,
        ]);

        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Kardus Barang Elektronik',
            'qr_code' => 'BB-ELC-2026-0001',
            'status' => InventoryStatus::RECEIVED,
            'storage_location' => 'Receiving Area',
        ]);

        $loc1 = StorageLocation::create([
            'code' => 'WH1-A-R01-L01',
            'warehouse' => 'WH1',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'capacity' => 5,
            'status' => StorageLocationStatus::AVAILABLE,
        ]);

        $loc2 = StorageLocation::create([
            'code' => 'WH1-B-R02-L01',
            'warehouse' => 'WH1',
            'zone' => 'B',
            'rack' => 'R02',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'capacity' => 5,
            'status' => StorageLocationStatus::AVAILABLE,
        ]);

        // 1. Initial Storage Assignment
        $assignAction = app(AssignInventoryToLocation::class);
        $assignAction->execute($item, $loc1, $this->operation);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item->id,
            'to_location_code' => 'WH1-A-R01-L01',
            'performed_by' => $this->operation->id,
        ]);

        // 2. Relocation
        $relocateAction = app(RelocateInventoryItem::class);
        $relocateAction->execute($item, $loc2, $this->operation, 'Pindah rak agar lebih mudah diakses');

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item->id,
            'from_location_code' => 'WH1-A-R01-L01',
            'to_location_code' => 'WH1-B-R02-L01',
            'performed_by' => $this->operation->id,
            'notes' => 'Pindah rak agar lebih mudah diakses',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_based_authorization_strictly_protects_modules(): void
    {
        // 1. Operation cannot access Orders Management
        $response = $this->actingAs($this->operation)->get('/admin/orders');
        $response->assertStatus(403);

        // 2. Operation cannot access Payments Management
        $response = $this->actingAs($this->operation)->get('/admin/payments');
        $response->assertStatus(403);

        // 3. Admin cannot access Inventory Management
        $response = $this->actingAs($this->admin)->get('/admin/inventory');
        $response->assertStatus(403);

        // 4. Admin cannot access Storage Locations
        $response = $this->actingAs($this->admin)->get('/admin/storage');
        $response->assertStatus(403);

        // 5. Owner has full superadmin access
        $this->actingAs($this->owner)->get('/admin/orders')->assertStatus(200);
        $this->actingAs($this->owner)->get('/admin/payments')->assertStatus(200);
        $this->actingAs($this->owner)->get('/admin/inventory')->assertStatus(200);
        $this->actingAs($this->owner)->get('/admin/storage')->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function upload_validation_rejects_disallowed_file_types_and_excessive_sizes(): void
    {
        $disallowedGif = UploadedFile::fake()->image('animation.gif');
        $oversizedPhoto = UploadedFile::fake()->image('huge_photo.jpg')->size(6000); // 6MB > 5MB

        Livewire::test(BookingForm::class)
            ->set('customerName', 'Dewi Lestari')
            ->set('customerPhone', '08123456789')
            ->set('serviceId', $this->service->id)
            ->set('pickupAddress', 'Jl. Sukarno Hatta 12')
            ->set('photos', [$disallowedGif])
            ->call('submit')
            ->assertHasErrors(['photos.0']);

        Livewire::test(BookingForm::class)
            ->set('customerName', 'Dewi Lestari')
            ->set('customerPhone', '08123456789')
            ->set('serviceId', $this->service->id)
            ->set('pickupAddress', 'Jl. Sukarno Hatta 12')
            ->set('photos', [$oversizedPhoto])
            ->call('submit')
            ->assertHasErrors(['photos.0']);
    }



    #[\PHPUnit\Framework\Attributes\Test]
    public function user_passwords_are_securely_hashed(): void
    {
        $this->assertTrue(Hash::check('secret123', $this->owner->password));
        $this->assertNotEquals('secret123', $this->owner->password);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function rate_limiting_throttles_excessive_tracking_requests(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $response = $this->get('/track');
            $response->assertStatus(200);
        }

        // 16th request should hit rate limit (429 Too Many Requests)
        $response = $this->get('/track');
        $response->assertStatus(429);
    }
}

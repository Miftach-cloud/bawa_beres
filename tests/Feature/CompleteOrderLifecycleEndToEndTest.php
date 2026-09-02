<?php

namespace Tests\Feature;

use App\Actions\Documentation\UploadInventoryPhoto;
use App\Actions\Inventory\CheckInventoryItem;
use App\Actions\Inventory\GenerateExpectedInventory;
use App\Actions\Inventory\OutboundInventoryItem;
use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Inventory\ReleaseInventoryItem;
use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Orders\ChangeOrderStatus;
use App\Actions\Payments\RecordPayment;
use App\Actions\Payments\VerifyPayment;
use App\Actions\Quotations\CreateQuotation;
use App\Actions\Schedules\CreateSchedule;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Enums\MovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PhotoType;
use App\Enums\QuotationStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Enums\UserRole;
use App\Exceptions\InvalidOrderStateTransitionException;
use App\Livewire\Public\BookingForm;
use App\Livewire\Public\OrderTracking;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\StorageLocation;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\PickupScheduledNotification;
use App\Notifications\QuotationCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompleteOrderLifecycleEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $admin;

    protected User $operation;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->owner = User::factory()->create([
            'name' => 'Owner BawaBeres',
            'email' => 'owner@bawaberes.id',
            'role' => UserRole::OWNER,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin BawaBeres',
            'email' => 'admin@bawaberes.id',
            'role' => UserRole::ADMIN,
        ]);

        $this->operation = User::factory()->create([
            'name' => 'Operation Lead',
            'email' => 'operation@bawaberes.id',
            'role' => UserRole::OPERATION,
        ]);

        $this->service = Service::create([
            'name' => 'Pindahan & Storage Mahasiswa',
            'description' => 'Paket hemat pindahan kost dan titip barang liburan semester.',
            'base_price' => 200000,
            'is_active' => true,
            'requires_destination' => false,
            'requires_storage' => true,
        ]);
    }

    #[Test]
    public function full_order_lifecycle_from_public_booking_to_completion(): void
    {
        // -------------------------------------------------------------
        // STEP 1: Customer Submits Public Booking with Estimation Photo
        // -------------------------------------------------------------
        $estimationPhoto = UploadedFile::fake()->image('kamar_kost.jpg', 800, 600);

        $bookingTest = Livewire::test(BookingForm::class)
            ->set('customerName', 'Ahmad Dani')
            ->set('customerPhone', '081234567890')
            ->set('customerEmail', 'ahmad@example.com')
            ->set('serviceId', $this->service->id)
            ->set('pickupAddress', 'Jl. Gajayana No. 50, Lowokwaru')
            ->set('pickupCity', 'Kota Malang')
            ->set('items', [
                ['name' => 'Kardus Buku & Skripsi', 'category' => 'Sedang', 'quantity' => 2, 'notes' => 'Hati-hati basah'],
                ['name' => 'Kulkas 1 Pintu Mini', 'category' => 'Besar', 'quantity' => 1, 'notes' => 'Barang elektronik'],
            ])
            ->set('photos', [$estimationPhoto])
            ->set('preferredDate', now()->addDays(2)->format('Y-m-d'))
            ->set('customerNotes', 'Mohon jemput pagi hari sekitar jam 9')
            ->call('submit');

        $bookingTest->assertSet('isSubmitted', true);

        $order = Order::whereHas('customer', fn ($q) => $q->where('phone', '081234567890'))->first();
        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::PENDING_REVIEW, $order->status);
        $this->assertEquals(2, $order->items()->count());
        $this->assertEquals('Jl. Gajayana No. 50, Lowokwaru', $order->pickupAddress->address);

        // Verify OrderAttachment persisted on private storage
        $this->assertCount(1, $order->attachments);
        $attachment = $order->attachments->first();
        Storage::disk('local')->assertExists($attachment->file_path);
        Storage::disk('public')->assertMissing($attachment->file_path);

        // Verify OrderCreated notification stored
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $order->customer->id,
            'type' => OrderCreatedNotification::class,
        ]);

        // -------------------------------------------------------------
        // STEP 2: Admin Access Security & Order Review
        // -------------------------------------------------------------
        // Unauthenticated visitor is redirected to login
        $this->get("/admin/orders/{$order->id}")->assertRedirect('/admin/login');

        // Non-staff user receives 403 Forbidden
        $customerUser = User::factory()->create(['role' => null]);
        $this->actingAs($customerUser)->get("/admin/orders/{$order->id}")->assertStatus(403);

        // Authorized admin reviews order
        $this->actingAs($this->admin);
        $orderShowResponse = $this->get("/admin/orders/{$order->id}");
        $orderShowResponse->assertStatus(200);
        $orderShowResponse->assertSee('Ahmad Dani');
        $orderShowResponse->assertSee($order->order_code);

        // Admin streams private estimation photo
        $attachmentResponse = $this->get(route('admin.media.order-attachment', $attachment));
        $attachmentResponse->assertStatus(200);
        $this->assertStringContainsString('private', $attachmentResponse->headers->get('Cache-Control'));

        // -------------------------------------------------------------
        // STEP 3: Admin Issues Official Quotation & Customer Accepts
        // -------------------------------------------------------------
        $createQuotation = app(CreateQuotation::class);
        $quotation = $createQuotation->execute($order, [
            'status' => QuotationStatus::SENT,
            'discount' => 25000,
            'tax' => 0,
            'notes' => 'Diskon promo mahasiswa awal semester',
            'items' => [
                ['name' => 'Biaya Pick-up & Transport', 'quantity' => 1, 'unit_price' => 150000],
                ['name' => 'Sewa Storage 1 Bulan (2 Kardus + 1 Kulkas)', 'quantity' => 1, 'unit_price' => 150000],
                ['name' => 'Bantuan Tenaga Angkut', 'quantity' => 1, 'unit_price' => 50000],
            ],
        ], $this->admin);

        $this->assertEquals(325000, $quotation->total_amount); // 350000 - 25000 = 325000
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $order->customer->id,
            'type' => QuotationCreatedNotification::class,
        ]);

        // Customer accepts quotation -> Order confirmed
        $order->update(['total_amount' => $quotation->total_amount]);
        $changeOrderStatus = app(ChangeOrderStatus::class);
        $changeOrderStatus->execute($order, OrderStatus::CONFIRMED, 'Penawaran disetujui oleh customer.', $this->admin);
        $this->assertEquals(OrderStatus::CONFIRMED, $order->fresh()->status);

        // -------------------------------------------------------------
        // STEP 4: Record & Verify Payment via Domain Actions
        // -------------------------------------------------------------
        $transferProof = UploadedFile::fake()->image('bukti_transfer_bca.jpg', 600, 800);
        $recordPayment = app(RecordPayment::class);
        $payment = $recordPayment->execute($order, [
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 325000,
            'bank_name' => 'BCA',
            'account_name' => 'Ahmad Dani',
            'status' => PaymentStatus::WAITING_VERIFICATION,
        ], $transferProof);

        // Proof stored on private storage
        Storage::disk('local')->assertExists($payment->proof_path);

        $verifyPayment = app(VerifyPayment::class);
        $verifyPayment->execute($payment, $this->admin, 'Pembayaran lunas via BCA transfer');

        $this->assertEquals(PaymentStatus::PAID, $payment->fresh()->status);
        $this->assertEquals(OrderStatus::PAID, $order->fresh()->status);
        $this->assertTrue($order->fresh()->isFullyPaid());

        // -------------------------------------------------------------
        // STEP 5: Operations Schedules Pickup Dispatch & Completes Pickup
        // -------------------------------------------------------------
        $this->actingAs($this->operation);
        $createSchedule = app(CreateSchedule::class);
        $schedule = $createSchedule->execute($order, [
            'type' => ScheduleType::PICKUP,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'assigned_team' => 'Tim Armada Alpha (Mas Wahyu & Mas Toni)',
            'vehicle' => 'Grand Max Pick-up (N 8899 BB)',
            'notes' => 'Bawa tali pengikat dan terpal',
        ], $this->operation);

        $this->assertEquals(OrderStatus::SCHEDULED, $order->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $order->customer->id,
            'type' => PickupScheduledNotification::class,
        ]);

        // Driver arrives and picks up items
        $order = $order->fresh();
        $changeOrderStatus->execute($order, OrderStatus::PICKED_UP, 'Barang telah dijemput driver dari lokasi customer.', $this->operation);
        $this->assertEquals(OrderStatus::PICKED_UP, $order->fresh()->status);

        // -------------------------------------------------------------
        // STEP 6: Physical Inventory Generation & Receiving
        // -------------------------------------------------------------
        $generateInventoryAction = app(GenerateExpectedInventory::class);
        $generatedItems = $generateInventoryAction->execute($order->fresh());

        // 2 boxes + 1 fridge = 3 physical inventory items
        $this->assertCount(3, $generatedItems);
        $this->assertEquals(3, $order->inventoryItems()->count());

        $item1 = $generatedItems[0];
        $item2 = $generatedItems[1];
        $item3 = $generatedItems[2];

        $this->assertEquals(InventoryStatus::EXPECTED, $item1->status);
        $this->assertStringStartsWith('INV-', $item1->inventory_code);
        $this->assertNotNull($item1->qr_code);

        $receiveAction = app(ReceiveInventoryItem::class);
        $receiveAction->execute($item1, $this->operation);
        $receiveAction->execute($item2, $this->operation);
        $receiveAction->execute($item3, $this->operation);

        $this->assertEquals(InventoryStatus::RECEIVED, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::RECEIVED, $item2->fresh()->status);
        $this->assertEquals(InventoryStatus::RECEIVED, $item3->fresh()->status);

        // -------------------------------------------------------------
        // STEP 7: QC Inspection, Photo Documentation & QR Validation
        // -------------------------------------------------------------
        $checkAction = app(CheckInventoryItem::class);
        $checkAction->execute($item1, ItemCondition::GOOD, 'Kardus kokoh aman');
        $checkAction->execute($item2, ItemCondition::GOOD, 'Kardus kokoh aman');
        $checkAction->execute($item3, ItemCondition::GOOD, 'Kulkas bersih dan kering');

        $this->assertEquals(InventoryStatus::CHECKED, $item1->fresh()->status);

        // Upload condition photo
        $photoFile = UploadedFile::fake()->image('kardus_buku_depan.jpg', 800, 600);
        $uploadPhotoAction = app(UploadInventoryPhoto::class);
        $photo = $uploadPhotoAction->execute(
            item: $item1,
            file: $photoFile,
            type: PhotoType::CONDITION,
            caption: 'Kondisi kardus tersegel lakban rapat',
            uploader: $this->operation
        );

        $this->assertDatabaseHas('inventory_photos', [
            'inventory_item_id' => $item1->id,
            'caption' => 'Kondisi kardus tersegel lakban rapat',
        ]);
        Storage::disk('local')->assertExists($photo->file_path);

        // Public QR scan checks:
        auth()->logout();

        // 1. Visitors see custody verification seal
        $scanResponse = $this->get($item1->scan_url);
        $scanResponse->assertStatus(200);
        $scanResponse->assertSee($item1->inventory_code);
        $scanResponse->assertSee('Gudang Resmi BawaBeres');
        $scanResponse->assertSee('Terverifikasi');
        $scanResponse->assertDontSee('081234567890'); // Customer phone concealed

        // 2. Sequential code enumeration is rejected for unauthenticated visitors
        $this->get("/i/{$item1->inventory_code}")->assertSee('Barang Fisik Tidak Ditemukan');

        // 3. Public tracking output displays current status
        $trackingResponse = $this->get("/track/{$order->order_code}");
        $trackingResponse->assertStatus(200);
        $trackingResponse->assertSee($order->order_code);

        // -------------------------------------------------------------
        // STEP 8: Storage Location Rack Allocation & Occupancy Sync
        // -------------------------------------------------------------
        $this->actingAs($this->operation);
        $storageLocation1 = StorageLocation::create([
            'code' => 'WH1-A-R01-L01',
            'warehouse' => 'WH1',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'capacity' => 10,
            'status' => StorageLocationStatus::AVAILABLE,
        ]);

        $storageLocation2 = StorageLocation::create([
            'code' => 'WH1-B-R01-L01',
            'warehouse' => 'WH1',
            'zone' => 'B',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::HEAVY_DUTY,
            'capacity' => 2,
            'status' => StorageLocationStatus::AVAILABLE,
        ]);

        $assignStorageAction = app(AssignInventoryToLocation::class);
        $assignStorageAction->execute($item1, $storageLocation1, $this->operation);
        $assignStorageAction->execute($item2, $storageLocation1, $this->operation);
        $assignStorageAction->execute($item3, $storageLocation2, $this->operation);

        $this->assertEquals(InventoryStatus::STORED, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::STORED, $item2->fresh()->status);
        $this->assertEquals(InventoryStatus::STORED, $item3->fresh()->status);
        $this->assertEquals(OrderStatus::STORED, $order->fresh()->status);

        // Rack occupancy is synchronized
        $this->assertEquals(2, $storageLocation1->fresh()->occupiedCount());
        $this->assertEquals(1, $storageLocation2->fresh()->occupiedCount());

        // Inbound movement record created
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item1->id,
            'movement_type' => MovementType::INBOUND->value,
            'to_location_code' => 'WH1-A-R01-L01',
        ]);

        // -------------------------------------------------------------
        // STEP 9: Inventory Relocation & Movement Audit
        // -------------------------------------------------------------
        $newRackSlot = StorageLocation::create([
            'code' => 'WH1-A-R01-L02',
            'warehouse' => 'WH1',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L02',
            'type' => StorageLocationType::STANDARD_RACK,
            'capacity' => 10,
            'status' => StorageLocationStatus::AVAILABLE,
        ]);

        $relocateAction = app(RelocateInventoryItem::class);
        $relocateAction->execute($item1, $newRackSlot, $this->operation, 'Penyesuaian tinggi rak penyimpanan');

        $this->assertEquals('WH1-A-R01-L02', $item1->fresh('storageLocation')->storageLocation->code);
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item1->id,
            'movement_type' => MovementType::RELOCATION->value,
            'from_location_code' => 'WH1-A-R01-L01',
            'to_location_code' => 'WH1-A-R01-L02',
            'performed_by' => $this->operation->id,
        ]);

        // Former rack has 1 item remaining, new rack has 1 item
        $this->assertEquals(1, $storageLocation1->fresh()->occupiedCount());
        $this->assertEquals(1, $newRackSlot->fresh()->occupiedCount());

        // -------------------------------------------------------------
        // STEP 10: Outbound Staging, Vacate Rack & Custody Handover
        // -------------------------------------------------------------
        $order = $order->fresh();
        $changeOrderStatus->execute($order, OrderStatus::OUTBOUND_REQUESTED, 'Customer mengajukan permintaan pengambilan barang storage.', $this->operation);
        $this->assertEquals(OrderStatus::OUTBOUND_REQUESTED, $order->fresh()->status);

        $outboundAction = app(OutboundInventoryItem::class);
        $outboundAction->execute($item1, $this->operation);
        $outboundAction->execute($item2, $this->operation);
        $outboundAction->execute($item3, $this->operation);

        $this->assertEquals(InventoryStatus::OUTBOUND, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::OUTBOUND, $item2->fresh()->status);
        $this->assertEquals(InventoryStatus::OUTBOUND, $item3->fresh()->status);

        // Vacate racks
        $vacateAction = app(VacateInventoryFromLocation::class);
        $vacateAction->execute($item1, $this->operation);
        $vacateAction->execute($item2, $this->operation);
        $vacateAction->execute($item3, $this->operation);

        // Racks are now completely free
        $this->assertEquals(0, $storageLocation1->fresh()->occupiedCount());
        $this->assertEquals(0, $storageLocation2->fresh()->occupiedCount());
        $this->assertEquals(0, $newRackSlot->fresh()->occupiedCount());

        // Release custody
        $releaseAction = app(ReleaseInventoryItem::class);
        $releaseAction->execute($item1);
        $releaseAction->execute($item2);
        $releaseAction->execute($item3);

        $this->assertEquals(InventoryStatus::RELEASED, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::RELEASED, $item2->fresh()->status);
        $this->assertEquals(InventoryStatus::RELEASED, $item3->fresh()->status);

        // -------------------------------------------------------------
        // STEP 11: Complete Order & Audit History Verification
        // -------------------------------------------------------------
        $order = $order->fresh();
        $changeOrderStatus->execute($order, OrderStatus::DELIVERED, 'Barang diantar kembali ke customer', $this->operation);
        $order = $order->fresh();
        $changeOrderStatus->execute($order, OrderStatus::COMPLETED, 'Pesanan selesai sepenuhnya', $this->admin);

        $this->assertEquals(OrderStatus::COMPLETED, $order->fresh()->status);

        // Verify OrderCompleted notification sent
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $order->customer->id,
            'type' => OrderCompletedNotification::class,
        ]);

        // Verify full order status histories count and sequence without illegal jumps
        $histories = $order->statusHistories()->orderBy('id')->get();
        $expectedSequence = [
            OrderStatus::PENDING_REVIEW,
            OrderStatus::CONFIRMED,
            OrderStatus::PAID,
            OrderStatus::SCHEDULED,
            OrderStatus::PICKED_UP,
            OrderStatus::STORED,
            OrderStatus::OUTBOUND_REQUESTED,
            OrderStatus::DELIVERED,
            OrderStatus::COMPLETED,
        ];

        $actualSequence = $histories->pluck('to_status')->all();
        $this->assertEquals($expectedSequence, $actualSequence);

        // Verify public tracking form renders with prefilled code
        $this->get("/track/{$order->order_code}")
            ->assertStatus(200)
            ->assertSee($order->order_code);

        // Verify verified customer tracking renders the final completed state
        Livewire::test(OrderTracking::class, ['order_code' => $order->order_code])
            ->set('phone', '081234567890')
            ->call('track')
            ->assertSee('Selesai')
            ->assertSee($order->order_code);
    }

    #[Test]
    public function illegal_order_status_transitions_are_strictly_rejected(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        $changeOrderStatus = app(ChangeOrderStatus::class);

        // Cannot skip directly from PENDING_REVIEW to COMPLETED
        $this->expectException(InvalidOrderStateTransitionException::class);
        $changeOrderStatus->execute($order, OrderStatus::COMPLETED, 'Illegal jump test');
    }

    #[Test]
    public function non_staff_users_cannot_access_any_internal_endpoints_throughout_lifecycle(): void
    {
        $customerUser = User::factory()->create(['role' => null]);

        $this->actingAs($customerUser);

        $this->get('/admin')->assertStatus(403);
        $this->get('/admin/orders')->assertStatus(403);
        $this->get('/admin/inventory')->assertStatus(403);
        $this->get('/admin/storage')->assertStatus(403);
        $this->get('/admin/schedule')->assertStatus(403);
        $this->get('/admin/payments')->assertStatus(403);
    }
}

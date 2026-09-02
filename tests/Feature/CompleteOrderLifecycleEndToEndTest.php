<?php

namespace Tests\Feature;

use App\Actions\Documentation\UploadInventoryPhoto;
use App\Actions\Inventory\OutboundInventoryItem;
use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Orders\ChangeOrderStatus;
use App\Actions\Payments\VerifyPayment;
use App\Actions\Quotations\CreateQuotation;
use App\Actions\Schedules\CreateSchedule;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Enums\InventoryStatus;
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
use App\Livewire\Public\BookingForm;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
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
        // STEP 1: Customer Submits Public Booking
        // -------------------------------------------------------------
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
            ->set('preferredDate', now()->addDays(2)->format('Y-m-d'))
            ->set('customerNotes', 'Mohon jemput pagi hari sekitar jam 9')
            ->call('submit');

        $bookingTest->assertSet('isSubmitted', true);

        $order = Order::whereHas('customer', fn ($q) => $q->where('phone', '081234567890'))->first();
        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::PENDING_REVIEW, $order->status);
        $this->assertEquals(2, $order->items()->count());
        $this->assertEquals('Jl. Gajayana No. 50, Lowokwaru', $order->pickupAddress->address);

        // Verify OrderCreated notification stored
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $order->customer->id,
            'type' => OrderCreatedNotification::class,
        ]);

        // -------------------------------------------------------------
        // STEP 2: Admin Reviews Order
        // -------------------------------------------------------------
        $this->actingAs($this->admin);
        $orderShowResponse = $this->get("/admin/orders/{$order->id}");
        $orderShowResponse->assertStatus(200);
        $orderShowResponse->assertSee('Ahmad Dani');
        $orderShowResponse->assertSee($order->order_code);

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
        // STEP 4: Record & Verify Payment
        // -------------------------------------------------------------
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => Payment::generateNumber($order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 325000,
            'bank_name' => 'BCA',
            'account_name' => 'Ahmad Dani',
            'status' => PaymentStatus::WAITING_VERIFICATION,
        ]);

        $verifyPayment = app(VerifyPayment::class);
        $verifyPayment->execute($payment, $this->admin, 'Pembayaran lunas via BCA transfer');

        $this->assertEquals(PaymentStatus::PAID, $payment->fresh()->status);
        $this->assertEquals(OrderStatus::PAID, $order->fresh()->status);

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
        $changeOrderStatus = app(ChangeOrderStatus::class);
        $changeOrderStatus->execute($order, OrderStatus::PICKED_UP, 'Barang telah dijemput driver dari lokasi customer.', $this->operation);
        $this->assertEquals(OrderStatus::PICKED_UP, $order->fresh()->status);

        // -------------------------------------------------------------
        // STEP 6: Field Team Receives Inventory Physical Items & Assigns QR
        // -------------------------------------------------------------

        $item1 = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Kardus Buku & Skripsi',
            'qr_code' => 'BB-BOX-2026-0001',
            'status' => InventoryStatus::EXPECTED,
        ]);

        $item2 = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Kulkas 1 Pintu Mini',
            'qr_code' => 'BB-APP-2026-0001',
            'status' => InventoryStatus::EXPECTED,
        ]);

        $receiveAction = app(ReceiveInventoryItem::class);
        $receiveAction->execute($item1, $this->operation);
        $receiveAction->execute($item2, $this->operation);

        $this->assertEquals(InventoryStatus::RECEIVED, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::RECEIVED, $item2->fresh()->status);

        // -------------------------------------------------------------
        // STEP 7: Field Team Uploads Condition Photo Documentation
        // -------------------------------------------------------------
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

        // -------------------------------------------------------------
        // STEP 8: Storage Location Rack Allocation
        // -------------------------------------------------------------
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
        $assignStorageAction->execute($item2, $storageLocation2, $this->operation);

        $this->assertEquals(InventoryStatus::STORED, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::STORED, $item2->fresh()->status);
        $this->assertEquals(OrderStatus::STORED, $order->fresh()->status);

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

        // -------------------------------------------------------------
        // STEP 10: Outbound Staging & Handover
        // -------------------------------------------------------------
        $order = $order->fresh();
        $changeOrderStatus->execute($order, OrderStatus::OUTBOUND_REQUESTED, 'Customer mengajukan permintaan pengambilan barang storage.', $this->operation);
        $this->assertEquals(OrderStatus::OUTBOUND_REQUESTED, $order->fresh()->status);

        $outboundAction = app(OutboundInventoryItem::class);
        $outboundAction->execute($item1, $this->operation);
        $outboundAction->execute($item2, $this->operation);

        $this->assertEquals(InventoryStatus::OUTBOUND, $item1->fresh()->status);
        $this->assertEquals(InventoryStatus::OUTBOUND, $item2->fresh()->status);

        // -------------------------------------------------------------
        // STEP 11: Complete Order & Audit Verification
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

        // Verify full order status histories count and sequence
        $histories = $order->statusHistories()->orderBy('id')->get();
        $this->assertGreaterThanOrEqual(4, $histories->count());
        $this->assertEquals(OrderStatus::COMPLETED, $histories->last()->to_status);
        $this->assertEquals($this->admin->id, $histories->last()->changed_by);
    }
}

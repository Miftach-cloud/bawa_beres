<?php

namespace Tests\Feature;

use App\Actions\Payments\RecordPayment;
use App\Enums\PaymentMethod;
use App\Enums\PhotoType;
use App\Livewire\Admin\Inventory\PhotosModal;
use App\Livewire\Admin\Payments\Manager as PaymentManager;
use App\Models\InventoryItem;
use App\Models\InventoryPhoto;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecureFileStorageTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $admin;

    protected User $operation;

    protected Order $order;

    protected InventoryItem $inventoryItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->owner()->create();
        $this->admin = User::factory()->admin()->create();
        $this->operation = User::factory()->operation()->create();

        $this->order = Order::factory()->create(['total_amount' => 500000]);
        $this->inventoryItem = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Lemari Pakaian 3 Pintu',
        ]);
    }

    #[Test]
    public function guest_cannot_access_payment_proof_or_inventory_photo(): void
    {
        Storage::fake('local');
        $filePath = 'payment-proofs/sample_proof.jpg';
        Storage::disk('local')->put($filePath, 'fake-image-binary-data');

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 250000,
            'proof_path' => $filePath,
        ]);

        $photo = InventoryPhoto::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => PhotoType::CONDITION,
            'file_path' => 'inventory-photos/sample_photo.jpg',
            'file_name' => 'sample_photo.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
        ]);
        Storage::disk('local')->put($photo->file_path, 'fake-photo-binary-data');

        // Guests are redirected to login
        $this->get(route('admin.media.payment-proof', $payment))->assertRedirect('/admin/login');
        $this->get(route('admin.media.inventory-photo', $photo))->assertRedirect('/admin/login');
    }

    #[Test]
    public function unauthorized_role_cannot_access_sensitive_operational_files(): void
    {
        Storage::fake('local');

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 250000,
            'proof_path' => 'payment-proofs/test_proof.jpg',
        ]);
        Storage::disk('local')->put($payment->proof_path, 'fake-data');

        $photo = InventoryPhoto::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => PhotoType::STORAGE,
            'file_path' => 'inventory-photos/test_photo.jpg',
            'file_name' => 'test_photo.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
        ]);
        Storage::disk('local')->put($photo->file_path, 'fake-data');

        // Operation cannot access financial payment proof
        $this->actingAs($this->operation)
            ->get(route('admin.media.payment-proof', $payment))
            ->assertStatus(403);

        // Admin cannot access inventory photos
        $this->actingAs($this->admin)
            ->get(route('admin.media.inventory-photo', $photo))
            ->assertStatus(403);
    }

    #[Test]
    public function authorized_role_can_access_sensitive_files_securely(): void
    {
        Storage::fake('local');

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 250000,
            'proof_path' => 'payment-proofs/verified_proof.jpg',
        ]);
        Storage::disk('local')->put($payment->proof_path, 'valid-proof-content');

        $photo = InventoryPhoto::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => PhotoType::STORAGE,
            'file_path' => 'inventory-photos/verified_photo.jpg',
            'file_name' => 'verified_photo.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
        ]);
        Storage::disk('local')->put($photo->file_path, 'valid-photo-content');

        // Admin can access payment proof
        $response = $this->actingAs($this->admin)->get(route('admin.media.payment-proof', $payment));
        $response->assertStatus(200);

        // Operation can access inventory photo
        $response = $this->actingAs($this->operation)->get(route('admin.media.inventory-photo', $photo));
        $response->assertStatus(200);

        // Owner can access both
        $this->actingAs($this->owner)->get(route('admin.media.payment-proof', $payment))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('admin.media.inventory-photo', $photo))->assertStatus(200);
    }

    #[Test]
    public function invalid_file_types_are_rejected_by_upload_validators(): void
    {
        $this->actingAs($this->admin);

        $fakeScript = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('openRecordModal')
            ->set('method', PaymentMethod::BANK_TRANSFER->value)
            ->set('amount', 100000)
            ->set('proofFile', $fakeScript)
            ->call('savePayment')
            ->assertHasErrors(['proofFile']);

        $this->actingAs($this->operation);
        $fakeExe = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');

        Livewire::test(PhotosModal::class)
            ->call('open', $this->inventoryItem->id)
            ->set('type', PhotoType::CONDITION->value)
            ->set('photos', [$fakeExe])
            ->call('uploadPhotos')
            ->assertHasErrors(['photos.*']);
    }

    #[Test]
    public function oversized_files_are_rejected(): void
    {
        $this->actingAs($this->admin);

        // 12MB is over the 5MB limit for payment proofs
        $largeFile = UploadedFile::fake()->image('huge.jpg')->size(12288);

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('openRecordModal')
            ->set('method', PaymentMethod::BANK_TRANSFER->value)
            ->set('amount', 100000)
            ->set('proofFile', $largeFile)
            ->call('savePayment')
            ->assertHasErrors(['proofFile']);
    }

    #[Test]
    public function direct_public_storage_does_not_contain_private_uploads(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $file = UploadedFile::fake()->image('private_receipt.jpg');
        $action = app(RecordPayment::class);
        $payment = $action->execute($this->order, [
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 100000,
        ], $file);

        // Assert exists on private local disk
        Storage::disk('local')->assertExists($payment->proof_path);

        // Assert NOT present on public disk
        Storage::disk('public')->assertMissing($payment->proof_path);
    }
}

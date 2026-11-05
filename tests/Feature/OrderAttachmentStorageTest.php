<?php

namespace Tests\Feature;

use App\Livewire\Public\BookingForm;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAttachmentStorageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operation;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->admin = User::factory()->admin()->create();
        $this->operation = User::factory()->operation()->create();

        $this->service = Service::factory()->create([
            'name' => 'Jasa Pindahan Rumah',
            'base_price' => 300000,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function booking_form_persists_uploaded_photos_as_order_attachments(): void
    {
        $file1 = UploadedFile::fake()->image('living_room.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('wardrobe.png', 600, 600);

        Livewire::test(BookingForm::class)
            ->set('customerName', 'Ahmad Dani')
            ->set('customerPhone', '081234567890')
            ->set('serviceId', $this->service->id)
            ->set('pickupAddress', 'Jl. Sigura-gura No. 15')
            ->set('pickupCity', 'Kota Malang')
            ->set('destinationAddress', 'Jl. Soekarno Hatta No. 20')
            ->set('destinationCity', 'Kota Malang')
            ->set('preferredDate', now()->addDay()->toDateString())
            ->set('items', [
                ['name' => 'Sofa 3 Seater', 'category' => 'Besar', 'quantity' => 1, 'notes' => 'Berat'],
            ])
            ->set('photos', [$file1, $file2])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $order = Order::latest('id')->first();
        $this->assertNotNull($order);

        // Verify attachments relation
        $this->assertCount(2, $order->attachments);
        $this->assertCount(2, $order->estimationPhotos);

        $attachment1 = $order->attachments()->first();
        $this->assertSame('ESTIMATION_PHOTO', $attachment1->type);
        $this->assertNotNull($attachment1->file_path);
        $this->assertNotNull($attachment1->original_name);

        // Verify file was saved to private local disk
        Storage::disk('local')->assertExists($attachment1->file_path);
        // Ensure NOT saved to public disk
        Storage::disk('public')->assertMissing($attachment1->file_path);
    }

    #[Test]
    public function authorized_staff_can_view_order_attachment_via_secure_route(): void
    {
        $order = Order::factory()->create(['service_id' => $this->service->id]);

        $fakePath = "orders/{$order->id}/estimation/sample_test.jpg";
        Storage::disk('local')->put($fakePath, 'fake-image-binary-data');

        $attachment = OrderAttachment::create([
            'order_id' => $order->id,
            'type' => 'ESTIMATION_PHOTO',
            'file_path' => $fakePath,
            'original_name' => 'sample_test.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.media.order-attachment', $attachment));

        $response->assertStatus(200);
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    #[Test]
    public function unauthenticated_visitor_cannot_access_order_attachment(): void
    {
        $order = Order::factory()->create(['service_id' => $this->service->id]);
        $fakePath = "orders/{$order->id}/estimation/sample_private.jpg";
        Storage::disk('local')->put($fakePath, 'secret-data');

        $attachment = OrderAttachment::create([
            'order_id' => $order->id,
            'type' => 'ESTIMATION_PHOTO',
            'file_path' => $fakePath,
            'original_name' => 'sample_private.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        $response = $this->get(route('admin.media.order-attachment', $attachment));

        $response->assertRedirect(route('admin.login'));
    }
}

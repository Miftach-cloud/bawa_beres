<?php

namespace Tests\Feature;

use App\Enums\PhotoType;
use App\Models\InventoryItem;
use App\Models\InventoryPhoto;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LegacyPrivateMediaMigrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_copies_referenced_public_media_and_can_remove_the_verified_source(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $order = Order::factory()->create();
        $item = InventoryItem::create(['order_id' => $order->id, 'name' => 'Lemari']);
        $photo = InventoryPhoto::create([
            'inventory_item_id' => $item->id,
            'type' => PhotoType::CONDITION,
            'file_path' => 'inventory-photos/legacy.jpg',
            'file_name' => 'legacy.jpg',
        ]);
        Storage::disk('public')->put($photo->file_path, 'legacy-photo');

        $this->artisan('media:migrate-legacy-private', ['--delete-source' => true])
            ->assertSuccessful();

        Storage::disk('local')->assertExists($photo->file_path);
        Storage::disk('public')->assertMissing($photo->file_path);
        $this->assertSame('legacy-photo', Storage::disk('local')->get($photo->file_path));
    }

    #[Test]
    public function it_imports_legacy_order_photos_idempotently(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $order = Order::factory()->create();
        $path = "orders/{$order->id}/estimation/room.jpg";
        Storage::disk('public')->put($path, 'room-photo');

        $this->artisan('media:migrate-legacy-private')->assertSuccessful();
        $this->artisan('media:migrate-legacy-private')->assertSuccessful();

        $this->assertSame(1, OrderAttachment::query()->where('file_path', $path)->count());
        Storage::disk('local')->assertExists($path);
    }

    #[Test]
    public function secure_routes_never_fall_back_to_public_storage(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $order = Order::factory()->create();
        $attachment = OrderAttachment::create([
            'order_id' => $order->id,
            'type' => 'ESTIMATION_PHOTO',
            'file_path' => "orders/{$order->id}/estimation/public-only.jpg",
            'original_name' => 'public-only.jpg',
        ]);
        Storage::disk('public')->put($attachment->file_path, 'public-only');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.media.order-attachment', $attachment))
            ->assertNotFound();
    }
}

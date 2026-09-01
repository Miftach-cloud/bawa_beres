<?php

namespace Tests\Unit;

use App\Enums\PhotoType;
use App\Models\InventoryItem;
use App\Models\InventoryPhoto;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryPhotoModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function photo_model_resolves_url_and_formats_size(): void
    {
        Storage::fake('public');

        $order = Order::factory()->create();
        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Meja Rias Kaca',
        ]);

        $photo = InventoryPhoto::create([
            'inventory_item_id' => $item->id,
            'type' => PhotoType::DAMAGE,
            'file_path' => 'inventory-photos/test_damage.jpg',
            'file_name' => 'test_damage.jpg',
            'file_size' => 2097152, // 2MB
            'mime_type' => 'image/jpeg',
            'caption' => 'Sudut kaca retak rambut',
        ]);

        $this->assertEquals(PhotoType::DAMAGE, $photo->type);
        $this->assertEquals('2 MB', $photo->formatted_size);
        $this->assertNotNull($photo->url);
        $this->assertStringContainsString('admin/media/inventory-photo', $photo->url);

        $this->assertCount(1, $item->damagePhotos);
        $this->assertCount(1, $item->photos);
    }
}

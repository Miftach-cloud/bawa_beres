<?php

namespace Tests\Feature;

use App\Actions\Documentation\DeleteInventoryPhoto;
use App\Actions\Documentation\UploadInventoryPhoto;
use App\Enums\PhotoType;
use App\Livewire\Admin\Inventory\PhotosModal;
use App\Models\InventoryItem;
use App\Models\InventoryPhoto;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryPhotoDocumentationTest extends TestCase
{
    use RefreshDatabase;

    protected User $operation;
    protected User $customerUser;
    protected Order $order;
    protected InventoryItem $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = User::factory()->operation()->create();
        $this->order = Order::factory()->create();
        $this->item = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Sofa 3 Seater Minimalis',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function action_uploads_photo_with_extracted_metadata(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('sofa_pickup.jpg', 800, 600)->size(1500); // 1500 KB

        $action = app(UploadInventoryPhoto::class);
        $photo = $action->execute(
            $this->item,
            $file,
            PhotoType::RECEIVING,
            'Foto saat dijemput dari rumah klien',
            $this->operation
        );

        $this->assertDatabaseHas('inventory_photos', [
            'id' => $photo->id,
            'inventory_item_id' => $this->item->id,
            'type' => PhotoType::RECEIVING->value,
            'file_name' => 'sofa_pickup.jpg',
            'caption' => 'Foto saat dijemput dari rumah klien',
            'uploaded_by' => $this->operation->id,
        ]);

        Storage::disk('public')->assertExists($photo->file_path);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function action_deletes_photo_and_cleans_up_storage(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('sofa_kondisi.jpg');
        $action = app(UploadInventoryPhoto::class);
        $photo = $action->execute($this->item, $file, PhotoType::CONDITION);

        Storage::disk('public')->assertExists($photo->file_path);

        $deleteAction = app(DeleteInventoryPhoto::class);
        $deleteAction->execute($photo);

        $this->assertDatabaseMissing('inventory_photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing($photo->file_path);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function livewire_photos_modal_uploads_multiple_photos_and_filters(): void
    {
        Storage::fake('public');
        $this->actingAs($this->operation);

        $file1 = UploadedFile::fake()->image('damage1.jpg');
        $file2 = UploadedFile::fake()->image('damage2.jpg');

        Livewire::test(PhotosModal::class)
            ->call('open', $this->item->id)
            ->assertSee($this->item->name)
            ->set('type', PhotoType::DAMAGE->value)
            ->set('caption', 'Goresan di kaki kayu belakang')
            ->set('photos', [$file1, $file2])
            ->call('uploadPhotos')
            ->assertHasNoErrors();

        $this->assertEquals(2, $this->item->photos()->count());
        $this->assertEquals(2, $this->item->damagePhotos()->count());

        Livewire::test(PhotosModal::class)
            ->call('open', $this->item->id)
            ->set('selectedCategoryFilter', PhotoType::DAMAGE->value)
            ->assertSee('Goresan di kaki kayu belakang');
    }
}

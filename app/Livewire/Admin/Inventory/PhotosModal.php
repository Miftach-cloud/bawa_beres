<?php

namespace App\Livewire\Admin\Inventory;

use App\Actions\Documentation\DeleteInventoryPhoto;
use App\Actions\Documentation\UploadInventoryPhoto;
use App\Enums\PhotoType;
use App\Models\InventoryItem;
use App\Models\InventoryPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class PhotosModal extends Component
{
    use WithFileUploads;

    public ?InventoryItem $item = null;

    public bool $show = false;

    // Upload form
    public $photos = [];

    public string $type = 'CONDITION';

    public string $caption = '';

    public string $selectedCategoryFilter = '';

    // Lightbox modal preview
    public ?InventoryPhoto $activePhoto = null;

    protected $listeners = [
        'openPhotoGallery' => 'open',
    ];

    public function open(int $itemId): void
    {
        Gate::authorize('manage-documentation');

        $this->item = InventoryItem::with(['photos.uploader'])->findOrFail($itemId);
        $this->resetUpload();
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->activePhoto = null;
        $this->resetUpload();
    }

    public function resetUpload(): void
    {
        $this->photos = [];
        $this->type = 'CONDITION';
        $this->caption = '';
        $this->selectedCategoryFilter = '';
        $this->resetValidation();
    }

    public function uploadPhotos(UploadInventoryPhoto $uploader): void
    {
        Gate::authorize('manage-documentation');

        $this->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:10240', // Max 10MB per image
            'type' => 'required|string|in:RECEIVING,CONDITION,STORAGE,OUTBOUND,DAMAGE',
            'caption' => 'nullable|string|max:500',
        ]);

        $uploadedCount = 0;
        foreach ($this->photos as $file) {
            $uploader->execute(
                $this->item,
                $file,
                $this->type,
                $this->caption ?: null,
                Auth::user()
            );
            $uploadedCount++;
        }

        $this->photos = [];
        $this->caption = '';
        $this->item->refresh();

        session()->flash('photo_message', "Berhasil mengunggah {$uploadedCount} foto dokumentasi.");
    }

    public function viewPhoto(int $photoId): void
    {
        $this->activePhoto = InventoryPhoto::findOrFail($photoId);
    }

    public function closeLightbox(): void
    {
        $this->activePhoto = null;
    }

    public function deletePhoto(int $photoId, DeleteInventoryPhoto $deleter): void
    {
        Gate::authorize('manage-documentation');

        $photo = InventoryPhoto::findOrFail($photoId);
        $deleter->execute($photo);

        if ($this->activePhoto && $this->activePhoto->id === $photoId) {
            $this->activePhoto = null;
        }

        $this->item->refresh();
        session()->flash('photo_message', 'Foto dokumentasi berhasil dihapus.');
    }

    public function render()
    {
        $photosList = collect();
        if ($this->item) {
            $query = $this->item->photos();
            if ($this->selectedCategoryFilter) {
                $query->where('type', $this->selectedCategoryFilter);
            }
            $photosList = $query->get();
        }

        return view('livewire.admin.inventory.photos-modal', [
            'photosList' => $photosList,
            'types' => PhotoType::cases(),
        ]);
    }
}

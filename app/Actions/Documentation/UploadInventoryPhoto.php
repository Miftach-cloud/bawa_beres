<?php

namespace App\Actions\Documentation;

use App\Enums\PhotoType;
use App\Models\InventoryItem;
use App\Models\InventoryPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadInventoryPhoto
{
    /**
     * Upload photo for an inventory item with metadata extraction
     */
    public function execute(
        InventoryItem $item,
        UploadedFile $file,
        PhotoType|string $type = PhotoType::CONDITION,
        ?string $caption = null,
        ?User $uploader = null
    ): InventoryPhoto {
        $photoType = ($type instanceof PhotoType) ? $type : PhotoType::from($type);

        $path = $file->store('inventory-photos', 'public');

        return InventoryPhoto::create([
            'inventory_item_id' => $item->id,
            'type' => $photoType,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'caption' => $caption,
            'uploaded_by' => $uploader?->id,
        ]);
    }
}

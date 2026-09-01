<?php

namespace App\Actions\Documentation;

use App\Models\InventoryPhoto;
use Illuminate\Support\Facades\Storage;

class DeleteInventoryPhoto
{
    /**
     * Delete photo from disk storage and database
     */
    public function execute(InventoryPhoto $photo): bool
    {
        if ($photo->file_path) {
            if (Storage::disk('local')->exists($photo->file_path)) {
                Storage::disk('local')->delete($photo->file_path);
            }
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
        }

        return (bool) $photo->delete();
    }
}

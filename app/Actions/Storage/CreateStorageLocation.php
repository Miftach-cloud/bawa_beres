<?php

namespace App\Actions\Storage;

use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Models\StorageLocation;

class CreateStorageLocation
{
    public function execute(array $data): StorageLocation
    {
        $code = $data['code'] ?? StorageLocation::formatCode(
            $data['warehouse'],
            $data['zone'],
            $data['rack'],
            $data['level']
        );

        $type = isset($data['type']) 
            ? ($data['type'] instanceof StorageLocationType ? $data['type'] : StorageLocationType::from($data['type']))
            : StorageLocationType::STANDARD_RACK;

        return StorageLocation::create([
            'code' => $code,
            'warehouse' => $data['warehouse'],
            'zone' => $data['zone'],
            'rack' => $data['rack'],
            'level' => $data['level'],
            'type' => $type,
            'status' => $data['status'] ?? StorageLocationStatus::AVAILABLE,
            'capacity' => $data['capacity'] ?? 5,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get secure URL accessor for authorized admin/staff viewing
     */
    public function getUrlAttribute(): string
    {
        return route('admin.media.order-attachment', $this);
    }

    /**
     * Check if underlying file exists in storage
     */
    public function fileExists(): bool
    {
        return Storage::disk('local')->exists($this->file_path);
    }
}

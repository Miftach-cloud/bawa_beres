<?php

namespace App\Models;

use App\Enums\PricingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'pricing_type',
        'base_price',
        'is_active',
        'requires_pickup',
        'requires_destination',
        'requires_storage',
    ];

    protected function casts(): array
    {
        return [
            'pricing_type' => PricingType::class,
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_pickup' => 'boolean',
            'requires_destination' => 'boolean',
            'requires_storage' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

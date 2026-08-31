<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'user_id',
        'name',
        'phone',
        'email',
        'notes',
    ];

    /**
     * Generate sequential/formatted customer code: CUS-YYYY-XXXXXX
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $prefix = "CUS-{$year}-";

        $latest = static::query()
            ->where('customer_code', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->value('customer_code');

        if ($latest) {
            $number = (int) substr($latest, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = static::generateCode();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

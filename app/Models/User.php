<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOperation(): bool
    {
        return $this->role === UserRole::OPERATION;
    }

    /**
     * Check if user matches any of given roles
     */
    public function hasRole(UserRole|array|string $roles): bool
    {
        if ($this->isOwner()) {
            return true; // Owner has superadmin override
        }

        if ($roles instanceof UserRole) {
            return $this->role === $roles;
        }

        if (is_string($roles)) {
            return $this->role->value === $roles;
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($role instanceof UserRole && $this->role === $role) {
                    return true;
                }
                if (is_string($role) && $this->role->value === $role) {
                    return true;
                }
            }
        }

        return false;
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }
}

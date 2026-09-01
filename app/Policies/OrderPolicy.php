<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN, UserRole::OPERATION]);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN, UserRole::OPERATION]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isOwner();
    }
}

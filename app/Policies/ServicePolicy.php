<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN, UserRole::OPERATION]);
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN, UserRole::OPERATION]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->isOwner();
    }
}

<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::OWNER->value;
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        return $user->role === UserRole::OWNER->value
            && $user->restaurants()->whereKey($restaurant->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::OWNER->value;
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $this->view($user, $restaurant);
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $this->view($user, $restaurant);
    }
}

<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use App\Policies\Concerns\RestaurantScope;

class CustomerPolicy
{
    use RestaurantScope;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::OWNER->value,
            UserRole::MANAGER->value,
            UserRole::STAFF->value,
        ]);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->viewAny($user) && $this->sameRestaurantId($user, $customer);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && $this->hasTenant($user);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->view($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        // اختياري: Owner/Manager فقط
        return in_array($user->role, [UserRole::OWNER->value, UserRole::MANAGER->value])
            && $this->sameRestaurantId($user, $customer);
    }
}

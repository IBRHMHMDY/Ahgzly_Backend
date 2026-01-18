<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff'])
            && $user->canAccessRestaurant($customer->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff'])
            && $user->canAccessRestaurant($customer->restaurant_id);
    }

    public function delete(User $user, Customer $customer): bool
    {
        // للـ MVP: نخلي الحذف Owner/Manager فقط
        return $user->hasAnyRole(['Owner', 'Manager'])
            && $user->canAccessRestaurant($customer->restaurant_id);
    }
}

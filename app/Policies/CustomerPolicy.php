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
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Customer $customer): bool
    {
        return false;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return false;
    }
}

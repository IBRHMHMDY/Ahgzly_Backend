<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // السماح بعرض القائمة
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager']);
    }

    // السماح بعرض التفاصيل
    public function view(User $user, User $model): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager']);
    }

    // السماح بالإضافة
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager']);
    }

    // السماح بالتعديل
    public function update(User $user, User $model): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager']);
    }

    // السماح بالحذف
    public function delete(User $user, User $model): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager']);
    }
}

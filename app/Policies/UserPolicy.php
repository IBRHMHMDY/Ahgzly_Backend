<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // السماح بعرض القائمة
    public function viewAny(User $user): bool
    {
        return true;
    }

    // السماح بعرض التفاصيل
    public function view(User $user, User $model): bool
    {
        return true;
    }

    // السماح بالإضافة
    public function create(User $user): bool
    {
        return true;
    }

    // السماح بالتعديل
    public function update(User $user, User $model): bool
    {
        return true;
    }

    // السماح بالحذف
    public function delete(User $user, User $model): bool
    {
        return true;
    }
}

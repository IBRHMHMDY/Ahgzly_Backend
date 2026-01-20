<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    // السماح برؤية القائمة: للمالك فقط
    public function viewAny(User $user): bool
    {
        return true;
    }

    // السماح برؤية التفاصيل: للمالك فقط، وفقط لمطاعمه
    public function view(User $user, Restaurant $restaurant): bool
    {
        return $user->hasRole('Owner') && $restaurant->owner_id === $user->id;
    }

    // السماح بالإضافة: للمالك فقط
    public function create(User $user): bool
    {
        return $user->hasRole('Owner');
    }

    // السماح بالتعديل: للمالك فقط، ولمطاعمه
    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->hasRole('Owner') && $restaurant->owner_id === $user->id;
    }

    // السماح بالحذف: للمالك فقط، ولمطاعمه
    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $user->hasRole('Owner') && $restaurant->owner_id === $user->id;
    }
}

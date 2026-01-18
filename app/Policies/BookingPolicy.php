<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff'])
            && $user->canAccessRestaurant($booking->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff'])
            && $user->canAccessRestaurant($booking->restaurant_id);
    }

    public function delete(User $user, Booking $booking): bool
    {
        // للـ MVP: الحذف Owner/Manager فقط
        return $user->hasAnyRole(['Owner', 'Manager'])
            && $user->canAccessRestaurant($booking->restaurant_id);
    }
}

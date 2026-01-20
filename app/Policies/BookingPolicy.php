<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    // هل يسمح برؤية القائمة؟
    public function viewAny(User $user): bool
    {
        return true; // أو يمكنك ربطها بصلاحية: $user->hasPermissionTo('view_bookings');
    }

    // هل يسمح برؤية حجز محدد؟
    public function view(User $user, Booking $booking): bool
    {
        return true;
    }

    // هل يسمح بالإضافة؟
    public function create(User $user): bool
    {
        return true;
    }

    // هل يسمح بالتعديل؟
    public function update(User $user, Booking $booking): bool
    {
        return true;
    }

    // هل يسمح بالحذف؟
    public function delete(User $user, Booking $booking): bool
    {
        return true;
    }
}

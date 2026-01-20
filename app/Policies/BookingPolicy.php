<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    // هل يسمح برؤية القائمة؟
    public function viewAny(User $user): bool
    {
        // أو يمكنك ربطها بصلاحية: $user->hasPermissionTo('view_bookings');
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    // هل يسمح برؤية حجز محدد؟
    public function view(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    // هل يسمح بالإضافة؟
    public function create(User $user): bool
    {
        return false;
    }

    // هل يسمح بالتعديل؟
    public function update(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    // هل يسمح بالحذف؟
    public function delete(User $user, Booking $booking): bool
    {
        return false;
    }
}

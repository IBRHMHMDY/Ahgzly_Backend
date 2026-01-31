<?php

namespace App\Observers;

use App\Models\Booking;
use Illuminate\Support\Facades\Cache;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $this->flushRestaurantAvailabilityCache($booking->restaurant_id);
    }

    public function updated(Booking $booking): void
    {
        // لو تغيّر status/start_at/end_at/booking_date/guests_count => الإتاحة تتغير
        if ($booking->wasChanged(['status', 'start_at', 'end_at', 'booking_date', 'guests_count', 'restaurant_id'])) {
            $this->flushRestaurantAvailabilityCache($booking->restaurant_id);
        }
    }

    public function deleted(Booking $booking): void
    {
        $this->flushRestaurantAvailabilityCache($booking->restaurant_id);
    }

    private function flushRestaurantAvailabilityCache(int $restaurantId): void
    {
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            Cache::tags(["restaurant:{$restaurantId}"])->flush();
        }
        // لو ما فيش tags: نسيبها TTL (5 دقائق) أو ننفذ حل fallback (سأذكره أسفل)
    }
}

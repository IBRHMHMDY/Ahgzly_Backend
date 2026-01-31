<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Models\RestaurantWorkingHour;
use Illuminate\Support\Facades\Cache;

class RestaurantObserver
{
    public function created(Restaurant $restaurant): void
    {
        // منع التكرار لو لأي سبب اتعمل create مرتين/seed
        $exists = RestaurantWorkingHour::query()
            ->where('restaurant_id', $restaurant->id)
            ->exists();

        if ($exists) {
            return;
        }

        // 7 أيام (0=Sunday .. 6=Saturday)
        for ($day = 0; $day <= 6; $day++) {
            RestaurantWorkingHour::create([
                'restaurant_id' => $restaurant->id,
                'day_of_week' => $day,
                'is_closed' => false,
                'opens_at' => '12:00:00',
                'closes_at' => '23:00:00',
            ]);
        }
    }

    public function updated(Restaurant $restaurant): void
    {
        if ($restaurant->wasChanged(['slot_duration_minutes', 'max_guests_per_slot', 'max_bookings_per_slot'])) {
            $store = Cache::getStore();
            if (method_exists($store, 'tags')) {
                Cache::tags(["restaurant:{$restaurant->id}"])->flush();
            }
        }
    }
}

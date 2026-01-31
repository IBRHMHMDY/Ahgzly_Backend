<?php

namespace App\Observers;

use App\Models\RestaurantWorkingHour;
use Illuminate\Support\Facades\Cache;

class RestaurantWorkingHourObserver
{
    public function created(RestaurantWorkingHour $hour): void
    {
        $this->flush($hour->restaurant_id);
    }

    public function updated(RestaurantWorkingHour $hour): void
    {
        $this->flush($hour->restaurant_id);
    }

    public function deleted(RestaurantWorkingHour $hour): void
    {
        $this->flush($hour->restaurant_id);
    }

    private function flush(int $restaurantId): void
    {
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            Cache::tags(["restaurant:{$restaurantId}"])->flush();
        }
    }
}

<?php

namespace App\Observers;

use App\Models\RestaurantClosure;
use Illuminate\Support\Facades\Cache;

class RestaurantClosureObserver
{
    public function created(RestaurantClosure $closure): void
    {
        $this->flush($closure->restaurant_id);
    }

    public function updated(RestaurantClosure $closure): void
    {
        $this->flush($closure->restaurant_id);
    }

    public function deleted(RestaurantClosure $closure): void
    {
        $this->flush($closure->restaurant_id);
    }

    private function flush(int $restaurantId): void
    {
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            Cache::tags(["restaurant:{$restaurantId}"])->flush();
        }
    }
}

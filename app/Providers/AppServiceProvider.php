<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Restaurant;
use App\Models\RestaurantClosure;
use App\Models\RestaurantWorkingHour;
use App\Observers\BookingObserver;
use App\Observers\RestaurantClosureObserver;
use App\Observers\RestaurantObserver;
use App\Observers\RestaurantWorkingHourObserver;
use App\Policies\RestaurantPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Restaurant::class, RestaurantPolicy::class);
        Restaurant::observe(RestaurantObserver::class);
        Booking::observe(BookingObserver::class);
        RestaurantWorkingHour::observe(RestaurantWorkingHourObserver::class);
        RestaurantClosure::observe(RestaurantClosureObserver::class);

    }
}

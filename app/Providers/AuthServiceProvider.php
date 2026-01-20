<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\RestaurantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    // protected $policies = [
    //     Booking::class => BookingPolicy::class,
    //     Customer::class => CustomerPolicy::class,
    //     Restaurant::class => RestaurantPolicy::class,
    //     User::class => UserPolicy::class,
    // ];

    public function boot(): void
    {
        // $this->registerPolicies();
    }
}

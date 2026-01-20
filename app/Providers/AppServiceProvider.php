<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use App\Observers\UserObserver;
use App\Policies\BookingPolicy;
use App\Policies\CustomerPolicy;
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
        // User::observe(UserObserver::class);
        // Gate::policy(Customer::class, CustomerPolicy::class);
        // Gate::policy(Booking::class, BookingPolicy::class);
    }
}

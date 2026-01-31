<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Restaurant;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $tenant = Filament::getTenant(); // Restaurant|null

        // ✅ Cache key per user + tenant
        $cacheKey = 'dashboard.stats.'.$user->id.'.'.($tenant?->id ?? 'no-tenant');

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($user, $tenant) {

            // ===== OWNER (scoped on his owned restaurants) =====
            if ($user->hasRole('Owner')) {

                // نحصل على فروع المالك فقط (IDs)
                $ownerRestaurantIds = $user->ownedRestaurants()->pluck('id');

                // لو المالك لا يملك فروعًا بعد
                if ($ownerRestaurantIds->isEmpty()) {
                    return [
                        Stat::make('Your Restaurants', 0),
                        Stat::make('Bookings (All Your Restaurants)', 0),
                        Stat::make('Customers (All Your Restaurants)', 0),
                    ];
                }

                $restaurantsCount = $ownerRestaurantIds->count();

                $bookingsCount = Booking::query()
                    ->whereIn('restaurant_id', $ownerRestaurantIds)
                    ->count();

                $customersCount = Customer::query()
                    ->whereIn('restaurant_id', $ownerRestaurantIds)
                    ->count();

                // إحصائية إضافية قوية: حجوزات اليوم لكل فروع المالك
                $todayBookings = Booking::query()
                    ->whereIn('restaurant_id', $ownerRestaurantIds)
                    ->whereDate('booking_date', today())
                    ->count();

                return [
                    Stat::make('Your Restaurants', $restaurantsCount),
                    Stat::make('Bookings (Your Restaurants)', $bookingsCount),
                    Stat::make('Customers (Your Restaurants)', $customersCount),
                    Stat::make('Today Bookings (Your Restaurants)', $todayBookings),
                ];
            }

            // ===== MANAGER / STAFF (scoped on current tenant only) =====
            if ($tenant) {
                $restaurantId = $tenant->getKey();

                $bookingsCount = Booking::query()
                    ->where('restaurant_id', $restaurantId)
                    ->count();

                $customersCount = Customer::query()
                    ->where('restaurant_id', $restaurantId)
                    ->count();

                $todayBookings = Booking::query()
                    ->where('restaurant_id', $restaurantId)
                    ->whereDate('booking_date', today())
                    ->count();

                return [
                    Stat::make('Bookings (This Restaurant)', $bookingsCount),
                    Stat::make('Customers (This Restaurant)', $customersCount),
                    Stat::make('Today Bookings', $todayBookings),
                ];
            }

            // لو لا يوجد tenant (نادراً)
            return [
                Stat::make('Bookings', 0),
                Stat::make('Customers', 0),
                Stat::make('Today Bookings', 0),
            ];
        });
    }
}

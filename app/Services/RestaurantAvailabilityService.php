<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Restaurant;
use Carbon\Carbon;

class RestaurantAvailabilityService
{
    public function getAvailableSlots(Restaurant $restaurant, string $date): array
    {
        $slotDuration = $restaurant->slot_duration_minutes ?? 90;

        $openingTime = Carbon::parse("$date 12:00");
        $closingTime = Carbon::parse("$date 23:00");

        $slots = [];

        while ($openingTime->lt($closingTime)) {
            $startAt = $openingTime->copy();
            $endAt = $openingTime->copy()->addMinutes($slotDuration);

            if ($endAt->gt($closingTime)) {
                break;
            }

            $overlappingBookings = Booking::where('restaurant_id', $restaurant->id)
                ->where('booking_date', $date)
                ->where('status', 'confirmed')
                ->where(function ($q) use ($startAt, $endAt) {
                    $q->where('start_at', '<', $endAt)
                        ->where('end_at', '>', $startAt);
                })
                ->get();

            $totalBookings = $overlappingBookings->count();
            $totalGuests = $overlappingBookings->sum('guests_count');

            $isAvailable = true;

            if (
                $restaurant->max_bookings_per_slot !== null &&
                $totalBookings >= $restaurant->max_bookings_per_slot
            ) {
                $isAvailable = false;
            }

            if (
                $restaurant->max_guests_per_slot !== null &&
                $totalGuests >= $restaurant->max_guests_per_slot
            ) {
                $isAvailable = false;
            }

            $slots[] = [
                'start_at' => $startAt->format('H:i'),
                'end_at' => $endAt->format('H:i'),
                'is_available' => $isAvailable,
            ];

            $openingTime->addMinutes($slotDuration);
        }

        return $slots;
    }
}

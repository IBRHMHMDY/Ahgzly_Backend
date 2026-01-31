<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Restaurant;
use Carbon\Carbon;

class RestaurantAvailabilityService
{
    public function getAvailableSlots(Restaurant $restaurant, string $date): array
    {
        // 1) Closure check
        $isClosedDate = $restaurant->closures()
            ->whereDate('date', $date)
            ->exists();

        if ($isClosedDate) {
            return [];
        }

        // 2) Working hours by day of week
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0=Sun..6=Sat

        $working = $restaurant->workingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $working || $working->is_closed) {
            return [];
        }

        if (! $working->opens_at || ! $working->closes_at) {
            return [];
        }

        $slotDuration = (int) ($restaurant->slot_duration_minutes ?? 90);

        $openingTime = Carbon::parse("$date {$working->opens_at}");
        $closingTime = Carbon::parse("$date {$working->closes_at}");

        if ($openingTime->gte($closingTime)) {
            return [];
        }

        $slots = [];

        // fetch bookings once (perf) ثم فلترة overlap in-memory (اختياري)
        // أو نترك query لكل slot (يكفي لـ MVP). الأفضل fetch once:
        $bookings = Booking::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('booking_date', $date)
            ->where('status', 'confirmed')
            ->get(['start_at', 'end_at', 'guests_count']);

        while ($openingTime->lt($closingTime)) {
            $startAt = $openingTime->copy();
            $endAt = $openingTime->copy()->addMinutes($slotDuration);

            if ($endAt->gt($closingTime)) {
                break;
            }

            // overlap detection in-memory (fast for typical daily booking count)
            $overlaps = $bookings->filter(function ($b) use ($startAt, $endAt) {
                return Carbon::parse($b->start_at)->lt($endAt)
                    && Carbon::parse($b->end_at)->gt($startAt);
            });

            $totalBookings = $overlaps->count();
            $totalGuests = $overlaps->sum('guests_count');

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

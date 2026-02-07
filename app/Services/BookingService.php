<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Create a booking for the authenticated user.
     *
     * Notes:
     * - We create (or reuse) a Customer profile per restaurant.
     * - We prevent duplicates for the same customer/date/start_at when not cancelled.
     * - We compute end_at if duration_minutes provided (default 90 minutes).
     */
    public function create(User $user, array $payload): Booking
    {
        return DB::transaction(function () use ($user, $payload) {
            /** @var Restaurant $restaurant */
            $restaurant = Restaurant::query()->findOrFail($payload['restaurant_id']);

            $customer = Customer::query()->firstOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    // keep phone as primary key for MVP (plus user_id for consistency)
                    'phone' => $user->phone,
                ],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                ]
            );

            // Ensure user_id stays linked if customer already existed
            if (empty($customer->user_id)) {
                $customer->forceFill(['user_id' => $user->id])->save();
            }

            $startAt = Carbon::createFromFormat('H:i', $payload['start_at']);
            $duration = (int) ($payload['duration_minutes'] ?? ($restaurant->slot_duration_minutes ?? 90));
            $endAt = (clone $startAt)->addMinutes($duration)->format('H:i');

            $exists = Booking::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('customer_id', $customer->id)
                ->whereDate('booking_date', $payload['booking_date'])
                ->where('start_at', $payload['start_at'])
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                abort(422, 'عذراً، لديك حجز مؤكد بالفعل في هذا التوقيت.');
            }

            // Capacity / overbooking protection (Phase 2)
            $maxGuests = $restaurant->max_guests_per_slot ? (int) $restaurant->max_guests_per_slot : null;
            $maxBookings = $restaurant->max_bookings_per_slot ? (int) $restaurant->max_bookings_per_slot : null;

            if ($maxGuests !== null || $maxBookings !== null) {
                // Overlap condition: existing.start_at < new_end AND existing.end_at > new_start
                $aggregate = Booking::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->whereDate('booking_date', $payload['booking_date'])
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('start_at')
                    ->whereNotNull('end_at')
                    ->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $payload['start_at'])
                    ->lockForUpdate()
                    ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(guests_count), 0) as guests_sum')
                    ->first();

                $currentCount = (int) ($aggregate->cnt ?? 0);
                $currentGuests = (int) ($aggregate->guests_sum ?? 0);

                if ($maxBookings !== null && ($currentCount + 1) > $maxBookings) {
                    abort(422, 'عذراً، هذا التوقيت ممتلئ حالياً. جرّب توقيتاً آخر.');
                }

                if ($maxGuests !== null && ($currentGuests + (int) $payload['guests_count']) > $maxGuests) {
                    abort(422, 'عذراً، هذا التوقيت لا يتسع لهذا العدد. قلّل عدد الأفراد أو اختر توقيتاً آخر.');
                }
            }

            return Booking::query()->create([
                'customer_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'booking_date' => $payload['booking_date'],
                'start_at' => $payload['start_at'],
                'end_at' => $endAt,
                'guests_count' => $payload['guests_count'],
                'status' => 'pending',
                'notes' => $payload['notes'] ?? null,
            ]);
        });
    }
}

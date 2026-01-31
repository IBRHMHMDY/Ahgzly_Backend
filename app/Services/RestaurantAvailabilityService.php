<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RestaurantAvailabilityService
{
    /**
     * Cache TTL (seconds)
     * 5 دقائق عادة ممتازة للـ MVP (تقليل الضغط + استجابة سريعة)
     */
    private int $ttl = 300;

    public function getAvailableSlots(Restaurant $restaurant, string $date): array
    {
        // Cache Key per restaurant/date + settings that affect slots
        $key = $this->cacheKey($restaurant, $date);

        // لو عندك Redis/Memcached تقدر تستخدم tags (أفضل للإبطال)
        if ($this->supportsTags()) {
            return Cache::tags($this->cacheTags($restaurant))
                ->remember($key, $this->ttl, fn () => $this->compute($restaurant, $date));
        }

        // Fallback لأي driver لا يدعم tags
        return Cache::remember($key, $this->ttl, fn () => $this->compute($restaurant, $date));
    }

    private function compute(Restaurant $restaurant, string $date): array
    {
        // 1) لو اليوم مغلق بقرار استثنائي (closures) => مفيش Slots
        $isClosedDate = $restaurant->closures()
            ->whereDate('date', $date)
            ->exists();

        if ($isClosedDate) {
            return [];
        }

        // 2) جلب ساعات العمل حسب يوم الأسبوع
        $dayOfWeek = Carbon::createFromFormat('Y-m-d', $date)->dayOfWeek; // 0=Sun..6=Sat

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

        // Guard
        if ($openingTime->gte($closingTime)) {
            return [];
        }

        // 3) تحميل حجوزات اليوم مرة واحدة (Performance)
        // status confirmed فقط، ويمكن توسعته لاحقاً
        $bookings = Booking::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('booking_date', $date)
            ->where('status', 'confirmed')
            ->get(['start_at', 'end_at', 'guests_count']);

        // 4) توليد Slots + حساب Overlap + Capacity
        $slots = [];

        while ($openingTime->lt($closingTime)) {
            $startAt = $openingTime->copy();
            $endAt = $openingTime->copy()->addMinutes($slotDuration);

            if ($endAt->gt($closingTime)) {
                break;
            }

            // Overlap in-memory (مناسب جدًا غالبًا للـ MVP)
            $overlaps = $bookings->filter(function ($b) use ($startAt, $endAt) {
                return Carbon::parse($b->start_at)->lt($endAt)
                    && Carbon::parse($b->end_at)->gt($startAt);
            });

            $totalBookings = $overlaps->count();
            $totalGuests = (int) $overlaps->sum('guests_count');

            $isAvailable = true;

            if (
                $restaurant->max_bookings_per_slot !== null &&
                $totalBookings >= (int) $restaurant->max_bookings_per_slot
            ) {
                $isAvailable = false;
            }

            if (
                $restaurant->max_guests_per_slot !== null &&
                $totalGuests >= (int) $restaurant->max_guests_per_slot
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

    private function cacheKey(Restaurant $restaurant, string $date): string
    {
        // ضمنا إعدادات المطعم المؤثرة على Slots
        // لو تتغير: slot_duration / max_* => الكاش لازم يتغير
        $settingsSig = implode(':', [
            (int) ($restaurant->slot_duration_minutes ?? 90),
            $restaurant->max_bookings_per_slot ?? 'null',
            $restaurant->max_guests_per_slot ?? 'null',
        ]);

        return "restaurants:{$restaurant->id}:available-slots:{$date}:{$settingsSig}";
    }

    private function cacheTags(Restaurant $restaurant): array
    {
        // Tag عام لكل الكاش المتعلق بالمطعم
        return ["restaurant:{$restaurant->id}"];
    }

    private function supportsTags(): bool
    {
        // drivers التي تدعم tags: redis, memcached عادة
        // file/database لا يدعم
        $store = Cache::getStore();

        return method_exists($store, 'tags');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Bookings\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CustomerBookingController extends Controller
{
    private function ensureCustomerRole(Request $request): void
    {
        abort_unless($request->user()?->hasRole('Customer'), 403, 'Customer only');
    }

    public function index(Request $request, Restaurant $restaurant)
    {
        $this->ensureCustomerRole($request);

        abort_unless($restaurant->is_active, 404);

        // العميل مرتبط بحساب user_id
        $bookings = Booking::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereHas('customer', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $bookings,
        ]);
    }

    public function store(StoreBookingRequest $request, Restaurant $restaurant)
    {
        $this->ensureCustomerRole($request);

        abort_unless($restaurant->is_active, 404);

        // 1) إيجاد/إنشاء Customer داخل نفس المطعم مع ربطه بالـ user_id
        $customer = Customer::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $customer) {
            // محاولة دمج حسب الهاتف داخل نفس المطعم (لتجنب تكرار العملاء)
            $customer = Customer::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('phone', $request->string('phone'))
                ->first();

            if ($customer) {
                $customer->update([
                    'user_id' => $request->user()->id,
                    'name' => $request->string('name'),
                    'email' => $request->input('email'),
                ]);
            } else {
                $customer = Customer::query()->create([
                    'restaurant_id' => $restaurant->id,
                    'user_id' => $request->user()->id,
                    'name' => $request->string('name'),
                    'phone' => $request->string('phone'),
                    'email' => $request->input('email'),
                ]);
            }
        }

        // 2) إنشاء الحجز Scoped بـ restaurant
        $booking = Booking::query()->create([
            'restaurant_id' => $restaurant->id,
            'customer_id' => $customer->id,
            'created_by' => null, // Customer عبر API
            'booking_date' => $request->date('booking_date'),
            'start_at' => $request->input('start_at'),
            'end_at' => $request->input('end_at'),
            'guests_count' => (int) $request->input('guests_count'),
            'status' => 'pending',
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'message' => 'Booking created',
            'data' => $booking,
        ], 201);
    }

    public function destroy(Request $request, Restaurant $restaurant, Booking $booking)
    {
        $this->ensureCustomerRole($request);

        abort_unless($restaurant->is_active, 404);

        // حماية: booking لازم تابع لنفس المطعم
        abort_unless((int) $booking->restaurant_id === (int) $restaurant->id, 404);

        // حماية: booking لازم تابع لعميل هذا المستخدم
        $owns = $booking->customer()
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_unless($owns, 403);

        // للـ MVP: الإلغاء = cancelled بدل delete (أفضل للأثر)
        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled',
        ]);
    }
}

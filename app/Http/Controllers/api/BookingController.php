<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // عرض حجوزات المستخدم الحالي فقط
    public function index()
    {
        $user = Auth::user();

        // نبحث عن الحجوزات المرتبطة برقم هاتف أو إيميل المستخدم في جداول العملاء
        // بما أن Booking مرتبط بـ Customer وليس User مباشرة
        $bookings = Booking::whereHas('customer', function ($query) use ($user) {
            $query->where('phone', $user->phone)
                ->orWhere('email', $user->email);
        })->latest()->get();

        return BookingResource::collection($bookings);
    }

    // إنشاء حجز جديد
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_at' => 'required', // يمكن تحسين التحقق من صيغة الوقت
            'guests_count' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($request->restaurant_id);

        // --- الخطوة السحرية: إيجاد أو إنشاء بروفايل العميل داخل المطعم ---
        // نبحث عن العميل في هذا المطعم تحديداً
        $customer = Customer::firstOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'phone' => $user->phone, // نعتمد الهاتف كمعرف أساسي
            ],
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );

        // التحقق من وجود نفس الحجز بالوقت والتاريخ
        $exists = Booking::where('restaurant_id', $restaurant->id)
            ->where('customer_id', $customer->id)
            ->where('booking_date', $request->booking_date)
            ->where('start_at', $request->start_at)
            ->where('status', '!=', 'cancelled') // مسموح بالحجز إذا كان السابق ملغياً
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'عذراً، لديك حجز مؤكد بالفعل في هذا التوقيت.',
            ], 422);
        }
        // --- إنشاء الحجز ---
        $booking = Booking::create([
            'customer_id' => $customer->id,
            'restaurant_id' => $restaurant->id, // للتأكيد (رغم وجوده في العميل)
            'booking_date' => $request->booking_date,
            'start_at' => $request->start_at,
            'guests_count' => $request->guests_count,
            'status' => 'pending', // الحالة الافتراضية
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => new BookingResource($booking),
        ], 201);
    }
}

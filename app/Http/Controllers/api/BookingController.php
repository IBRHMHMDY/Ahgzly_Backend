<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Support\ApiResponse;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    // GET /api/bookings (or legacy /api/mybookings)
    public function index(Request $request)
    {
        $user = $request->user();

        // Prefer stable link by user_id (customer profile), fallback to phone/email for legacy records
        $bookings = Booking::query()
            ->with(['restaurant', 'customer'])
            ->whereHas('customer', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone)
                    ->orWhere('email', $user->email);
            })
            ->latest()
            ->paginate(20);

        return ApiResponse::success(
            BookingResource::collection($bookings)->response()->getData(true),
            'Bookings fetched successfully'
        );
    }

    // POST /api/bookings
    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->create($request->user(), $request->validated());

        return ApiResponse::success(
            new BookingResource($booking->load(['restaurant', 'customer'])),
            'Booking created successfully',
            201
        );
    }
}

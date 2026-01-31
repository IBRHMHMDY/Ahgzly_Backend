<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\RestaurantAvailabilityService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class RestaurantAvailabilityController extends Controller
{
    public function index(
        Restaurant $restaurant,
        Request $request,
        RestaurantAvailabilityService $service
    ) {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $slots = $service->getAvailableSlots(
            $restaurant,
            $request->date
        );

        return ApiResponse::success(
            data: $slots,
            message: 'Available slots fetched successfully'
        );
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AvailableSlotsRequest;
use App\Models\Restaurant;
use App\Services\RestaurantAvailabilityService;
use App\Support\ApiResponse;

class RestaurantAvailabilityController extends Controller
{
    public function index(
        Restaurant $restaurant,
        AvailableSlotsRequest $request,
        RestaurantAvailabilityService $service
    ) {
        $date = $request->validated()['date'];

        $slots = $service->getAvailableSlots($restaurant, $date);

        return ApiResponse::success(
            data: $slots,
            message: 'Available slots fetched successfully'
        );
    }
}

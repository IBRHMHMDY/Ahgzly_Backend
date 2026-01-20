<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('is_active', true)->get();

        return RestaurantResource::collection($restaurants);
    }

    public function show($id)
    {
        $restaurant = Restaurant::where('is_active', true)->findOrFail($id);

        return new RestaurantResource($restaurant);
    }
}

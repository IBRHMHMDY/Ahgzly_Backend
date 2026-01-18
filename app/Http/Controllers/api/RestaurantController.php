<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->select(['id', 'name', 'slug', 'phone', 'address'])
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'data' => $restaurants,
        ]);
    }

    public function show(Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        return response()->json([
            'data' => $restaurant->only(['id', 'name', 'slug', 'phone', 'address']),
        ]);
    }
}

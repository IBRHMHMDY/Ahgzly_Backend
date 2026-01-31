<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $restaurants = $user->favoriteRestaurants()
            ->where('restaurants.is_active', true)
            ->latest('restaurant_favorites.created_at')
            ->paginate(20);

        return ApiResponse::success(
            RestaurantResource::collection($restaurants)->response()->getData(true),
            'Favorites fetched successfully'
        );
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $user = $request->user();

        if (! $restaurant->is_active) {
            return ApiResponse::error('Restaurant is not active', 404);
        }

        $user->favoriteRestaurants()->syncWithoutDetaching([$restaurant->id]);

        return ApiResponse::success([
            'restaurant_id' => $restaurant->id,
            'is_favorited' => true,
        ], 'Added to favorites', 201);
    }

    public function destroy(Request $request, Restaurant $restaurant)
    {
        $user = $request->user();

        $user->favoriteRestaurants()->detach($restaurant->id);

        return ApiResponse::success([
            'restaurant_id' => $restaurant->id,
            'is_favorited' => false,
        ], 'Removed from favorites');
    }
}

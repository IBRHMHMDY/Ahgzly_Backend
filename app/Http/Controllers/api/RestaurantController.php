<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->when($user, function ($query) use ($user) {
                $query->withExists([
                    'favoritedByUsers as is_favorited' => fn ($q) => $q->where('user_id', $user->id),
                ]);
            })
            ->get();

        return ApiResponse::success(
            RestaurantResource::collection($restaurants),
            'Restaurants fetched successfully'
        );
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $restaurant = Restaurant::query()
            ->where('is_active', true)
            ->when($user, function ($query) use ($user) {
                $query->withExists([
                    'favoritedByUsers as is_favorited' => fn ($q) => $q->where('user_id', $user->id),
                ]);
            })
            ->findOrFail($id);

        return ApiResponse::success(
            new RestaurantResource($restaurant),
            'Restaurant fetched successfully'
        );
    }
}

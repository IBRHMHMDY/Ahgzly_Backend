<?php

namespace App\Http\Middleware;

use App\Support\RestaurantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetRestaurantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        // أولوية للهيدر (مفيد للاختبار أو API داخليًا)
        $rid = $request->header('X-Restaurant-Id');

        // أو من session (ده اللي هنستخدمه غالبًا مع Filament لاحقًا)
        if (! $rid && $request->hasSession()) {
            $rid = $request->session()->get('current_restaurant_id');
        }

        RestaurantContext::set($rid ? (int) $rid : null);

        return $next($request);
    }
}

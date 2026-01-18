<?php

namespace App\Support;

class RestaurantContext
{
    private static ?int $restaurantId = null;

    public static function set(?int $restaurantId): void
    {
        self::$restaurantId = $restaurantId;
    }

    public static function get(): ?int
    {
        return self::$restaurantId;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantWorkingHour extends Model
{
    protected $fillable = [
        'restaurant_id',
        'day_of_week',
        'opens_at',
        'closes_at',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantClosure extends Model
{
    protected $fillable = [
        'restaurant_id',
        'date',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}

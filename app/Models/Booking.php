<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'restaurant_id',
        'customer_id',
        'created_by',
        'booking_date',
        'start_at',
        'end_at',
        'guests_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

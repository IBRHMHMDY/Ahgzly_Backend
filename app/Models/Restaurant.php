<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Restaurant extends Model implements HasAvatar
{
    protected $fillable = [
        'owner_id', 'name', 'slug', 'phone', 'address', 'is_active', 'logo', 'slot_duration_minutes', 'max_guests_per_slot', 'max_bookings_per_slot',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'slot_duration_minutes' => 'integer',
        'max_guests_per_slot' => 'integer',
        'max_bookings_per_slot' => 'integer',
    ];


    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'restaurant_user')
            ->withPivot(['is_default', 'is_active'])
            ->withTimestamps();
    }


    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'restaurant_favorites')->withTimestamps();
    }


    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->logo) {
            return null; // يرجع للأحرف تلقائياً
        }

        return asset('storage/'.$this->logo);
    }
}

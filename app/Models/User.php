<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedRestaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class, 'owner_id');
    }

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class)
            ->withPivot(['is_default', 'is_active'])
            ->withTimestamps();
    }

    /**
     * هل المستخدم مرتبط بهذا المطعم (Owner عبر owner_id أو أي دور عبر pivot)
     */
    public function canAccessRestaurant(int $restaurantId): bool
    {
        if ($this->hasRole('Owner') && $this->ownedRestaurants()->whereKey($restaurantId)->exists()) {
            return true;
        }

        return $this->restaurants()
            ->whereKey($restaurantId)
            ->wherePivot('is_active', true)
            ->exists();
    }
}

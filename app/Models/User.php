<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasTenants
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

    public function canAccessPanel(Panel $panel): bool
    {
        // فقط أدوار Filament
        return $this->hasAnyRole(['Owner', 'Manager', 'Staff']);
    }

    public function getTenants(Panel $panel): Collection
    {
        // Owner: المطاعم التي يملكها
        if ($this->hasRole('Owner')) {
            return $this->ownedRestaurants()->where('is_active', true)->get();
        }

        // Manager/Staff: المطاعم المرتبط بها عبر pivot
        return $this->restaurants()
            ->where('restaurants.is_active', true)
            ->wherePivot('is_active', true)
            ->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        /** @var Restaurant $tenant */
        return $this->canAccessRestaurant($tenant->id);
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        // 1) لو في pivot مطعم افتراضي
        $default = $this->restaurants()
            ->wherePivot('is_active', true)
            ->wherePivot('is_default', true)
            ->first();

        // 2) لو Owner ومفيش default في pivot: أول مطعم يملكه
        if (! $default && $this->hasRole('Owner')) {
            $default = $this->ownedRestaurants()->where('is_active', true)->first();
        }

        return $default;
    }
}

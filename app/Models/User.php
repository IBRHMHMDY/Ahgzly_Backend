<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasName, HasTenants
{
    use HasApiTokens, HasDatabaseNotifications, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'string',
    ];

    // --- العلاقات ---

    public function ownedRestaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class, 'owner_id');
    }

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_user')
            ->withPivot(['is_default', 'is_active'])
            ->withTimestamps();
    }

    public function getFilamentName(): string
    {
        $rolesMap = [
            'Owner' => 'مالك',
            'Manager' => 'مدير',
            'Staff' => 'موظف',
            'Customer' => 'عميل',
        ];

        $role = $this->getRoleNames()->first();
        $roleLabel = $rolesMap[$role] ?? $role ?? 'بدون دور';

        return "{$this->name}\n{$roleLabel}";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'sysadmin') {
            return $this->hasRole('SysAdmin');
        }

        // ✅ Panel admin الأساسي: Owner/Manager/Staff (وسيسمح للـ SysAdmin أيضًا لو تحب)
        return $this->hasAnyRole(['SysAdmin', 'Owner', 'Manager', 'Staff']);
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($this->hasRole('SysAdmin')) {
            return Restaurant::query()->get();
        }
        // نجمع المطاعم المملوكة + المطاعم التي يعمل بها
        $owned = $this->ownedRestaurants()->where('is_active', true)->get();

        $workedAt = $this->restaurants()
            ->where('restaurants.is_active', true)
            ->wherePivot('is_active', true)
            ->get();

        return $owned->merge($workedAt)->unique('id');
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->hasRole('SysAdmin')) {
            return true;
        }

        // 1. هل هو المالك؟
        if ($this->ownedRestaurants()->whereKey($tenant->id)->exists()) {
            return true;
        }

        // 2. هل هو موظف مفعل؟
        return $this->restaurants()
            ->whereKey($tenant->id)
            ->wherePivot('is_active', true)
            ->exists();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        // الأولوية للمطعم المحدد كـ default في الـ pivot
        $default = $this->restaurants()
            ->wherePivot('is_active', true)
            ->wherePivot('is_default', true)
            ->first();

        // إذا لم يوجد، نأخذ أول مطعم يملكه
        if (! $default) {
            $default = $this->ownedRestaurants()->where('is_active', true)->first();
        }

        // إذا لم يوجد، نأخذ أول مطعم يعمل به
        if (! $default) {
            $default = $this->restaurants()->where('restaurants.is_active', true)->first();
        }

        return $default;
    }

    public function favoriteRestaurants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_favorites')->withTimestamps();
    }
}

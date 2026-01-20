<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Filament\Facades\Filament;

trait RestaurantScope
{
    protected function tenantId(): ?int
    {
        $tenant = Filament::getTenant();

        return $tenant?->getKey();
    }

    protected function hasTenant(User $user): bool
    {
        return (bool) $this->tenantId();
    }

    // للجداول التي تحتوي restaurant_id
    protected function sameRestaurantId(User $user, $record): bool
    {
        $tenantId = $this->tenantId();

        return $tenantId && (int) $record->restaurant_id === (int) $tenantId;
    }

    // للمستخدمين المرتبطين عبر pivot user_restaurants
    protected function userBelongsToTenant(User $targetUser): bool
    {
        $tenantId = $this->tenantId();

        return $tenantId && $targetUser->restaurants()->whereKey($tenantId)->exists();
    }
}

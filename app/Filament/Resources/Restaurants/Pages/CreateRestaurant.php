<?php

namespace App\Filament\Resources\Restaurants\Pages;

use App\Filament\Resources\Restaurants\RestaurantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // إجبار ملكية المطعم
        $data['owner_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $restaurant = $this->record;
        $owner = auth()->user();

        $hasDefault = $owner->restaurants()->wherePivot('is_default', true)->exists();

        $owner->restaurants()->syncWithoutDetaching([
            $restaurant->id => [
                'is_active' => true,
                'is_default' => ! $hasDefault,
            ],
        ]);
    }
}

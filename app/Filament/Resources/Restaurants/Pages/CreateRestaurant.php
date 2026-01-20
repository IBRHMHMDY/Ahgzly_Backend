<?php

namespace App\Filament\Resources\Restaurants\Pages;

use App\Filament\Resources\Restaurants\RestaurantResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function afterCreate(): void
    {
        // 1. المطعم تم إنشاؤه بالفعل وموجود في $this->record
        $restaurant = $this->record;
        $user = Auth::user();

        // 2. نربط المالك بالمطعم في جدول الـ Pivot لكي يظهر في قائمة التنقل
        $user->restaurants()->syncWithoutDetaching([
            $restaurant->id => [
                'is_active' => true,
                'is_default' => false,
            ],
        ]);
    }

    protected function getRedirectUrl(): string
    {
        // التوجيه لصفحة القائمة (الجدول) بدلاً من صفحة التعديل
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;

        $user->restaurants()->syncWithoutDetaching([
            Filament::getTenant()->id => [
                'is_active' => true,
                'is_default' => false,
            ],
        ]);

        $user = $this->record; // الموظف الجديد
        $tenant = \Filament\Facades\Filament::getTenant(); // المطعم الحالي

        // ربطه في جدول الـ Pivot
        $user->restaurants()->attach($tenant->id);

    }

    protected function getRedirectUrl(): string
    {
        // التوجيه لصفحة القائمة (الجدول) بدلاً من صفحة التعديل
        return $this->getResource()::getUrl('index');
    }
}

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
        $tenantId = Filament::getTenant()->id;

        // اربط الموظف بالمطعم الحالي مرة واحدة فقط (آمن ضد التكرار)
        $user->restaurants()->syncWithoutDetaching([
            $tenantId => [
                'is_active' => true,
                'is_default' => false,
            ],
        ]);

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

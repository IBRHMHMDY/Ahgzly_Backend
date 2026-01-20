<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenantId = Filament::getTenant()?->getKey();
        $data['restaurant_id'] = $tenantId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // التوجيه لصفحة القائمة (الجدول) بدلاً من صفحة التعديل
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenantId = Filament::getTenant()?->getKey();

        // إجبار restaurant_id + created_by من السيرفر
        $data['restaurant_id'] = $tenantId;
        $data['created_by'] = auth()->id();

        return $data;
    }
}

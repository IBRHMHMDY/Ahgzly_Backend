<?php

namespace App\Filament\Resources\Restaurants\RelationManagers\WorkingHours\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WorkingHoursForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('day_of_week')
                ->required()
                ->options([
                    0 => 'Sunday',
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                ])
                ->disabled(fn ($record) => filled($record)),

            Toggle::make('is_closed')
                ->label('Closed')
                ->live(),

            TimePicker::make('opens_at')
                ->label('Opens At')
                ->seconds(false)
                ->required(fn ($get) => ! $get('is_closed'))
                ->disabled(fn ($get) => $get('is_closed')),

            TimePicker::make('closes_at')
                ->label('Closes At')
                ->seconds(false)
                ->required(fn ($get) => ! $get('is_closed'))
                ->disabled(fn ($get) => $get('is_closed')),
        ]);
    }
}

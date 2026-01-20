<?php

namespace App\Filament\Resources\Restaurants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('owner_id')
                    ->default(fn () => auth()->id())
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('address')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}

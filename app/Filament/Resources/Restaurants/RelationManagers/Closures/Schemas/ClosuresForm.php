<?php

namespace app\Filament\Resources\Restaurants\RelationManagers\Closures\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClosuresForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('date')
                ->required()
                ->native(false),

            TextInput::make('reason')
                ->maxLength(255)
                ->placeholder('Optional'),
        ]);
    }
}

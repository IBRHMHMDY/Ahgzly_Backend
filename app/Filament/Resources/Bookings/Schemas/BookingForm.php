<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('restaurant_id')
                    ->default(fn () => Filament::getTenant()?->getKey())
                    ->required(),
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
                Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->required(),
                Select::make('customer_id')
                    ->relationship(
                        name: 'customer',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $tenant = \Filament\Facades\Filament::getTenant();

                            return $query->when($tenant, fn ($q) => $q->where('restaurant_id', $tenant->getKey()));
                        }
                    )
                    ->searchable()
                    ->required(),
                TextInput::make('created_by')
                    ->numeric()
                    ->default(null),
                DatePicker::make('booking_date')
                    ->required(),
                DateTimePicker::make('start_at'),
                DateTimePicker::make('end_at'),
                TextInput::make('guests_count')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

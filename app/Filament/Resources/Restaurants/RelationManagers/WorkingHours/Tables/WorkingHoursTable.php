<?php

namespace app\Filament\Resources\Restaurants\RelationManagers\WorkingHours\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkingHoursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn ($state) => [
                        'Sunday',
                        'Monday',
                        'Tuesday',
                        'Wednesday',
                        'Thursday',
                        'Friday',
                        'Saturday',
                    ][$state] ?? (string) $state),

                IconColumn::make('is_closed')
                    ->boolean()
                    ->label('Closed'),

                TextColumn::make('opens_at')->label('Opens')->placeholder('-'),
                TextColumn::make('closes_at')->label('Closes')->placeholder('-'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}

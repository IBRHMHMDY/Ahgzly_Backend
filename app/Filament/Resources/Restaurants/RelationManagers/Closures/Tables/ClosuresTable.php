<?php

namespace app\Filament\Resources\Restaurants\RelationManagers\Closures\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClosuresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')->date()->label('Date'),
                TextColumn::make('reason')->label('Reason')->wrap(),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Closure'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

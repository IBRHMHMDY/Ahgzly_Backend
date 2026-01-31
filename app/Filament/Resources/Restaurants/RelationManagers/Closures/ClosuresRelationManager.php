<?php

namespace App\Filament\Resources\Restaurants\RelationManagers;

use app\Filament\Resources\Restaurants\RelationManagers\Closures\Schemas\ClosuresForm;
use app\Filament\Resources\Restaurants\RelationManagers\Closures\Tables\ClosuresTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClosuresRelationManager extends RelationManager
{
    protected static string $relationship = 'closures';

    public function form(Schema $form): Schema
    {
        return ClosuresForm::configure($form);
    }

    public function table(Table $table): Table
    {
        return ClosuresTable::configure($table);
    }
}

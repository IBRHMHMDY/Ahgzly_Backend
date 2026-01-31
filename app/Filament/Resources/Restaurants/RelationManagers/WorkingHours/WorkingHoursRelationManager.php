<?php

namespace App\Filament\Resources\Restaurants\RelationManagers;

use App\Filament\Resources\Restaurants\RelationManagers\WorkingHours\Schemas\WorkingHoursForm;
use App\Filament\Resources\Restaurants\RelationManagers\WorkingHours\Tables\WorkingHoursTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WorkingHoursRelationManager extends RelationManager
{
    protected static string $relationship = 'workingHours';

    public function form(Schema $form): Schema
    {
        return WorkingHoursForm::configure($form);
    }

    public function table(Table $table): Table
    {
        return WorkingHoursTable::configure($table);
    }
}

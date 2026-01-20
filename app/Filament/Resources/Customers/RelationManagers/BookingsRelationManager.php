<?php

namespace App\Filament\Resources\Customers\RelationManagers;

// use Filament\Forms; // ❌
// use Filament\Forms\Form; // ❌
use Filament\Resources\RelationManagers\RelationManager; // ✅
use Filament\Schemas\Components\TextInput; // ✅
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'سجل الحجوزات';

    // التحديث ليتوافق مع نسختك
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([ // بدلاً من schema()
                TextInput::make('guests_count')
                    ->required()
                    ->numeric()
                    ->label('عدد الضيوف'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('booking_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('الوقت')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                    }),
            ]);
    }
}

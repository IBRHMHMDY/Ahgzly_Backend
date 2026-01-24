<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('booking_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),

                TextColumn::make('start_at')
                    ->label('الوقت')
                    ->time('h:i A')
                    ->sortable(),

                TextColumn::make('guests_count')
                    ->label('الضيوف')
                    ->badge(),

                TextColumn::make('status')
                    ->label('حالة الحجز')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'confirmed' => 'info',
                        'attended' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'attended' => 'تم الحضور',
                        'cancelled' => 'ملغي',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('تصفية بالحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'attended' => 'تم الحضور',
                        'cancelled' => 'ملغي',
                    ]),

                Filter::make('booking_date')
                    ->schema([
                        DatePicker::make('from')->label('من تاريخ'),
                        DatePicker::make('to')->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $d) => $q->whereDate('booking_date', '>=', $d))
                            ->when($data['to'], fn ($q, $d) => $q->whereDate('booking_date', '<=', $d));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('confirm')
                        ->label('تأكيد')
                        ->action(fn (Booking $record) => $record->update(['status' => 'confirmed']))
                        ->visible(fn (Booking $record) => $record->status === 'pending'),

                    Action::make('attend')
                        ->label('حضر')
                        ->action(fn (Booking $record) => $record->update(['status' => 'attended']))
                        ->visible(fn (Booking $record) => $record->status === 'confirmed'),

                    Action::make('cancel')
                        ->label('إلغاء')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Booking $record) => $record->update(['status' => 'cancelled']))
                        ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'confirmed'])),
                    // ActionsEditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

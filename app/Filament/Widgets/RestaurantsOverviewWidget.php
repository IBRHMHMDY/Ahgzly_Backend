<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RestaurantsOverviewWidget extends BaseWidget
{
    protected static ?string $heading = 'Restaurants Overview';

    // عدد الصفوف في الواجهة
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('Owner') ?? false;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query(
                Restaurant::query()
                    ->where('owner_id', $user->id)
                    ->withCount([
                        // حجوزات اليوم
                        'bookings as bookings_today_count' => fn ($q) => $q->whereDate('booking_date', today()),

                        // حجوزات الأسبوع القادم
                        'bookings as bookings_next_7_days_count' => fn ($q) => $q->whereBetween('booking_date', [today(), today()->addDays(7)]),

                        // عدد العملاء
                        'customers as customers_count',
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Restaurant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bookings_today_count')
                    ->label('Today Bookings')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bookings_next_7_days_count')
                    ->label('Next 7 Days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customers_count')
                    ->label('Customers')
                    ->sortable(),

                // لو عندك status أو is_active
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('bookings_today_count', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('No restaurants found')
            ->emptyStateDescription('Create restaurants first to see the overview here.');
    }
}

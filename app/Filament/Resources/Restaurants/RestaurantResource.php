<?php

namespace App\Filament\Resources\Restaurants;

use App\Filament\Resources\Restaurants\Pages\CreateRestaurant;
use App\Filament\Resources\Restaurants\Pages\EditRestaurant;
use App\Filament\Resources\Restaurants\Pages\ListRestaurants;
use App\Filament\Resources\Restaurants\Schemas\RestaurantForm;
use App\Filament\Resources\Restaurants\Tables\RestaurantsTable;
use App\Models\Restaurant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Restaurants';

    public static function form(Schema $schema): Schema
    {
        return RestaurantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurants::route('/'),
            'create' => CreateRestaurant::route('/create'),
            'edit' => EditRestaurant::route('/{record}/edit'),
        ];
    }

    protected static bool $isScopedToTenant = false;

    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     $user = auth()->user();

    //     // Owner يرى مطاعمه فقط
    //     return $query->when($user?->hasRole('Owner'), fn (Builder $q) => $q->where('owner_id', $user->id));
    // }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()?->hasRole('Owner');
    // }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->hasAnyRole(['Owner', 'Manager']) ?? false;
    // }
}

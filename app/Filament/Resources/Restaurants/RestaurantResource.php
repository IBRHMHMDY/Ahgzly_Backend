<?php

namespace App\Filament\Resources\Restaurants;

use App\Filament\Resources\Restaurants\Pages\CreateRestaurant;
use App\Filament\Resources\Restaurants\Pages\EditRestaurant;
use App\Filament\Resources\Restaurants\Pages\ListRestaurants;
use App\Filament\Resources\Restaurants\Schemas\RestaurantForm;
use App\Filament\Resources\Restaurants\Tables\RestaurantsTable;
use App\Models\Restaurant;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'إدارة المطاعم';

    protected static ?int $navigationSort = 0; // نضعه في الأول

    // 🔴 هام جداً: نلغي السكوب التلقائي لنسمح للمالك برؤية كل فروعه وإدارتها
    protected static bool $isScopedToTenant = false;

    // protected static ?string $tenantOwnershipRelationshipName = 'restaurants';

    // ✅ ونقوم نحن بالفلترة يدوياً: المالك يرى مطاعمه فقط
    public static function getEloquentQuery(): Builder
    {
        // إذا كان المستخدم Super Admin (مستقبلاً) يرى الكل
        // حالياً: المالك يرى ما يملكه فقط
        return parent::getEloquentQuery()->where('owner_id', Auth::id());
    }

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
            \App\Filament\Resources\Restaurants\RelationManagers\WorkingHoursRelationManager::class,
            \App\Filament\Resources\Restaurants\RelationManagers\ClosuresRelationManager::class,
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
}

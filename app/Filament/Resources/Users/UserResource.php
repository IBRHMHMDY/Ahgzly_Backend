<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'الموظفين'; // تسمية عربية مناسبة

    protected static ?int $navigationSort = 2;

    // تفعيل الـ Tenancy Scope لهذا الـ Resource
    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('restaurants', function ($query) {
                $query->where('restaurants.id', Filament::getTenant()->id);
            });
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    // /* ==============================
    //     Permissions
    // ============================== */

    // public static function canViewAny(): bool
    // {
    //     return in_array(auth()->user()->role, [
    //         UserRole::OWNER,
    //         UserRole::MANAGER,
    //         UserRole::STAFF,
    //     ]);
    // }

    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->role === UserRole::OWNER->value;
    // }

    // public static function canCreate(): bool
    // {
    //     return in_array(
    //         auth()->user()?->role,
    //         [
    //             UserRole::OWNER->value,
    //             UserRole::MANAGER->value,
    //         ]
    //     );
    // }

    // public static function canEdit($record): bool
    // {
    //     return in_array(
    //         auth()->user()?->role,
    //         [
    //             UserRole::OWNER->value,
    //             UserRole::MANAGER->value,
    //         ]
    //     );
    // }

    // public static function canDelete($record): bool
    // {
    //     return in_array(
    //         auth()->user()?->role,
    //         [
    //             UserRole::OWNER->value,
    //             UserRole::MANAGER->value,
    //         ]
    //     );
    // }
}

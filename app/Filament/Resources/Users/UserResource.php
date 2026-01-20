<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'الموظفين'; // تسمية عربية مناسبة

    protected static ?int $navigationSort = 2;

    // 1. تفعيل السكوب الخاص بالـ Tenant
    protected static bool $isScopedToTenant = true;

    // 2. تحديد اسم العلاقة الموجودة في مودل User والتي تربطه بالمطعم
    protected static ?string $tenantRelationshipName = 'restaurants';

    protected static ?string $tenantOwnershipRelationshipName = 'restaurants';

    public static function getEloquentQuery(): Builder
    {
        // 1. نبدأ بالاستعلام الأساسي
        $query = parent::getEloquentQuery();
        $user = Auth::user();
        // 2. إخفاء المستخدم الحالي (أنا لا أرى نفسي) - كما فعلنا سابقاً
        $query->where('id', '!=', Auth::id());

        // 3. 👇 الإضافة الجديدة: إخفاء أي مستخدم لديه دور 'Owner'
        $query->whereDoesntHave('roles', function (Builder $q) {
            $q->where('name', 'Owner');
        });
        // إذا كان المستخدم مدير، يرى فقط الـ Staff في هذا المطعم
        if ($user->hasRole('Manager')) {
            $query->whereHas('roles', function (Builder $q) {
                $q->where('name', 'Staff');
            });
        }

        return $query;
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
}

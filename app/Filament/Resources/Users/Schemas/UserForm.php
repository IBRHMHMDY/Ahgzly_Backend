<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الموظف')
                ->schema([
                    TextInput::make('name')
                        ->label('الاسم')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        // التأكد أن الإيميل غير مستخدم في جدول المستخدمين
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        // مطلوبة فقط عند الإنشاء
                        ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                        ->dehydrated(fn ($state) => filled($state)) // لا تحفظ إذا كان فارغاً (عند التعديل)
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)), // 👈 هذه هي الدالة الصحيحة للهاش

                    Select::make('roles')
                        ->label('الدور الوظيفي')
                        ->relationship('roles', 'name', function (Builder $query) {
                            // نسمح للمالك بتعيين مديرين أو موظفين فقط
                            return $query->whereIn('name', ['Manager', 'Staff']);
                        })
                        ->required()
                        ->preload(),
                ])->columns(2),
        ]);
    }
}

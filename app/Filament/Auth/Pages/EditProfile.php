<?php

namespace App\Filament\Auth\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('تغيير كلمة المرور')
                    ->description('اتركها فارغة إذا كنت لا تريد تغييرها')
                    ->components([
                        TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->rule(\Illuminate\Validation\Rules\Password::default()),

                        TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        // العودة إلى الصفحة الرئيسية للوحة التحكم (Dashboard)
        return filament()->getUrl();
    }
}

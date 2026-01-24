<?php

namespace App\Filament\Resources\Restaurants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تفاصيل المطعم')
                    ->components([
                        FileUpload::make('logo')
                            ->label('شعار المطعم')
                            ->image()
                            ->imageEditor() // اختياري
                            ->directory('restaurants/logos')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions([
                                '1:1',
                            ])
                            ->imageEditorViewportWidth(256)
                            ->imageEditorViewportHeight(256),
                        TextInput::make('name')
                            ->label('اسم المطعم')
                            ->required()
                            ->live(onBlur: true) // تحديث فوري
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('المعرف في الرابط (Slug)')
                            ->required()
                            ->readOnly() // يولد تلقائياً
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        TextInput::make('address')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('مفعل؟')
                            ->default(true)
                            ->helperText('إغلاق هذا الخيار سيخفي المطعم من التطبيق.'),

                        // نربط المطعم بالمالك الحالي تلقائياً (مخفي)
                        Hidden::make('owner_id')
                            ->default(Auth::id()),
                    ])->columns(2),
            ]);
    }
}

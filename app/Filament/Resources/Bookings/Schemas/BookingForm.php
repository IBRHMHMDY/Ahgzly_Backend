<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\Booking;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الحجز')
                    ->schema([
                        // اختيار العميل: يجب أن يظهر عملاء المطعم الحالي فقط
                        // Filament يتعامل مع الـ Scoping تلقائياً إذا كانت العلاقات صحيحة
                        Select::make('customer_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->label('الاسم'),
                                TextInput::make('phone')
                                    ->required()
                                    ->tel()
                                    ->label('رقم الهاتف'),
                            ]),

                        DatePicker::make('booking_date')
                            ->label('تاريخ الحجز')
                            ->required()
                            ->default(now()),

                        TimePicker::make('start_at')
                            ->label('وقت الحضور')
                            ->seconds(false)
                            ->required()
                            ->rule(function (Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    // نجلب البيانات من الحقول الأخرى في الفورم
                                    $customerId = $get('customer_id');
                                    $date = $get('booking_date');

                                    // إذا كانت البيانات ناقصة لا نفحص
                                    if (! $customerId || ! $date || ! $value) {
                                        return;
                                    }

                                    // نبحث عن تكرار
                                    $exists = Booking::where('customer_id', $customerId)
                                        ->where('booking_date', $date)
                                        ->where('start_at', $value) // الوقت المدخل
                                        ->where('status', '!=', 'cancelled')
                                        ->exists();

                                    if ($exists) {
                                        $fail('يوجد حجز مسجل لهذا العميل في نفس التوقيت.');
                                    }
                                };
                            }),
                        TextInput::make('guests_count')
                            ->label('عدد الضيوف')
                            ->numeric()
                            ->default(2)
                            ->required(),
                        Select::make('status')
                            ->label('حالة الحجز')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'confirmed' => 'مؤكد',
                                'cancelled' => 'ملغي',
                                'completed' => 'مكتمل',
                            ])
                            ->default('pending')
                            ->required(),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}

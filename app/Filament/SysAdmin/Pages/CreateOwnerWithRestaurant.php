<?php

namespace App\Filament\SysAdmin\Pages;

use App\Models\Restaurant;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateOwnerWithRestaurant extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Create Owner + Restaurant';

    protected static ?string $title = 'Create Owner + First Restaurant';

    protected string $view = 'filament.sysadmin.create-owner-with-restaurant';

    protected static ?string $slug = 'create-owner-with-restaurant';

    public ?array $data = [];

    /** ✅ منع الوصول للصفحة إلا SysAdmin حتى لو كتب URL */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasRole('SysAdmin');
    }

    /** ✅ لا تسجلها في القائمة الجانبية إلا SysAdmin */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->hasRole('SysAdmin');
    }

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasRole('SysAdmin'), 403);
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('معلومات المالك')
                    ->schema([
                        TextInput::make('owner_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('owner_email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email'),
                        TextInput::make('owner_phone')
                            ->tel()
                            ->required()
                            ->unique(User::class, 'phone'),

                        TextInput::make('owner_password')->password()->required()->minLength(8),
                    ])
                    ->columns(2),

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

                        TextInput::make('slot_duration_minutes')
                            ->label('مدة الحجز (بالدقائق)')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(360)
                            ->default(90)
                            ->helperText('المدة الافتراضية للحجز عند إنشاء end_at.'),

                        TextInput::make('max_guests_per_slot')
                            ->label('الحد الأقصى للضيوف لكل توقيت')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->helperText('اتركه فارغاً لتعطيل حد الضيوف.'),

                        TextInput::make('max_bookings_per_slot')
                            ->label('الحد الأقصى لعدد الحجوزات لكل توقيت')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->helperText('اتركه فارغاً لتعطيل حد عدد الحجوزات.'),

                        Toggle::make('is_active')
                            ->label('مفعل؟')
                            ->default(true)
                            ->helperText('إغلاق هذا الخيار سيخفي المطعم من التطبيق.'),
                    ])->columns(2),
            ]);
    }

    public function submit(): void
    {
        abort_unless(Auth::user()->hasRole('SysAdmin'), 403);

        $state = $this->form->getState();

        DB::transaction(function () use ($state) {
            $owner = User::create([
                'name' => $state['owner_name'],
                'email' => $state['owner_email'],
                'phone' => $state['owner_phone'],
                'password' => Hash::make($state['owner_password']),
            ]);

            $owner->assignRole('Owner');

            $restaurant = Restaurant::create([
                'name' => $state['name'],
                'slug' => $state['slug'],
                'phone' => $state['phone'],
                'address' => $state['address'],
                'owner_id' => $owner->id,
                'is_active' => $state['is_active'],
            ]);

            // ربطه بالـ pivot ليظهر له tenant menu
            $owner->restaurants()->attach($restaurant->id);
        });

        Notification::make()
            ->title('Owner and Restaurant created successfully')
            ->success()
            ->send();

        // ممكن تسيبه يرجع لقائمة الملاك/المطاعم
        $this->redirect(Filament::getUrl());
    }
}

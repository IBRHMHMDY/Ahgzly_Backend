<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected string $view = 'filament.pages.edit-profile';

    protected static ?string $title = 'تعديل الملف الشخصي';

    protected static ?string $slug = 'profile'; // الرابط سيكون /admin/profile

    // إخفاء الصفحة من القائمة الجانبية
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        // تعبئة الفورم ببيانات المستخدم الحالي
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('البيانات الشخصية')
                ->schema([
                    TextInput::make('name')
                        ->label('الاسم الكامل')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true, table: 'users', column: 'email', ignorable: fn () => auth()->user()),

                    // حقول تغيير كلمة المرور
                    TextInput::make('password')
                        ->label('كلمة المرور الجديدة')
                        ->password()
                        ->revealable()
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                        ->dehydrated(fn ($state) => filled($state)) // لا ترسل الحقل للحفظ إذا كان فارغاً
                        ->required(fn ($livewire) => filled($livewire->data['password_confirmation'] ?? null)) // مطلوب فقط لو كتب تأكيد
                        ->confirmed(), // يجب أن يطابق حقل password_confirmation

                    TextInput::make('password_confirmation')
                        ->label('تأكيد كلمة المرور')
                        ->password()
                        ->revealable()
                        ->dehydrated(false), // لا نحتاج لحفظ هذا الحقل في الداتابيس
                ])->columns(2),
        ])
            ->statePath('data');
    }

    public function save(): void
    {
        // التحقق من صحة البيانات
        $data = $this->form->getState();

        try {
            $user = Auth::user();

            // تحديث البيانات الأساسية
            $user->name = $data['name'];
            $user->email = $data['email'];

            // تحديث كلمة المرور فقط إذا تم إدخالها
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            Notification::make()
                ->title('تم تحديث الملف الشخصي بنجاح')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('حدث خطأ')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

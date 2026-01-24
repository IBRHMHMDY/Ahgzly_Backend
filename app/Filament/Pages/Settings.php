<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static bool $isTenantAware = false;

    protected static ?string $title = 'إعدادات النظام';

    protected static ?string $slug = 'settings'; // 👈 هذا يجعل الرابط /admin/settings

    // 👇 لإخفاء الصفحة من القائمة الجانبية (Sidebar) لأننا وضعناها في User Menu
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(GeneralSettings $settings): void
    {
        // هنا يتم جلب البيانات المحفوظة مسبقاً لعرضها في الفورم
        // سأقوم بوضع بيانات وهمية الآن، وعليك استبدالها بجلب البيانات من الداتابيس
        $this->form->fill([
            'site_name' => $settings->site_name,
            'support_email' => $settings->support_email,
            'maintenance_mode' => $settings->maintenance_mode,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('الإعدادات العامة')
                    ->description('تحكم في البيانات الأساسية للتطبيق')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('اسم التطبيق')
                            ->required(),

                        TextInput::make('support_email')
                            ->label('بريد الدعم الفني')
                            ->email()
                            ->required(),

                        Toggle::make('maintenance_mode')
                            ->label('وضع الصيانة')
                            ->helperText('تفعيل هذا الخيار سيمنع المستخدمين من دخول التطبيق.'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(GeneralSettings $settings): void
    {
        $data = $this->form->getState();

        $settings->site_name = $data['site_name'];
        $settings->support_email = $data['support_email'];
        $settings->maintenance_mode = $data['maintenance_mode'];
        $settings->save();

        Notification::make()
            ->title('تم حفظ الإعدادات')
            ->success()
            ->send();
    }
}

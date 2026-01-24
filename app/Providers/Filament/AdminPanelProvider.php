<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Pages\Settings;
use App\Filament\Widgets\BookingsChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\Restaurant;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            // 👇 1. تفعيل أيقونة الإشعارات (الجرس) بجانب البروفايل
            ->databaseNotifications()

            // 👇 2. تخصيص قائمة المستخدم (User Menu)
            ->userMenuItems([
                // إضافة زر "الإعدادات"
                Action::make('settings')
                    ->label('الإعدادات')
                    ->url(fn () => Settings::getUrl(tenant: Filament::getTenant())) // ضع رابط صفحة الإعدادات الخاصة بك هنا
                    ->icon('heroicon-o-cog-6-tooth')
                    ->sort(1),

                // يمكنك أيضاً تخصيص زر "الملف الشخصي" إذا أردت
                Action::make('profile')
                    ->label('ملفي الشخصي')
                    ->url(fn (): string => EditProfile::getUrl())
                    ->icon('heroicon-o-user')
                    ->sort(2),
            ])
            // --- إعدادات الـ Multi-Tenancy ---
            ->tenant(Restaurant::class, slugAttribute: 'slug')
            // ---------------------------------
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                BookingsChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

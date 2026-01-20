<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $tenantId = \Filament\Facades\Filament::getTenant()?->id;

        // مصفوفة الإحصائيات الأساسية (تظهر للجميع حسب المطعم الحالي)
        $stats = [
            Stat::make('حجوزات المطعم', \App\Models\Booking::where('restaurant_id', $tenantId)->count())
                ->description('إجمالي الحجوزات هنا')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            Stat::make('إجمالي العملاء', \App\Models\Customer::where('restaurant_id', $tenantId)->count())
                ->description('قاعدة بيانات العملاء')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('موظفي الفرع', \App\Models\User::whereHas('restaurants', fn ($q) => $q->where('restaurants.id', $tenantId))->count())
                ->description('فريق العمل الحالي')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];

        // 👇 إضافة إحصائية "عدد الفروع" فقط إذا كان المستخدم مالك (Owner)
        if ($user->hasRole('Owner')) {
            // نضعها في بداية المصفوفة (أو نهايتها حسب رغبتك)
            array_unshift($stats,
                Stat::make('إجمالي الفروع', \App\Models\Restaurant::count())
                    ->description('عدد المطاعم التي تديرها')
                    ->descriptionIcon('heroicon-m-building-office-2')
                    ->color('info')
            );
        }
        if ($user->hasRole('Owner')) {
            $stats[] = Stat::make('إجمالي حجوزات النظام', \App\Models\Booking::count())
                ->description('عبر جميع مطاعمك')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary');
        }

        return $stats;
    }
}

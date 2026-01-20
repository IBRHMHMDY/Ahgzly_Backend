<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class BookingsChart extends ChartWidget
{
    protected ?string $heading = 'Bookings Chart';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()?->id;

        if (! $tenantId) {
            return ['datasets' => [], 'labels' => []];
        }

        $bookingsData = Booking::where('restaurant_id', $tenantId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                \Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'),
                \Illuminate\Support\Facades\DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الحجوزات',
                    'data' => array_values($bookingsData),
                ],
            ],
            'labels' => array_keys($bookingsData),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

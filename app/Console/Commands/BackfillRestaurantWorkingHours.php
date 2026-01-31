<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\RestaurantWorkingHour;
use Illuminate\Console\Command;

class BackfillRestaurantWorkingHours extends Command
{
    protected $signature = 'restaurants:backfill-working-hours {--force : overwrite existing}';

    protected $description = 'Create default working hours for restaurants that do not have any.';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $restaurants = Restaurant::query()->get(['id']);
        $updated = 0;

        foreach ($restaurants as $restaurant) {
            $hasHours = RestaurantWorkingHour::query()
                ->where('restaurant_id', $restaurant->id)
                ->exists();

            if ($hasHours && ! $force) {
                continue;
            }

            if ($force) {
                RestaurantWorkingHour::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->delete();
            }

            for ($day = 0; $day <= 6; $day++) {
                RestaurantWorkingHour::create([
                    'restaurant_id' => $restaurant->id,
                    'day_of_week' => $day,
                    'is_closed' => false,
                    'opens_at' => '12:00:00',
                    'closes_at' => '23:00:00',
                ]);
            }

            $updated++;
        }

        $this->info("Done. Updated restaurants: {$updated}");

        return self::SUCCESS;
    }
}

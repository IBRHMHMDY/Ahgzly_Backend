<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevRestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'Owner'))->first();

        if (! $owner) {
            return;
        }

        $restaurant = Restaurant::query()->firstOrCreate(
            ['slug' => 'demo-restaurant'],
            [
                'owner_id' => $owner->id,
                'name' => 'Demo Restaurant',
                'phone' => null,
                'address' => null,
                'is_active' => true,
            ]
        );

        // ربط الـ Owner في pivot (علشان default tenant)
        $owner->restaurants()->syncWithoutDetaching([
            $restaurant->id => ['is_default' => true, 'is_active' => true],
        ]);
    }
}

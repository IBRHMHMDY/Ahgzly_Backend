<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevUsersSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'owner@admin.com'],
            [
                'name' => 'Demo Owner',
                'password' => bcrypt('owner@0000'), // سيتم hashing تلقائيًا بسبب casts في User
                'phone' => '01002545658',
                'role' => UserRole::OWNER,

            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@admin.com'],
            [
                'name' => 'Demo Manager',
                'password' => bcrypt('manager@0000'), // سيتم hashing تلقائيًا بسبب casts في User
                'phone' => '01002598758',
                'role' => UserRole::MANAGER,

            ]
        );

        $Staff = User::firstOrCreate(
            ['email' => 'staff@admin.com'],
            [
                'name' => 'Demo Staff',
                'password' => bcrypt('staff@0000'), // سيتم hashing تلقائيًا بسبب casts في User
                'phone' => '01002598758',
                'role' => UserRole::STAFF,

            ]
        );

        if (! $owner->hasRole('Owner')) {
            $owner->syncRoles(['Owner']);
        }
        if (! $manager->hasRole('manager')) {
            $manager->syncRoles(['manager']);
        }
        if (! $staff->hasRole('staff')) {
            $staff->syncRoles(['staff']);
        }
    }
}

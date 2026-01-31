<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantWorkingHour;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles: SysAdmin,Owner,Manager,Staff
        $roleSysadmin = Role::firstOrCreate(['name' => 'SysAdmin', 'guard_name' => 'web']);
        $roleOwner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $roleStaff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // Create Users per Role
        $sysAdmin = User::firstOrCreate(
            ['email' => 'sys@admin.com'],
            [
                'name' => 'Mr.SysAdmin',
                'phone' => '01000000000',
                'password' => Hash::make('00000000'), // كلمة المرور
            ]
        );
        // $ownerUser = User::firstOrCreate(
        //     ['email' => 'owner@admin.com'],
        //     [
        //         'name' => 'Mr. Owner',
        //         'phone' => '01000000001',
        //         'password' => Hash::make('00000000'), // كلمة المرور
        //     ]
        // );
        // $managerUser = User::firstOrCreate(
        //     ['email' => 'manager@admin.com'],
        //     [
        //         'name' => 'Mr. Manager',
        //         'phone' => '01000000002',
        //         'password' => Hash::make('00000000'), // كلمة المرور
        //     ]
        // );
        // $staffUser = User::firstOrCreate(
        //     ['email' => 'staff@admin.com'],
        //     [
        //         'name' => 'Mr. Staff',
        //         'phone' => '01000000003',
        //         'password' => Hash::make('00000000'), // كلمة المرور
        //     ]
        // );

        // Assign Role per User
        $sysAdmin->assignRole($roleSysadmin);
        // $ownerUser->assignRole($roleOwner);
        // $managerUser->assignRole($roleManager);
        // $staffUser->assignRole($roleStaff);

        // // 3. Create Restaurant
        // $restaurant = Restaurant::firstOrCreate(
        //     ['slug' => 'Restaurant1-main'],
        //     [
        //         'name' => 'Restaurant1',
        //         'owner_id' => $ownerUser->id, // نربطه بالمالك
        //         'is_active' => true,
        //         'address' => 'Cairo, Egypt',
        //         'phone' => '01234567897',
        //     ]
        // );

        // // 4. Link Restaurant to User by Pivot Table (Restaurant_user)
        // $ownerUser->restaurants()->syncWithoutDetaching([
        //     $restaurant->id => [
        //         'is_active' => true,
        //         'is_default' => true,
        //     ],
        // ]);
        // $managerUser->restaurants()->syncWithoutDetaching([
        //     $restaurant->id => [
        //         'is_active' => true,
        //         'is_default' => false,
        //     ],
        // ]);
        // $staffUser->restaurants()->syncWithoutDetaching([
        //     $restaurant->id => [
        //         'is_active' => true,
        //         'is_default' => false,
        //     ],
        // ]);

        Restaurant::query()->each(function (Restaurant $restaurant) {
            for ($day = 0; $day <= 6; $day++) {
                RestaurantWorkingHour::updateOrCreate(
                    ['restaurant_id' => $restaurant->id, 'day_of_week' => $day],
                    [
                        'is_closed' => false,
                        'opens_at' => '12:00:00',
                        'closes_at' => '23:00:00',
                    ]
                );
            }
        });

        $this->command->info('Users: sys@admin.com');
        $this->command->info('Password: 00000000');
        $this->command->info('✅ Setup Done!');
        // $this->command->info('Restaurant: '.$restaurant->name);
    }
}

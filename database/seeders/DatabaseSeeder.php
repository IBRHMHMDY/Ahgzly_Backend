<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles: Owner,Manager,Staff
        $roleOwner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $roleStaff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // Create Users per Role
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@admin.com'],
            [
                'name' => 'Mr. Owner',
                'phone' => '01000000001',
                'password' => Hash::make('00000000'), // كلمة المرور
            ]
        );
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@admin.com'],
            [
                'name' => 'Mr. Manager',
                'phone' => '01000000002',
                'password' => Hash::make('00000000'), // كلمة المرور
            ]
        );
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@admin.com'],
            [
                'name' => 'Mr. Staff',
                'phone' => '01000000003',
                'password' => Hash::make('00000000'), // كلمة المرور
            ]
        );

        // Assign Role per User
        $ownerUser->assignRole($roleOwner);
        $managerUser->assignRole($roleManager);
        $staffUser->assignRole($roleStaff);

        // 3. Create Restaurant
        $restaurant = Restaurant::firstOrCreate(
            ['slug' => 'Restaurant1-main'],
            [
                'name' => 'Restaurant1',
                'owner_id' => $ownerUser->id, // نربطه بالمالك
                'is_active' => true,
                'address' => 'Cairo, Egypt',
                'phone' => '01234567897',
            ]
        );

        // 4. Link Restaurant to User by Pivot Table (Restaurant_user)
        $ownerUser->restaurants()->syncWithoutDetaching([
            $restaurant->id => [
                'is_active' => true,
                'is_default' => true,
            ],
        ]);
        $managerUser->restaurants()->syncWithoutDetaching([
            $restaurant->id => [
                'is_active' => true,
                'is_default' => false,
            ],
        ]);
        $staffUser->restaurants()->syncWithoutDetaching([
            $restaurant->id => [
                'is_active' => true,
                'is_default' => false,
            ],
        ]);

        $this->command->info('✅ Setup Done!');
        $this->command->info('Users: \\n --owner@admin.com : Owner \\n --manager@admin.com : Manager \\n --staff@admin.com : Staff');
        $this->command->info('Password: 00000000');
        $this->command->info('Restaurant: '.$restaurant->name);
    }
}

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
        // 1. إنشاء الأدوار (Roles)
        // نستخدم firstOrCreate لتجنب الأخطاء إذا كانت موجودة مسبقاً
        $roleOwner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $roleStaff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // 2. إنشاء مستخدم المالك (Owner User)
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@admin.com'],
            [
                'name' => 'Mr. Owner',
                'phone' => '01000000001',
                'password' => Hash::make('00000000'), // كلمة المرور
            ]
        );

        // تعيين دور Owner للمستخدم
        $ownerUser->assignRole($roleOwner);

        // 3. إنشاء مطعم تجريبي
        $restaurant = Restaurant::firstOrCreate(
            ['slug' => 'ahgzly-main'],
            [
                'name' => 'مطعم احجزلي الرئيسي',
                'owner_id' => $ownerUser->id, // نربطه بالمالك
                'is_active' => true,
                'address' => 'Cairo, Egypt',
                'phone' => '0123456789',
            ]
        );

        // 4. ربط المستخدم بالمطعم في جدول الـ Pivot
        // هذا ضروري لأن دالة getTenants تعتمد على الجدول الوسيط أيضاً للوصول السريع
        // وأيضاً لتحديد أنه المطعم الافتراضي
        $ownerUser->restaurants()->syncWithoutDetaching([
            $restaurant->id => [
                'is_active' => true,
                'is_default' => true,
            ],
        ]);

        $this->command->info('✅ Setup Done!');
        $this->command->info('User: owner@ahgzly.com');
        $this->command->info('Password: password');
        $this->command->info('Restaurant: '.$restaurant->name);
    }
}

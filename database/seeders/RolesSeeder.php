<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Owner', 'Manager', 'Staff', 'Customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}

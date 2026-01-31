<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSysAdmin extends Command
{
    protected $signature = 'app:create-sysadmin {email} {--name=SysAdmin} {--password=}';

    protected $description = 'Create or promote a SysAdmin user';

    public function handle(): int
    {
        $email = $this->argument('email');
        $name = $this->option('name');
        $password = $this->option('password') ?: 'ChangeMe@12345';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        // لو المستخدم موجود لكن بدون باسورد مناسب (نادر)، يمكنك تحديثه يدوياً
        if (! $user->hasRole('SysAdmin')) {
            $user->assignRole('SysAdmin');
        }

        $this->info("SysAdmin ready: {$user->email}");
        $this->warn("Password (if newly created): {$password}");
        $this->warn('Please change password after first login.');

        return self::SUCCESS;
    }
}

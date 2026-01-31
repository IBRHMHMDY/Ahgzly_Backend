<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // ✅ لو داخل sysadmin panel
        if ($request->is('sysadmin') || $request->is('sysadmin/*')) {
            return route('filament.sysadmin.auth.login');
        }

        // ✅ الافتراضي: admin panel
        return route('filament.admin.auth.login');
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;

class RedirectSysAdminToSetup
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasRole('SysAdmin')) {

            // ✅ أول تشغيل: لا يوجد مطاعم
            if (Restaurant::query()->count() === 0) {

                // تجنب loop: لو هو بالفعل داخل sysadmin panel لا تعيد توجيه
                if (! $request->is('sysadmin*')) {
                    return redirect('/sysadmin/create-owner-with-restaurant');
                }
            }
        }

        return $next($request);
    }
}

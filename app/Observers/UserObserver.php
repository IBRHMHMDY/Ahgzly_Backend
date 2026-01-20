<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function saved(User $user): void
    {
        if (! $user->role) {
            return;
        }

        // role هنا Enum بسبب cast
        $roleName = $user->role->value;

        // اجعل user عنده Role واحدة فقط
        if (! $user->hasRole($roleName)) {
            $user->syncRoles([$roleName]);
        }
    }
}

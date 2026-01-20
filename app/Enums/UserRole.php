<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}

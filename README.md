# Ahgzly MVP - Booking System (Laravel 12 API + Filament)

## Stack
- Laravel 12 (API)
- Sanctum Authentication
- Filament v4 (Dashboard)
- Spatie Roles & Permissions
- Single Database, Simple Multi-Tenancy via restaurant_id

## Roles
- Owner (Filament)
- Manager (Filament)
- Staff (Filament)
- Customer (API only)

## Rules
- Owner can create multiple restaurants
- Owner assigns Managers (only)
- Owner or Manager assigns Staff
- Staff manages bookings & customers only
- Customer uses API only

## Local Setup
1) Clone repo
2) composer install
3) copy .env and set DB
4) php artisan key:generate
5) php artisan migrate --seed
6) php artisan serve

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert customers.phone from INT to VARCHAR(20) without requiring doctrine/dbal
        // and drop customers.password (unused in the mobile API).
        if (Schema::hasTable('customers')) {
            try {
                DB::statement("ALTER TABLE customers MODIFY phone VARCHAR(20) NULL");
            } catch (\Throwable $e) {
                // Ignore if already modified
            }

            try {
                DB::statement("ALTER TABLE customers DROP COLUMN password");
            } catch (\Throwable $e) {
                // Ignore if already dropped
            }
        }
    }

    public function down(): void
    {
        // Down migrations are intentionally no-op to avoid data loss.
    }
};

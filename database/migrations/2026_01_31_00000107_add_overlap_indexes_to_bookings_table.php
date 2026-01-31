<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Optimize overlap checks and listing per restaurant/date
            $table->index(['restaurant_id', 'booking_date', 'start_at'], 'bookings_rest_date_start_idx');
            $table->index(['restaurant_id', 'booking_date', 'end_at'], 'bookings_rest_date_end_idx');
            $table->index(['restaurant_id', 'booking_date', 'status'], 'bookings_rest_date_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_rest_date_start_idx');
            $table->dropIndex('bookings_rest_date_end_idx');
            $table->dropIndex('bookings_rest_date_status_idx');
        });
    }
};

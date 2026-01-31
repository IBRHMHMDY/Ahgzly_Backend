<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Slot settings (MVP Phase 2)
            $table->unsignedSmallInteger('slot_duration_minutes')
                ->default(90)
                ->after('is_active');

            // If set: total guests in the same overlapping time window cannot exceed this value.
            $table->unsignedSmallInteger('max_guests_per_slot')
                ->nullable()
                ->after('slot_duration_minutes');

            // If set: total bookings in the same overlapping time window cannot exceed this value.
            $table->unsignedSmallInteger('max_bookings_per_slot')
                ->nullable()
                ->after('max_guests_per_slot');

            $table->index(['is_active', 'slot_duration_minutes']);
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'slot_duration_minutes']);
            $table->dropColumn(['slot_duration_minutes', 'max_guests_per_slot', 'max_bookings_per_slot']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            // لو العميل عنده حساب API (اختياري للـ MVP)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->integer('phone')->nullable();
            $table->string('password')->default('000000');
            $table->timestamps();

            $table->index(['restaurant_id', 'phone']);
            $table->index(['restaurant_id', 'email']);
            $table->unique(['restaurant_id', 'phone']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

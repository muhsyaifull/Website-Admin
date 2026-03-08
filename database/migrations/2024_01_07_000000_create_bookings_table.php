<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('user_id')->constrained('users');

            // Data perwakilan
            $table->string('representative_name', 100);
            $table->text('representative_address');
            $table->string('representative_phone', 20);

            // Jumlah peserta
            $table->integer('adult_count');
            $table->integer('child_count')->default(0);
            $table->integer('total_participants');

            // Tour sessions
            $table->foreignId('taman_session_id')->nullable()->constrained('tour_sessions');
            $table->foreignId('museum_session_id')->nullable()->constrained('tour_sessions');

            // Payment calculation
            $table->decimal('unit_price', 10, 0);
            $table->decimal('total_price', 10, 0);

            $table->date('visit_date');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
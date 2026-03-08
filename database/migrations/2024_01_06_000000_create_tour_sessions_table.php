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
        Schema::create('tour_sessions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['taman', 'museum']);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('label', 50);
            $table->integer('capacity')->default(20);
            $table->integer('booked')->default(0);
            $table->foreignId('educator_id')->constrained('educators');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_sessions');
    }
};
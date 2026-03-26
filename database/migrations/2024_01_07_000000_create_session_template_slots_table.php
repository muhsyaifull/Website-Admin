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
        Schema::create('session_template_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_template_id')->constrained('session_templates')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->default(20);
            $table->foreignId('educator_id')->nullable()->constrained('educators')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_template_slots');
    }
};
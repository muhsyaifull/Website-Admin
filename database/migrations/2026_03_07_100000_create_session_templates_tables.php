<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('session_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g. "Default Weekday", "Weekend", "Holiday"
            $table->enum('type', ['taman', 'museum']);
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('apply_days')->nullable(); // [0,6] = Sun,Sat. null = manual only
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

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

        // Add template reference to existing tour_sessions
        Schema::table('tour_sessions', function (Blueprint $table) {
            $table->foreignId('session_template_id')->nullable()->after('sort_order')
                ->constrained('session_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tour_sessions', function (Blueprint $table) {
            $table->dropForeign(['session_template_id']);
            $table->dropColumn('session_template_id');
        });
        Schema::dropIfExists('session_template_slots');
        Schema::dropIfExists('session_templates');
    }
};

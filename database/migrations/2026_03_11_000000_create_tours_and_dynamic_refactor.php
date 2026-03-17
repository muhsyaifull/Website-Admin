<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create tours table
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Pivot: packages <-> tours
        Schema::create('package_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Pivot: educators <-> tours (replaces specialization)
        Schema::create('educator_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. Pivot: bookings <-> tour_sessions (replaces taman_session_id / museum_session_id)
        Schema::create('booking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. Add tour_id to session_templates
        Schema::table('session_templates', function (Blueprint $table) {
            $table->foreignId('tour_id')->nullable()->after('type')
                ->constrained()->nullOnDelete();
        });

        // 6. Add tour_id to tour_sessions
        Schema::table('tour_sessions', function (Blueprint $table) {
            $table->foreignId('tour_id')->nullable()->after('type')
                ->constrained()->nullOnDelete();
        });

        // 7. Seed initial tours from existing data
        $tamanId = DB::table('tours')->insertGetId([
            'name' => 'Taman Atsiri',
            'slug' => 'taman',
            'description' => 'Tour Taman Atsiri - Aromatic Garden Tour',
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $museumId = DB::table('tours')->insertGetId([
            'name' => 'Museum Atsiri',
            'slug' => 'museum',
            'description' => 'Tour Museum Atsiri - Essential Oil Museum Tour',
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Migrate existing type data to tour_id
        DB::table('session_templates')->where('type', 'taman')->update(['tour_id' => $tamanId]);
        DB::table('session_templates')->where('type', 'museum')->update(['tour_id' => $museumId]);
        DB::table('tour_sessions')->where('type', 'taman')->update(['tour_id' => $tamanId]);
        DB::table('tour_sessions')->where('type', 'museum')->update(['tour_id' => $museumId]);

        // 9. Migrate educator specializations to pivot
        $educators = DB::table('educators')->get();
        foreach ($educators as $educator) {
            if (in_array($educator->specialization, ['taman', 'both'])) {
                DB::table('educator_tours')->insert([
                    'educator_id' => $educator->id,
                    'tour_id' => $tamanId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if (in_array($educator->specialization, ['museum', 'both'])) {
                DB::table('educator_tours')->insert([
                    'educator_id' => $educator->id,
                    'tour_id' => $museumId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 10. Migrate booking sessions to pivot
        $bookings = DB::table('bookings')->get();
        foreach ($bookings as $booking) {
            if ($booking->taman_session_id) {
                DB::table('booking_sessions')->insert([
                    'booking_id' => $booking->id,
                    'tour_session_id' => $booking->taman_session_id,
                    'tour_id' => $tamanId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($booking->museum_session_id) {
                DB::table('booking_sessions')->insert([
                    'booking_id' => $booking->id,
                    'tour_session_id' => $booking->museum_session_id,
                    'tour_id' => $museumId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 11. Migrate package tours - all existing packages get both tours
        $packages = DB::table('packages')->get();
        foreach ($packages as $package) {
            DB::table('package_tours')->insert([
                ['package_id' => $package->id, 'tour_id' => $tamanId, 'created_at' => now(), 'updated_at' => now()],
                ['package_id' => $package->id, 'tour_id' => $museumId, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('tour_sessions', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });

        Schema::table('session_templates', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });

        Schema::dropIfExists('booking_sessions');
        Schema::dropIfExists('educator_tours');
        Schema::dropIfExists('package_tours');
        Schema::dropIfExists('tours');
    }
};

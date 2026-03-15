<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $hasTamanSession = Schema::hasColumn('bookings', 'taman_session_id');
        $hasMuseumSession = Schema::hasColumn('bookings', 'museum_session_id');

        if ($hasTamanSession || $hasMuseumSession) {
            Schema::table('bookings', function (Blueprint $table) use ($hasTamanSession, $hasMuseumSession) {
                if ($hasTamanSession) {
                    $table->dropConstrainedForeignId('taman_session_id');
                }

                if ($hasMuseumSession) {
                    $table->dropConstrainedForeignId('museum_session_id');
                }
            });
        }

        if (Schema::hasColumn('tour_sessions', 'type')) {
            Schema::table('tour_sessions', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('session_templates', 'type')) {
            Schema::table('session_templates', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('bookings', 'taman_session_id') || !Schema::hasColumn('bookings', 'museum_session_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'taman_session_id')) {
                    $table->foreignId('taman_session_id')->nullable()->after('total_participants')->constrained('tour_sessions');
                }

                if (!Schema::hasColumn('bookings', 'museum_session_id')) {
                    $table->foreignId('museum_session_id')->nullable()->after('taman_session_id')->constrained('tour_sessions');
                }
            });
        }

        if (!Schema::hasColumn('tour_sessions', 'type')) {
            Schema::table('tour_sessions', function (Blueprint $table) {
                $table->string('type', 50)->after('id');
            });
        }

        if (!Schema::hasColumn('session_templates', 'type')) {
            Schema::table('session_templates', function (Blueprint $table) {
                $table->string('type', 50)->after('name');
            });
        }
    }
};

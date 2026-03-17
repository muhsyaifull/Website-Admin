<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $columnsToDrop = array_filter([
            Schema::hasColumn('tours', 'icon') ? 'icon' : null,
            Schema::hasColumn('tours', 'color') ? 'color' : null,
        ]);

        if ($columnsToDrop === []) {
            return;
        }

        Schema::table('tours', function (Blueprint $table) use ($columnsToDrop) {
            $table->dropColumn($columnsToDrop);
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'icon')) {
                $table->string('icon', 50)->default('fas fa-map-marked-alt')->after('description');
            }

            if (!Schema::hasColumn('tours', 'color')) {
                $table->string('color', 7)->default('#007bff')->after('icon');
            }
        });
    }
};
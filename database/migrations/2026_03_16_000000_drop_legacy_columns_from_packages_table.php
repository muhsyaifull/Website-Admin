<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $columnsToDrop = array_values(array_filter([
            Schema::hasColumn('packages', 'color') ? 'color' : null,
            Schema::hasColumn('packages', 'bg_color') ? 'bg_color' : null,
            Schema::hasColumn('packages', 'has_saldo') ? 'has_saldo' : null,
            Schema::hasColumn('packages', 'saldo_amount') ? 'saldo_amount' : null,
            Schema::hasColumn('packages', 'has_resto') ? 'has_resto' : null,
        ]));

        if ($columnsToDrop !== []) {
            Schema::table('packages', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'color')) {
                $table->string('color', 7)->default('#7B3F2A')->after('includes');
            }

            if (!Schema::hasColumn('packages', 'bg_color')) {
                $table->string('bg_color', 7)->default('#F5EDE8')->after('color');
            }

            if (!Schema::hasColumn('packages', 'has_saldo')) {
                $table->boolean('has_saldo')->default(false)->after('bg_color');
            }

            if (!Schema::hasColumn('packages', 'saldo_amount')) {
                $table->decimal('saldo_amount', 10, 0)->nullable()->after('has_saldo');
            }

            if (!Schema::hasColumn('packages', 'has_resto')) {
                $table->boolean('has_resto')->default(false)->after('saldo_amount');
            }
        });
    }
};

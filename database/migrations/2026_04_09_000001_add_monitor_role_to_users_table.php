<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cashier', 'educator', 'admin', 'monitor') NOT NULL DEFAULT 'cashier'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'monitor')->update(['role' => 'cashier']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cashier', 'educator', 'admin') NOT NULL DEFAULT 'cashier'");
    }
};

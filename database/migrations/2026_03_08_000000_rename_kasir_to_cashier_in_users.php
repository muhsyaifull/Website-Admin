<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // First add 'cashier' to the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('kasir', 'cashier', 'educator', 'admin') DEFAULT 'cashier'");

        // Update existing data
        DB::table('users')->where('role', 'kasir')->update(['role' => 'cashier']);

        // Remove 'kasir' from the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cashier', 'educator', 'admin') DEFAULT 'cashier'");
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'cashier')->update(['role' => 'kasir']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('kasir', 'educator', 'admin') DEFAULT 'kasir'");
    }
};

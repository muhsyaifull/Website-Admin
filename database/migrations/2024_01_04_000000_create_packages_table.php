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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('label', 50);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 0);
            $table->json('includes');
            $table->string('color', 7)->default('#7B3F2A');
            $table->string('bg_color', 7)->default('#F5EDE8');
            $table->boolean('has_saldo')->default(false);
            $table->decimal('saldo_amount', 10, 0)->nullable();
            $table->boolean('has_resto')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
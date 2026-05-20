<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('tourist')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('currency_preference', 3)->default('USD')->after('phone');
            $table->foreignId('current_island_id')->nullable()->after('currency_preference')->constrained('islands')->nullOnDelete();
            $table->date('trip_start')->nullable()->after('current_island_id');
            $table->date('trip_end')->nullable()->after('trip_start');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_island_id']);
            $table->dropColumn(['role', 'phone', 'currency_preference', 'current_island_id', 'trip_start', 'trip_end']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('item_count')->default(0);
            $table->decimal('amount_mvr', 12, 2);
            $table->decimal('amount_usd', 12, 2)->nullable();
            $table->string('currency', 3)->default('MVR');
            $table->string('status')->default('pending');
            $table->text('special_requests')->nullable();
            $table->dateTime('scheduled_for')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('order_bundle_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->change();
            $table->foreignId('order_bundle_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_bundle_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_bundle_id');
        });

        Schema::dropIfExists('order_bundles');
    }
};

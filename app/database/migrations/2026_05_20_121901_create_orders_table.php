<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_mvr', 10, 2);
            $table->decimal('amount_usd', 10, 2)->nullable();
            $table->string('currency', 3)->default('MVR');
            $table->string('status')->default('pending');
            $table->string('voucher_code')->unique();
            $table->dateTime('scheduled_for')->nullable();
            $table->dateTime('fulfilled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->text('special_requests')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['provider_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

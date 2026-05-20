<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('swipe_transaction_id')->nullable();
            $table->string('swipe_reference')->nullable();
            $table->string('swipe_short_code')->nullable();
            $table->string('payment_type')->default('QR');
            $table->decimal('amount_mvr', 10, 2);
            $table->string('currency', 3)->default('MVR');
            $table->string('status')->default('pending');
            $table->string('escrow_state')->default('holding');
            $table->dateTime('charged_at')->nullable();
            $table->dateTime('released_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('provider_net', 10, 2)->default(0);
            $table->decimal('tgst_amount', 10, 2)->default(0);
            $table->json('swipe_payload')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('escrow_state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

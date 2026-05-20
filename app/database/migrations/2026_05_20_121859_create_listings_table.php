<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('island_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('category');
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('price_mvr', 10, 2);
            $table->decimal('price_usd', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('lead_time_minutes')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('order_count')->default(0);
            $table->timestamps();

            $table->index(['island_id', 'category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};

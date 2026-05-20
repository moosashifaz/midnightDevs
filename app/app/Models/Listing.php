<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'eat' => 'Taste',
        'wash' => 'Refresh',
        'buy' => 'Shop',
        'experience' => 'Explore',
    ];

    public const CATEGORY_SUBLABELS = [
        'eat' => 'Local food & cafés',
        'wash' => 'Wellness & essentials',
        'buy' => 'Crafts & souvenirs',
        'experience' => 'Experiences & island life',
    ];

    public const TYPES = [
        'instant' => 'Instant Purchase',
        'scheduled' => 'Scheduled Service',
        'experience' => 'Experience',
    ];

    protected $fillable = [
        'provider_id', 'island_id', 'title', 'slug', 'category', 'type',
        'description', 'price_mvr', 'price_usd', 'image_url', 'tags',
        'is_active', 'lead_time_minutes', 'rating', 'review_count', 'order_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
        'price_mvr' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function categorySublabel(): string
    {
        return self::CATEGORY_SUBLABELS[$this->category] ?? '';
    }
}

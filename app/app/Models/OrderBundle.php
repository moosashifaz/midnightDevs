<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderBundle extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'reference',
        'user_id',
        'item_count',
        'amount_mvr',
        'amount_usd',
        'currency',
        'status',
        'special_requests',
        'scheduled_for',
    ];

    protected function casts(): array
    {
        return [
            'amount_mvr' => 'decimal:2',
            'amount_usd' => 'decimal:2',
            'scheduled_for' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $bundle) {
            if (! $bundle->reference) {
                $bundle->reference = 'AA-BND-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }
}

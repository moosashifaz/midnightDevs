<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FULFILLED = 'fulfilled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'reference', 'user_id', 'order_bundle_id', 'listing_id', 'provider_id', 'amount_mvr',
        'amount_usd', 'currency', 'status', 'voucher_code', 'scheduled_for',
        'fulfilled_at', 'completed_at', 'cancelled_at', 'cancellation_reason',
        'special_requests',
    ];

    protected $casts = [
        'amount_mvr' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'scheduled_for' => 'datetime',
        'fulfilled_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (! $order->reference) {
                $order->reference = 'AA-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            }
            if (! $order->voucher_code) {
                $order->voucher_code = 'V'.strtoupper(substr(bin2hex(random_bytes(6)), 0, 12));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(OrderBundle::class, 'order_bundle_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function isFulfillable(): bool
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_FULFILLED]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}

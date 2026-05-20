<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_FAILED = 'failed';

    public const ESCROW_HOLDING = 'holding';
    public const ESCROW_RELEASED = 'released';
    public const ESCROW_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id', 'swipe_transaction_id', 'swipe_reference', 'swipe_short_code',
        'payment_type', 'amount_mvr', 'currency', 'status', 'escrow_state',
        'charged_at', 'released_at', 'refunded_at',
        'platform_commission', 'provider_net', 'tgst_amount', 'swipe_payload',
    ];

    protected $casts = [
        'amount_mvr' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'provider_net' => 'decimal:2',
        'tgst_amount' => 'decimal:2',
        'charged_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'swipe_payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

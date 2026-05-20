<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedPlan extends Model
{
    protected $fillable = [
        'user_id',
        'island_id',
        'budget_usd',
        'days',
        'spent_usd',
        'summary',
        'plan_data',
    ];

    protected function casts(): array
    {
        return [
            'budget_usd' => 'decimal:2',
            'spent_usd' => 'decimal:2',
            'plan_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function titleLine(): string
    {
        $island = $this->island?->name ?? 'Island';
        $budget = number_format((float) $this->budget_usd, 0);

        return "{$this->days} days · \${$budget} · {$island}";
    }
}

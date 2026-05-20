<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['identifier', 'type', 'code', 'expires_at', 'used_at'])]
class OtpCode extends Model
{
    use HasFactory;
    
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
    
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
    
    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
    
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }
}

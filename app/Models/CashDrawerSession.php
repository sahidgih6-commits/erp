<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashDrawerSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'opening_balance',
        'closing_balance',
        'expected_balance',
        'difference',
        'total_sales',
        'total_cash',
        'total_card',
        'total_mobile',
        'transaction_count',
        'opened_at',
        'closed_at',
        'notes',
        'closing_notes',
        'is_open',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_card' => 'decimal:2',
        'total_mobile' => 'decimal:2',
        'transaction_count' => 'integer',
        'is_open' => 'boolean',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen(): bool
    {
        return is_null($this->closed_at);
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('closed_at');
    }

    public function scopeClosed($query)
    {
        return $query->whereNotNull('closed_at');
    }
}

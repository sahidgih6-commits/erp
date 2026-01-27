<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email',
        'address',
        'credit_limit',
        'current_due',
        'total_purchase',
        'loyalty_points',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_due' => 'decimal:2',
        'total_purchase' => 'decimal:2',
        'loyalty_points' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['total_due'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalDueAttribute()
    {
        return $this->current_due;
    }

    public function hasCredit(): bool
    {
        return $this->credit_limit > 0;
    }

    public function canPurchase(float $amount): bool
    {
        if (!$this->hasCredit()) {
            return false;
        }
        
        return ($this->current_due + $amount) <= $this->credit_limit;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithDue($query)
    {
        return $query->where('current_due', '>', 0);
    }
}

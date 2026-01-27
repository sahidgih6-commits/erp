<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class POSTransaction extends Model
{
    use HasFactory;

    protected $table = 'pos_transactions';

    protected $fillable = [
        'business_id',
        'user_id',
        'transaction_number',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'amount_tendered',
        'change',
        'status',
        'receipt_printed',
        'drawer_opened',
        'items',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'items' => 'array',
        'completed_at' => 'datetime',
        'receipt_printed' => 'boolean',
        'drawer_opened' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_tendered' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    /**
     * Get the business that owns this transaction.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who created this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the receipt prints for this transaction.
     */
    public function receiptPrints(): HasMany
    {
        return $this->hasMany(ReceiptPrint::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->transaction_number) {
                $model->transaction_number = 'TRX-' . now()->format('YmdHis') . '-' . rand(100, 999);
            }
            if (!$model->completed_at) {
                $model->completed_at = now();
            }
        });
    }

    /**
     * Get transaction status label.
     */
    public function getStatusLabel(): string
    {
        $statuses = [
            'completed' => __('pos.transaction_saved'),
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Check if transaction can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' && now()->diffInHours($this->completed_at) <= 24;
    }

    /**
     * Get formatted transaction number.
     */
    public function getFormattedTransactionNumber(): string
    {
        return $this->transaction_number;
    }
}

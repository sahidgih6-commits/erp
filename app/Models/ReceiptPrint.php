<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptPrint extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'pos_transaction_id',
        'receipt_number',
        'paper_size',
        'printer_name',
        'status',
        'error_message',
        'retry_count',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    /**
     * Get the business that owns this receipt print.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the POS transaction associated with this receipt.
     */
    public function posTransaction(): BelongsTo
    {
        return $this->belongsTo(POSTransaction::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->receipt_number) {
                $model->receipt_number = 'RCP-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
            }
        });
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        $statuses = [
            'pending' => 'Pending',
            'printing' => 'Printing',
            'completed' => __('pos.print_successful'),
            'failed' => __('pos.print_failed'),
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Mark receipt as successfully printed.
     */
    public function markAsPrinted(): void
    {
        $this->update([
            'status' => 'completed',
            'printed_at' => now(),
        ]);
    }

    /**
     * Mark receipt print as failed and can be retried.
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Can this receipt be retried?
     */
    public function canRetry(): bool
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }
}

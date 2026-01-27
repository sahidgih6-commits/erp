<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'quantity',
        'sell_price',
        'total_amount',
        'payment_method',
        'paid_amount',
        'change_amount',
        'discount_amount',
        'discount_type',
        'due_amount',
        'note',
        'status',
        'expected_clear_date',
        'actual_clear_date',
        'payment_status',
        'profit',
        'voucher_number',
        'voucher_image',
    ];

    protected $casts = [
        'sell_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'profit' => 'decimal:2',
        'expected_clear_date' => 'date',
        'actual_clear_date' => 'date',
    ];

    protected $appends = ['total', 'due'];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function profitRealizations(): HasMany
    {
        return $this->hasMany(ProfitRealization::class);
    }

    // Get realized profit (নগদ লাভ)
    public function getRealizedProfitAttribute()
    {
        return $this->profitRealizations()->sum('profit_amount');
    }

    // Get pending profit (বাকি লাভ)
    public function getPendingProfitAttribute()
    {
        return $this->profit - $this->realized_profit;
    }

    // Accessor for backward compatibility
    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    public function getDueAttribute()
    {
        return $this->due_amount;
    }

    // Boot method to calculate totals
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $sale->total_amount = $sale->quantity * $sale->sell_price;
            $product = Product::find($sale->product_id);
            $sale->profit = ($sale->sell_price - $product->purchase_price) * $sale->quantity;
            $sale->due_amount = $sale->total_amount - ($sale->paid_amount ?? 0);
            
            // Set payment status
            if ($sale->due_amount == 0) {
                $sale->payment_status = 'paid';
            } elseif ($sale->paid_amount > 0) {
                $sale->payment_status = 'partial';
            } else {
                $sale->payment_status = 'unpaid';
            }
        });
        
        static::updating(function ($sale) {
            $sale->due_amount = $sale->total_amount - ($sale->paid_amount ?? 0);
            
            // Update payment status
            if ($sale->due_amount == 0) {
                $sale->payment_status = 'paid';
                if (!$sale->actual_clear_date) {
                    $sale->actual_clear_date = now();
                }
            } elseif ($sale->paid_amount > 0) {
                $sale->payment_status = 'partial';
            } else {
                $sale->payment_status = 'unpaid';
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'image',
        'purchase_price',
        'sell_price',
        'current_stock',
        'min_stock_level',
        'unit',
    ];

    protected $appends = ['stock', 'price'];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'min_stock_level' => 'decimal:2',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Helper methods
    public function addStock(int $quantity, float $purchasePrice, int $addedBy): void
    {
        $this->increment('current_stock', $quantity);
        $this->update(['purchase_price' => $purchasePrice]);

        $this->stockEntries()->create([
            'quantity' => $quantity,
            'purchase_price' => $purchasePrice,
            'added_by' => $addedBy,
        ]);
    }

    public function reduceStock(int $quantity): void
    {
        if ($this->current_stock < $quantity) {
            throw new \Exception('পর্যাপ্ত স্টক নেই। বর্তমান স্টক: ' . $this->current_stock);
        }
        $this->decrement('current_stock', $quantity);
    }

    public function getStockValue(): float
    {
        return $this->current_stock * $this->purchase_price;
    }

    // Accessor for compatibility
    public function getStockAttribute()
    {
        return $this->current_stock;
    }

    public function getPriceAttribute()
    {
        return $this->sell_price;
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock_level;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }
}

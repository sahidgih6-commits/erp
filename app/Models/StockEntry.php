<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'purchase_price',
        'added_by',
        'business_id',
        'supplier_name',
        'supplier_phone',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owners(): HasMany
    {
        return $this->hasMany(User::class, 'business_id')->role('owner');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'business_id');
    }

    public function voucherTemplate()
    {
        return $this->hasOne(VoucherTemplate::class, 'business_id');
    }

    public function systemVersion()
    {
        return $this->hasOne(SystemVersion::class);
    }

    public function hardwareDevices(): HasMany
    {
        return $this->hasMany(HardwareDevice::class);
    }

    public function hardwareAuditLogs(): HasMany
    {
        return $this->hasMany(HardwareAuditLog::class);
    }

    public function posTransactions(): HasMany
    {
        return $this->hasMany(POSTransaction::class);
    }

    public function receiptPrints(): HasMany
    {
        return $this->hasMany(ReceiptPrint::class);
    }
}

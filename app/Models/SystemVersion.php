<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'version',
        'pos_enabled',
        'barcode_scanner_enabled',
        'thermal_printer_enabled',
        'cash_drawer_enabled',
        'pos_activated_at',
        'upgraded_at',
        'upgrade_notes',
    ];

    protected $casts = [
        'pos_activated_at' => 'datetime',
        'upgraded_at' => 'datetime',
    ];

    /**
     * Get the business that owns this system version.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Check if POS system is enabled for this business.
     */
    public function isPOSEnabled(): bool
    {
        return $this->pos_enabled;
    }

    /**
     * Activate POS system for this business.
     */
    public function activatePOS(): void
    {
        $this->update([
            'pos_enabled' => true,
            'pos_activated_at' => now(),
        ]);
    }

    /**
     * Deactivate POS system for this business.
     */
    public function deactivatePOS(): void
    {
        $this->update([
            'pos_enabled' => false,
        ]);
    }

    /**
     * Check if barcode scanner feature is available.
     */
    public function canUseBarcodeScanner(): bool
    {
        if (!$this->pos_enabled || !$this->barcode_scanner_enabled) {
            return false;
        }

        return in_array($this->version, ['pro', 'enterprise']);
    }

    /**
     * Check if thermal printer feature is available.
     */
    public function canUseThermalPrinter(): bool
    {
        if (!$this->pos_enabled || !$this->thermal_printer_enabled) {
            return false;
        }

        return in_array($this->version, ['pro', 'enterprise']);
    }

    /**
     * Check if cash drawer feature is available.
     */
    public function canUseCashDrawer(): bool
    {
        if (!$this->pos_enabled || !$this->cash_drawer_enabled) {
            return false;
        }

        return in_array($this->version, ['pro', 'enterprise']);
    }

    /**
     * Check if all features are available for current version.
     */
    public function isBusiness(string $version): bool
    {
        return $this->version === $version;
    }
}

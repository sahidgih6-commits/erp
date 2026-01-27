<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HardwareDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'device_type',
        'device_name',
        'device_model',
        'device_serial_number',
        'connection_type',
        'port',
        'ip_address',
        'is_enabled',
        'is_connected',
        'last_connected_at',
        'last_disconnected_at',
        'configuration',
        'notes',
    ];

    protected $casts = [
        'configuration' => 'array',
        'last_connected_at' => 'datetime',
        'last_disconnected_at' => 'datetime',
    ];

    /**
     * Get the business that owns this hardware device.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the audit logs for this device.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(HardwareAuditLog::class);
    }

    /**
     * Check if device is ready for use.
     */
    public function isReady(): bool
    {
        return $this->is_enabled && $this->is_connected;
    }

    /**
     * Update device connection status.
     */
    public function markAsConnected(): void
    {
        $this->update([
            'is_connected' => true,
            'last_connected_at' => now(),
        ]);
    }

    /**
     * Update device disconnection status.
     */
    public function markAsDisconnected(): void
    {
        $this->update([
            'is_connected' => false,
            'last_disconnected_at' => now(),
        ]);
    }

    /**
     * Get device status label.
     */
    public function getStatusLabel(): string
    {
        if (!$this->is_enabled) {
            return 'disabled';
        }

        return $this->is_connected ? 'connected' : 'disconnected';
    }

    /**
     * Get device type translated name.
     */
    public function getDeviceTypeLabel(): string
    {
        $types = [
            'barcode_scanner' => __('pos.barcode_scanner'),
            'thermal_printer' => __('pos.thermal_printer'),
            'cash_drawer' => __('pos.cash_drawer'),
        ];

        return $types[$this->device_type] ?? $this->device_type;
    }
}

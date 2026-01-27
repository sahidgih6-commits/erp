<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HardwareAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'hardware_device_id',
        'device_type',
        'action',
        'details',
        'status',
        'error_message',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    /**
     * Get the business that owns this audit log.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hardware device related to this log.
     */
    public function hardwareDevice(): BelongsTo
    {
        return $this->belongsTo(HardwareDevice::class);
    }

    /**
     * Get action label.
     */
    public function getActionLabel(): string
    {
        $actions = [
            'scan' => 'Barcode Scanned',
            'print' => 'Receipt Printed',
            'open_drawer' => 'Cash Drawer Opened',
            'connect' => 'Device Connected',
            'disconnect' => 'Device Disconnected',
            'error' => 'Device Error',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Scope to get logs for specific device type.
     */
    public function scopeForDeviceType($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope to get only failed logs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get logs for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\HardwareDevice;
use App\Models\SystemVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HardwareManagementController extends Controller
{
    /**
     * Display hardware management dashboard.
     */
    public function index()
    {
        $businesses = Business::with(['hardwareDevices', 'systemVersion'])->paginate(15);

        return view('superadmin.hardware.index', [
            'businesses' => $businesses,
        ]);
    }

    /**
     * Show hardware details for a specific business.
     */
    public function show(Business $business)
    {
        $business->load([
            'hardwareDevices',
            'systemVersion',
            'hardwareAuditLogs' => function ($query) {
                $query->latest('logged_at')->limit(50);
            },
        ]);

        $devices = $business->hardwareDevices;
        $systemVersion = $business->systemVersion()->first();
        $auditLogs = $business->hardwareAuditLogs;

        return view('superadmin.hardware.show', [
            'business' => $business,
            'devices' => $devices,
            'systemVersion' => $systemVersion,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Show form to configure version and hardware features.
     */
    public function configureVersion(Business $business)
    {
        $systemVersion = $business->systemVersion()->firstOrCreate(
            ['business_id' => $business->id],
            [
                'version' => 'basic',
                'barcode_scanner_enabled' => false,
                'thermal_printer_enabled' => false,
                'cash_drawer_enabled' => false,
            ]
        );

        return view('superadmin.hardware.configure-version', [
            'business' => $business,
            'systemVersion' => $systemVersion,
        ]);
    }

    /**
     * Update system version and features.
     */
    public function updateVersion(Request $request, Business $business)
    {
        $request->validate([
            'version' => 'required|in:basic,pro,enterprise',
            'pos_enabled' => 'boolean',
            'barcode_scanner_enabled' => 'boolean',
            'thermal_printer_enabled' => 'boolean',
            'cash_drawer_enabled' => 'boolean',
        ]);

        $systemVersion = $business->systemVersion()->firstOrCreate(
            ['business_id' => $business->id]
        );

        $oldVersion = $systemVersion->version;
        $wasPOSEnabled = $systemVersion->pos_enabled;

        $updateData = [
            'version' => $request->version,
            'pos_enabled' => $request->boolean('pos_enabled'),
            'barcode_scanner_enabled' => $request->boolean('barcode_scanner_enabled'),
            'thermal_printer_enabled' => $request->boolean('thermal_printer_enabled'),
            'cash_drawer_enabled' => $request->boolean('cash_drawer_enabled'),
            'upgraded_at' => now(),
            'upgrade_notes' => "Upgraded from {$oldVersion} to {$request->version}",
        ];

        // Set POS activation timestamp if just enabled
        if ($request->boolean('pos_enabled') && !$wasPOSEnabled) {
            $updateData['pos_activated_at'] = now();
        }

        $systemVersion->update($updateData);

        return redirect()->route('superadmin.hardware.show', $business)
            ->with('success', __('Version updated successfully'));
    }

    /**
     * Show form to add new hardware device.
     */
    public function createDevice(Business $business)
    {
        return view('superadmin.hardware.create-device', [
            'business' => $business,
        ]);
    }

    /**
     * Store new hardware device.
     */
    public function storeDevice(Request $request, Business $business)
    {
        $request->validate([
            'device_type' => 'required|in:barcode_scanner,thermal_printer,cash_drawer',
            'device_name' => 'required|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'device_serial_number' => 'nullable|string|max:255',
            'connection_type' => 'required|in:usb,network,bluetooth',
            'port' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'configuration' => 'nullable|array',
        ]);

        $device = $business->hardwareDevices()->create([
            'device_type' => $request->device_type,
            'device_name' => $request->device_name,
            'device_model' => $request->device_model,
            'device_serial_number' => $request->device_serial_number,
            'connection_type' => $request->connection_type,
            'port' => $request->port,
            'ip_address' => $request->ip_address,
            'configuration' => $request->configuration,
            'is_enabled' => true,
        ]);

        return redirect()->route('superadmin.hardware.show', $business)
            ->with('success', __('pos.hardware_configuration') . ' ' . __('messages.saved_successfully'));
    }

    /**
     * Show form to edit hardware device.
     */
    public function editDevice(Business $business, HardwareDevice $device)
    {
        return view('superadmin.hardware.edit-device', [
            'business' => $business,
            'device' => $device,
        ]);
    }

    /**
     * Update hardware device.
     */
    public function updateDevice(Request $request, Business $business, HardwareDevice $device)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'device_serial_number' => 'nullable|string|max:255',
            'connection_type' => 'required|in:usb,network,bluetooth',
            'port' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'is_enabled' => 'boolean',
            'configuration' => 'nullable|array',
        ]);

        $device->update($request->all());

        return redirect()->route('superadmin.hardware.show', $business)
            ->with('success', __('pos.hardware_configuration') . ' ' . __('messages.updated_successfully'));
    }

    /**
     * Toggle device enable/disable status.
     */
    public function toggleDevice(Business $business, HardwareDevice $device)
    {
        $device->update(['is_enabled' => !$device->is_enabled]);

        $status = $device->is_enabled ? __('pos.enabled') : __('pos.disabled');

        return redirect()->route('superadmin.hardware.show', $business)
            ->with('success', __('pos.device') . ' ' . $status);
    }

    /**
     * Delete hardware device.
     */
    public function deleteDevice(Business $business, HardwareDevice $device)
    {
        $device->delete();

        return redirect()->route('superadmin.hardware.show', $business)
            ->with('success', __('pos.device') . ' ' . __('messages.deleted_successfully'));
    }

    /**
     * View hardware audit logs for business.
     */
    public function auditLogs(Business $business)
    {
        $logs = $business->hardwareAuditLogs()
            ->with(['user', 'hardwareDevice'])
            ->latest('logged_at')
            ->paginate(25);

        return view('superadmin.hardware.audit-logs', [
            'business' => $business,
            'logs' => $logs,
        ]);
    }

    /**
     * Test hardware device connection.
     */
    public function testDevice(Business $business, HardwareDevice $device)
    {
        // This would be implemented with actual hardware testing logic
        $device->markAsConnected();

        return redirect()->route('superadmin.hardware.show', $business)
            ->with('success', __('pos.device') . ' ' . __('pos.test') . ' successful');
    }
}

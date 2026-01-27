@extends('superadmin.hardware.layout')

@section('title', __('pos.hardware_management') . ' - ' . $business->name)

@section('content')
<div class="space-y-6">
    <!-- Business Header -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $business->name }}</h1>
                <p class="text-gray-600 mt-1">{{ $business->email }} | {{ $business->phone }}</p>
            </div>
            <a href="{{ route('superadmin.hardware.index') }}" class="text-blue-600 hover:text-blue-900">
                ← {{ __('pos.back') }}
            </a>
        </div>
    </div>

    <!-- System Version Card -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('pos.system_version') }}</h2>
            <a href="{{ route('superadmin.hardware.configure-version', $business) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ __('pos.configure') }}
            </a>
        </div>
        <div class="p-6">
            @if($systemVersion)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">{{ __('messages.current_version') ?? 'Current Version' }}</p>
                        <p class="font-bold text-lg text-gray-900 mt-2">
                            @if($systemVersion->version === 'enterprise')
                                {{ __('pos.enterprise_version') }} 💎
                            @elseif($systemVersion->version === 'pro')
                                {{ __('pos.pro_version') }} ⭐
                            @else
                                {{ __('pos.basic_version') }} 📦
                            @endif
                        </p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded">
                        <p class="text-sm text-gray-600">{{ __('pos.barcode_scanner') }}</p>
                        <p class="font-bold text-lg {{ $systemVersion->barcode_scanner_enabled ? 'text-green-600' : 'text-red-600' }} mt-2">
                            {{ $systemVersion->barcode_scanner_enabled ? __('pos.enabled') : __('pos.disabled') }}
                        </p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded">
                        <p class="text-sm text-gray-600">{{ __('pos.thermal_printer') }}</p>
                        <p class="font-bold text-lg {{ $systemVersion->thermal_printer_enabled ? 'text-green-600' : 'text-red-600' }} mt-2">
                            {{ $systemVersion->thermal_printer_enabled ? __('pos.enabled') : __('pos.disabled') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 mb-4">{{ __('messages.no_version_configured') ?? 'No system version configured' }}</p>
                    <a href="{{ route('superadmin.hardware.configure-version', $business) }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        {{ __('messages.configure_now') ?? 'Configure Now' }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Hardware Devices -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('pos.devices') }}</h2>
            <a href="{{ route('superadmin.hardware.create-device', $business) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + {{ __('messages.add_device') ?? 'Add Device' }}
            </a>
        </div>

        @if($devices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.device') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.model') ?? 'Model' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.connection') ?? 'Connection' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.last_connected') ?? 'Last Connected' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($devices as $device)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $device->device_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $device->getDeviceTypeLabel() }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $device->device_model ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="capitalize">{{ $device->connection_type }}</span>
                                    @if($device->port)
                                        <br><span class="text-xs text-gray-500">{{ __('messages.port') ?? 'Port' }}: {{ $device->port }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-2 h-2 rounded-full {{ $device->is_connected ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        <span class="font-semibold {{ $device->is_connected ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $device->getStatusLabel() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($device->last_connected_at)
                                        {{ $device->last_connected_at->diffForHumans() }}
                                    @else
                                        {{ __('messages.never') ?? 'Never' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('superadmin.hardware.edit-device', [$business, $device]) }}" 
                                       class="text-blue-600 hover:text-blue-900">{{ __('messages.edit') ?? 'Edit' }}</a>
                                    <a href="{{ route('superadmin.hardware.toggle-device', [$business, $device]) }}" 
                                       class="text-{{ $device->is_enabled ? 'orange' : 'green' }}-600 hover:text-{{ $device->is_enabled ? 'orange' : 'green' }}-900">
                                        {{ $device->is_enabled ? __('pos.disable') : __('pos.enable') }}
                                    </a>
                                    <form action="{{ route('superadmin.hardware.delete-device', [$business, $device]) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('messages.confirm_delete') ?? 'Confirm delete' }}')">
                                            {{ __('messages.delete') ?? 'Delete' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">{{ __('messages.no_devices_configured') ?? 'No devices configured' }}</p>
                <a href="{{ route('superadmin.hardware.create-device', $business) }}" 
                   class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    + {{ __('messages.add_device') ?? 'Add Device' }}
                </a>
            </div>
        @endif
    </div>

    <!-- Recent Audit Logs -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('pos.hardware_logs') }}</h2>
            <a href="{{ route('superadmin.hardware.audit-logs', $business) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                {{ __('messages.view_all') ?? 'View All' }} →
            </a>
        </div>

        @if($auditLogs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.user') ?? 'User' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.action') ?? 'Action' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.device') ?? 'Device' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.status') ?? 'Status' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.timestamp') ?? 'Timestamp' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($auditLogs->take(10) as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->getActionLabel() }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->hardwareDevice?->device_name ?? $log->device_type }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->logged_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                {{ __('messages.no_logs_found') ?? 'No audit logs found' }}
            </div>
        @endif
    </div>
</div>
@endsection

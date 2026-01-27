@extends('superadmin.hardware.layout')

@section('title', __('messages.edit_device') . ' - ' . $business->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('superadmin.hardware.index') }}" class="text-blue-600 hover:text-blue-900">{{ __('pos.hardware_management') }}</a>
        <span class="text-gray-500">/</span>
        <a href="{{ route('superadmin.hardware.show', $business) }}" class="text-blue-600 hover:text-blue-900">{{ $business->name }}</a>
        <span class="text-gray-500">/</span>
        <span class="text-gray-900">{{ __('messages.edit_device') ?? 'Edit Device' }}</span>
    </div>

    <!-- Edit Device Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('superadmin.hardware.update-device', [$business, $device]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Device Type (Read-only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('pos.device_type') ?? 'Device Type' }}</label>
                <div class="px-4 py-2 bg-gray-100 rounded-lg text-gray-900 font-medium">
                    {{ $device->getDeviceTypeLabel() }}
                </div>
            </div>

            <!-- Device Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.device_name') ?? 'Device Name' }} <span class="text-red-500">*</span></label>
                <input type="text" name="device_name" required value="{{ old('device_name', $device->device_name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Main Counter Scanner">
                @error('device_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Device Model -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.model') ?? 'Model' }}</label>
                <input type="text" name="device_model" value="{{ old('device_model', $device->device_model) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Honeywell Voyager">
                @error('device_model')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Serial Number -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.serial_number') ?? 'Serial Number' }}</label>
                <input type="text" name="device_serial_number" value="{{ old('device_serial_number', $device->device_serial_number) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="ABC123XYZ">
                @error('device_serial_number')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Connection Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.connection_type') ?? 'Connection Type' }} <span class="text-red-500">*</span></label>
                <select name="connection_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="connectionType" onchange="updateConnectionFields()">
                    <option value="usb" @if($device->connection_type === 'usb') selected @endif>USB</option>
                    <option value="network" @if($device->connection_type === 'network') selected @endif>{{ __('messages.network') ?? 'Network' }}</option>
                    <option value="bluetooth" @if($device->connection_type === 'bluetooth') selected @endif>Bluetooth</option>
                </select>
                @error('connection_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Port (for USB/Serial) -->
            <div id="portField">
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.port') ?? 'Port' }}</label>
                <input type="text" name="port" value="{{ old('port', $device->port) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="COM3, /dev/ttyUSB0">
                @error('port')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- IP Address (for Network) -->
            <div id="ipField" class="hidden">
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.ip_address') ?? 'IP Address' }}</label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $device->ip_address) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="192.168.1.100">
                @error('ip_address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer p-4 bg-gray-50 rounded-lg">
                    <input type="checkbox" name="is_enabled" value="1" class="w-4 h-4"
                           @if(old('is_enabled', $device->is_enabled)) checked @endif>
                    <div>
                        <p class="font-medium text-gray-900">{{ __('pos.enabled') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.device_is_active') ?? 'Device is active and available for use' }}</p>
                    </div>
                </label>
            </div>

            <!-- Configuration (JSON) -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.advanced_config') ?? 'Advanced Configuration' }} (JSON)</label>
                <textarea name="configuration" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('configuration', json_encode($device->configuration ?? [])) }}</textarea>
                <p class="text-xs text-gray-600 mt-1">{{ __('messages.optional_json_config') ?? 'Optional: Add device-specific JSON configuration' }}</p>
                @error('configuration')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Device Status Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700 font-medium mb-2">{{ __('pos.device_status') }}</p>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-blue-600">{{ __('messages.connection_status') ?? 'Connection Status' }}:</p>
                        <p class="font-semibold text-gray-900 flex items-center gap-2 mt-1">
                            <span class="inline-block w-2 h-2 {{ $device->is_connected ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                            {{ $device->is_connected ? __('pos.connected') : __('pos.disconnected') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-blue-600">{{ __('messages.last_connected') ?? 'Last Connected' }}:</p>
                        <p class="font-semibold text-gray-900 mt-1">
                            @if($device->last_connected_at)
                                {{ $device->last_connected_at->format('d M Y, H:i') }}
                            @else
                                {{ __('messages.never') ?? 'Never' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    {{ __('messages.update_device') ?? 'Update Device' }}
                </button>
                <a href="{{ route('superadmin.hardware.show', $business) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                    {{ __('pos.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function updateConnectionFields() {
        const type = document.getElementById('connectionType').value;
        document.getElementById('portField').classList.toggle('hidden', type === 'network');
        document.getElementById('ipField').classList.toggle('hidden', type !== 'network');
    }
    updateConnectionFields();
</script>
@endsection

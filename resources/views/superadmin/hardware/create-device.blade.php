@extends('superadmin.hardware.layout')

@section('title', __('messages.add_device') . ' - ' . $business->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('superadmin.hardware.index') }}" class="text-blue-600 hover:text-blue-900">{{ __('pos.hardware_management') }}</a>
        <span class="text-gray-500">/</span>
        <a href="{{ route('superadmin.hardware.show', $business) }}" class="text-blue-600 hover:text-blue-900">{{ $business->name }}</a>
        <span class="text-gray-500">/</span>
        <span class="text-gray-900">{{ __('messages.add_device') ?? 'Add Device' }}</span>
    </div>

    <!-- Add Device Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('superadmin.hardware.store-device', $business) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Device Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('pos.device_type') ?? 'Device Type' }} <span class="text-red-500">*</span></label>
                <select name="device_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- {{ __('messages.select_type') ?? 'Select Type' }} --</option>
                    <option value="barcode_scanner">{{ __('pos.barcode_scanner') }}</option>
                    <option value="thermal_printer">{{ __('pos.thermal_printer') }}</option>
                    <option value="cash_drawer">{{ __('pos.cash_drawer') }}</option>
                </select>
                @error('device_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Device Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.device_name') ?? 'Device Name' }} <span class="text-red-500">*</span></label>
                <input type="text" name="device_name" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ __('messages.e_g') ?? 'E.g.' }} Main Counter Scanner">
                @error('device_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Device Model -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.model') ?? 'Model' }}</label>
                <input type="text" name="device_model"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ __('messages.e_g') ?? 'E.g.' }} Honeywell Voyager">
                @error('device_model')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Serial Number -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.serial_number') ?? 'Serial Number' }}</label>
                <input type="text" name="device_serial_number"
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
                    <option value="usb">USB</option>
                    <option value="network">{{ __('messages.network') ?? 'Network' }}</option>
                    <option value="bluetooth">Bluetooth</option>
                </select>
                @error('connection_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Port (for USB/Serial) -->
            <div id="portField">
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.port') ?? 'Port' }}</label>
                <input type="text" name="port"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ __('messages.e_g') ?? 'E.g.' }} COM3, /dev/ttyUSB0">
                @error('port')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- IP Address (for Network) -->
            <div id="ipField" class="hidden">
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.ip_address') ?? 'IP Address' }}</label>
                <input type="text" name="ip_address"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="192.168.1.100">
                @error('ip_address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Configuration (JSON) -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('messages.advanced_config') ?? 'Advanced Configuration' }} (JSON)</label>
                <textarea name="configuration" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                          placeholder='{"baud_rate": 9600, "timeout": 5}'>@if(old('configuration')){{ old('configuration') }}@endif</textarea>
                <p class="text-xs text-gray-600 mt-1">{{ __('messages.optional_json_config') ?? 'Optional: Add device-specific JSON configuration' }}</p>
                @error('configuration')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    {{ __('messages.add_device') ?? 'Add Device' }}
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

@extends('pos.layout')

@section('title', 'হার্ডওয়্যার কনফিগারেশন')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">হার্ডওয়্যার ডিভাইস কনফিগারেশন</h1>
            <p class="text-gray-600 mt-1">POS সিস্টেমের জন্য হার্ডওয়্যার ডিভাইস সেটআপ এবং পরিচালনা করুন</p>
        </div>
        <a href="{{ route('pos.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ← ফিরে যান
        </a>
    </div>

    <!-- System Version Info -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-6 mb-6 shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold">বর্তমান সিস্টেম ভার্সন</h3>
                <p class="text-2xl font-bold mt-2">{{ ucfirst($systemVersion->version) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-90">সাপোর্ট করে:</p>
                <div class="mt-2 space-y-1 text-sm">
                    @if($systemVersion->canUseBarcodeScanner())
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                            বারকোড স্ক্যানার
                        </div>
                    @endif
                    @if($systemVersion->canUseThermalPrinter())
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                            থার্মাল প্রিন্টার
                        </div>
                    @endif
                    @if($systemVersion->canUseCashDrawer())
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                            ক্যাশ ড্রয়ার
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Configured Devices -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">কনফিগার করা ডিভাইস</h2>
            <div class="flex gap-2">
                <button onclick="scanForDevices()" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    ডিভাইস খুঁজুন
                </button>
                <button onclick="document.getElementById('addDeviceModal').classList.remove('hidden')" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + নতুন ডিভাইস যোগ করুন
                </button>
            </div>
        </div>

        <!-- Auto-detected Devices -->
        <div id="detectedDevicesSection" class="hidden mb-6 p-4 bg-yellow-50 border border-yellow-300 rounded-lg">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-900">🔍 সনাক্ত করা নতুন ডিভাইস</h3>
                <button onclick="document.getElementById('detectedDevicesSection').classList.add('hidden')" 
                        class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div id="detectedDevicesList" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Detected devices will be inserted here -->
            </div>
        </div>

        @if($devices->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($devices as $device)
                    <div class="border rounded-lg p-4 {{ $device->is_enabled ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-lg">{{ $device->device_name }}</h3>
                                <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $device->device_type)) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-3 h-3 {{ $device->is_connected ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                            </div>
                        </div>

                        <div class="space-y-1 text-sm mb-4">
                            @if($device->brand)
                                <p><strong>ব্র্যান্ড:</strong> {{ $device->brand }}</p>
                            @endif
                            @if($device->model)
                                <p><strong>মডেল:</strong> {{ $device->model }}</p>
                            @endif
                            <p><strong>সংযোগ:</strong> {{ ucfirst($device->connection_type) }}</p>
                            @if($device->port)
                                <p><strong>পোর্ট:</strong> {{ $device->port }}</p>
                            @endif
                            @if($device->ip_address)
                                <p><strong>IP:</strong> {{ $device->ip_address }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-2">
                                স্ট্যাটাস: 
                                <span class="{{ $device->is_enabled ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ $device->is_enabled ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                </span>
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="testDevice({{ $device->id }})" 
                                    class="flex-1 px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                টেস্ট করুন
                            </button>
                            <a href="{{ route('pos.hardware.toggle', $device) }}" 
                               class="flex-1 px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600 text-center">
                                {{ $device->is_enabled ? 'বন্ধ করুন' : 'চালু করুন' }}
                            </a>
                            <form action="{{ route('pos.hardware.destroy', $device) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('নিশ্চিত?')"
                                        class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                                    মুছুন
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                </svg>
                <p class="mt-4 text-lg">কোনো হার্ডওয়্যার ডিভাইস কনফিগার করা নেই</p>
                <p class="mt-2">নতুন ডিভাইস যোগ করতে উপরের বাটনে ক্লিক করুন</p>
            </div>
        @endif
    </div>

    <!-- Supported Devices Guide -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">সাপোর্টেড হার্ডওয়্যার ডিভাইস</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Barcode Scanner -->
            <div class="border rounded-lg p-4">
                <h3 class="font-bold text-lg mb-3 text-blue-600">📷 বারকোড স্ক্যানার</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="font-semibold">সাপোর্টেড ব্র্যান্ড:</p>
                        <ul class="list-disc list-inside text-gray-600">
                            @foreach($supportedDevices['barcode_scanner']['brands'] as $brand)
                                <li>{{ $brand }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold">সংযোগের ধরন:</p>
                        <p class="text-gray-600">{{ implode(', ', $supportedDevices['barcode_scanner']['connection']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Thermal Printer -->
            <div class="border rounded-lg p-4">
                <h3 class="font-bold text-lg mb-3 text-green-600">🖨️ থার্মাল প্রিন্টার</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="font-semibold">সাপোর্টেড ব্র্যান্ড:</p>
                        <ul class="list-disc list-inside text-gray-600">
                            @foreach($supportedDevices['thermal_printer']['brands'] as $brand)
                                <li>{{ $brand }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold">পেপার সাইজ:</p>
                        <p class="text-gray-600">{{ implode(', ', $supportedDevices['thermal_printer']['paper_sizes']) }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">সংযোগের ধরন:</p>
                        <p class="text-gray-600">{{ implode(', ', $supportedDevices['thermal_printer']['connection']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Cash Drawer -->
            <div class="border rounded-lg p-4">
                <h3 class="font-bold text-lg mb-3 text-purple-600">💰 ক্যাশ ড্রয়ার</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="font-semibold">সাপোর্টেড ব্র্যান্ড:</p>
                        <ul class="list-disc list-inside text-gray-600">
                            @foreach($supportedDevices['cash_drawer']['brands'] as $brand)
                                <li>{{ $brand }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold">সংযোগের ধরন:</p>
                        <p class="text-gray-600">{{ implode(', ', $supportedDevices['cash_drawer']['connection']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Device Modal -->
<div id="addDeviceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">নতুন হার্ডওয়্যার ডিভাইস যোগ করুন</h2>
            <button onclick="document.getElementById('addDeviceModal').classList.add('hidden')" 
                    class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('pos.hardware.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold mb-1">ডিভাইসের ধরন *</label>
                <select name="device_type" required class="w-full px-3 py-2 border rounded">
                    <option value="">নির্বাচন করুন</option>
                    <option value="barcode_scanner">বারকোড স্ক্যানার</option>
                    <option value="thermal_printer">থার্মাল প্রিন্টার</option>
                    <option value="cash_drawer">ক্যাশ ড্রয়ার</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">ডিভাইসের নাম *</label>
                <input type="text" name="device_name" required class="w-full px-3 py-2 border rounded" placeholder="যেমন: Main Scanner">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">ব্র্যান্ড</label>
                    <input type="text" name="brand" class="w-full px-3 py-2 border rounded" placeholder="যেমন: Epson">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">মডেল</label>
                    <input type="text" name="model" class="w-full px-3 py-2 border rounded" placeholder="যেমন: TM-T20">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">সংযোগের ধরন *</label>
                <select name="connection_type" required class="w-full px-3 py-2 border rounded">
                    <option value="">নির্বাচন করুন</option>
                    <option value="USB">USB</option>
                    <option value="Serial">Serial</option>
                    <option value="Ethernet">Ethernet</option>
                    <option value="Bluetooth">Bluetooth</option>
                    <option value="WiFi">Wi-Fi</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">পোর্ট</label>
                    <input type="text" name="port" class="w-full px-3 py-2 border rounded" placeholder="যেমন: COM3 or /dev/ttyUSB0">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">IP ঠিকানা</label>
                    <input type="text" name="ip_address" class="w-full px-3 py-2 border rounded" placeholder="যেমন: 192.168.1.100">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    সংরক্ষণ করুন
                </button>
                <button type="button" onclick="document.getElementById('addDeviceModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    বাতিল
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function testDevice(deviceId) {
    fetch(`/pos/hardware/${deviceId}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ডিভাইস টেস্ট সফল!\n\nডিভাইস: ' + data.device_type + '\nসংযোগ: ' + data.connection_type);
            location.reload();
        } else {
            alert('❌ ডিভাইস টেস্ট ব্যর্থ!\n\n' + data.message);
        }
    })
    .catch(error => {
        alert('❌ টেস্ট করতে সমস্যা হয়েছে');
    });
}

// Auto-detect USB/Serial devices
async function scanForDevices() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="animate-spin">⌛</span> খুঁজছি...';
    button.disabled = true;
    
    try {
        // Check for Web Serial API support (Chrome/Edge)
        if ('serial' in navigator) {
            await detectSerialDevices();
        }
        
        // Check for Web USB API support
        if ('usb' in navigator) {
            await detectUSBDevices();
        }
        
        // Fallback: Show manual detection info
        if (!('serial' in navigator) && !('usb' in navigator)) {
            alert('⚠️ আপনার ব্রাউজার স্বয়ংক্রিয় ডিভাইস সনাক্তকরণ সমর্থন করে না।\n\nদয়া করে ম্যানুয়ালি ডিভাইস যোগ করুন।');
        }
    } catch (error) {
        console.error('Device scan error:', error);
        alert('❌ ডিভাইস খুঁজতে সমস্যা হয়েছে।\n\n' + error.message);
    } finally {
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

async function detectSerialDevices() {
    try {
        const ports = await navigator.serial.getPorts();
        
        if (ports.length === 0) {
            // Request port selection
            const port = await navigator.serial.requestPort();
            if (port) {
                const info = port.getInfo();
                addDetectedDevice({
                    name: 'Serial Device',
                    port: 'Serial Port',
                    vendor: info.usbVendorId ? `0x${info.usbVendorId.toString(16)}` : null,
                    product: info.usbProductId ? `0x${info.usbProductId.toString(16)}` : null
                });
            }
        } else {
            ports.forEach((port, index) => {
                const info = port.getInfo();
                addDetectedDevice({
                    name: `Serial Device ${index + 1}`,
                    port: `Serial Port ${index + 1}`,
                    vendor: info.usbVendorId ? `0x${info.usbVendorId.toString(16)}` : null,
                    product: info.usbProductId ? `0x${info.usbProductId.toString(16)}` : null
                });
            });
        }
    } catch (error) {
        console.log('Serial detection cancelled or failed:', error);
    }
}

async function detectUSBDevices() {
    try {
        const devices = await navigator.usb.getDevices();
        
        if (devices.length === 0) {
            // Request device selection
            const device = await navigator.usb.requestDevice({ filters: [] });
            if (device) {
                addDetectedDevice({
                    name: device.productName || 'USB Device',
                    port: 'USB',
                    vendor: device.manufacturerName || `VID: ${device.vendorId}`,
                    product: device.productName || `PID: ${device.productId}`
                });
            }
        } else {
            devices.forEach(device => {
                addDetectedDevice({
                    name: device.productName || 'USB Device',
                    port: 'USB',
                    vendor: device.manufacturerName || `VID: ${device.vendorId}`,
                    product: device.productName || `PID: ${device.productId}`
                });
            });
        }
    } catch (error) {
        console.log('USB detection cancelled or failed:', error);
    }
}

function addDetectedDevice(deviceInfo) {
    const section = document.getElementById('detectedDevicesSection');
    const list = document.getElementById('detectedDevicesList');
    
    // Determine device type based on name/vendor
    let deviceType = 'barcode_scanner';
    const nameLower = (deviceInfo.name + ' ' + deviceInfo.vendor + ' ' + deviceInfo.product).toLowerCase();
    
    if (nameLower.includes('printer') || nameLower.includes('epson') || nameLower.includes('star')) {
        deviceType = 'thermal_printer';
    } else if (nameLower.includes('drawer')) {
        deviceType = 'cash_drawer';
    }
    
    const deviceCard = document.createElement('div');
    deviceCard.className = 'border border-green-400 bg-white rounded p-3';
    deviceCard.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <div>
                <h4 class="font-bold text-sm">${deviceInfo.name}</h4>
                <p class="text-xs text-gray-600">পোর্ট: ${deviceInfo.port}</p>
                ${deviceInfo.vendor ? `<p class="text-xs text-gray-600">ব্র্যান্ড: ${deviceInfo.vendor}</p>` : ''}
            </div>
            <span class="px-2 py-1 text-xs rounded ${
                deviceType === 'barcode_scanner' ? 'bg-blue-100 text-blue-800' :
                deviceType === 'thermal_printer' ? 'bg-purple-100 text-purple-800' :
                'bg-green-100 text-green-800'
            }">
                ${deviceType === 'barcode_scanner' ? 'স্ক্যানার' :
                  deviceType === 'thermal_printer' ? 'প্রিন্টার' : 'ড্রয়ার'}
            </span>
        </div>
        <button onclick="autoAddDevice('${deviceInfo.name}', '${deviceType}', '${deviceInfo.port}', '${deviceInfo.vendor}', '${deviceInfo.product}')"
                class="w-full px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
            + এই ডিভাইস যোগ করুন
        </button>
    `;
    
    list.appendChild(deviceCard);
    section.classList.remove('hidden');
}

function autoAddDevice(name, type, port, vendor, product) {
    fetch('/pos/hardware/auto-add', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            device_name: name,
            device_type: type,
            port: port,
            vendor: vendor,
            product: product
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ডিভাইস সফলভাবে যোগ হয়েছে!');
            location.reload();
        } else {
            alert('❌ ডিভাইস যোগ করতে সমস্যা হয়েছে।');
        }
    })
    .catch(error => {
        alert('❌ সমস্যা হয়েছে: ' + error.message);
    });
}
</script>
@endsection

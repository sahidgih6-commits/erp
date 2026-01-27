@extends('superadmin.hardware.layout')

@section('title', __('pos.system_version') . ' - ' . $business->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('superadmin.hardware.index') }}" class="text-blue-600 hover:text-blue-900">{{ __('pos.hardware_management') }}</a>
        <span class="text-gray-500">/</span>
        <span class="text-gray-900">{{ $business->name }}</span>
        <span class="text-gray-500">/</span>
        <span class="text-gray-900">{{ __('pos.system_version') }}</span>
    </div>

    <!-- Version Selection -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('superadmin.hardware.update-version', $business) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Current Version Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700">
                    💡 {{ __('messages.current_version') ?? 'Current Version' }}: 
                    <strong>
                        @if($systemVersion->version === 'enterprise')
                            {{ __('pos.enterprise_version') }}
                        @elseif($systemVersion->version === 'pro')
                            {{ __('pos.pro_version') }}
                        @else
                            {{ __('pos.basic_version') }}
                        @endif
                    </strong>
                </p>
                @if($systemVersion->upgraded_at)
                    <p class="text-xs text-blue-600 mt-1">{{ __('messages.last_updated') ?? 'Last Updated' }}: {{ $systemVersion->upgraded_at->format('d M Y, H:i') }}</p>
                @endif
            </div>

            <!-- Version Options -->
            <div class="space-y-4">
                <p class="text-lg font-semibold text-gray-900">{{ __('pos.system_version') }}</p>

                <!-- Basic Version -->
                <label class="flex items-start gap-4 p-4 border-2 rounded-lg cursor-pointer" 
                       id="basicLabel">
                    <input type="radio" name="version" value="basic" class="mt-1"
                           @if($systemVersion->version === 'basic') checked @endif>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ __('pos.basic_version') }} 📦</p>
                        <p class="text-sm text-gray-600 mt-1">{{ __('messages.basic_features') ?? 'Basic features for small shops' }}</p>
                        <ul class="text-xs text-gray-600 mt-2 space-y-1">
                            <li>✓ {{ __('pos.manual_product') ?? 'Manual product entry' }}</li>
                            <li>✗ {{ __('pos.barcode_scanner') }}</li>
                            <li>✗ {{ __('pos.thermal_printer') }}</li>
                            <li>✗ {{ __('pos.cash_drawer') }}</li>
                        </ul>
                    </div>
                </label>

                <!-- Pro Version -->
                <label class="flex items-start gap-4 p-4 border-2 rounded-lg cursor-pointer"
                       id="proLabel">
                    <input type="radio" name="version" value="pro" class="mt-1"
                           @if($systemVersion->version === 'pro') checked @endif>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ __('pos.pro_version') }} ⭐</p>
                        <p class="text-sm text-gray-600 mt-1">{{ __('messages.professional_features') ?? 'For growing businesses' }}</p>
                        <ul class="text-xs text-gray-600 mt-2 space-y-1">
                            <li>✓ {{ __('pos.manual_product') ?? 'Manual product entry' }}</li>
                            <li>✓ {{ __('pos.barcode_scanner') }}</li>
                            <li>✓ {{ __('pos.thermal_printer') }}</li>
                            <li>✗ {{ __('pos.cash_drawer') }}</li>
                        </ul>
                    </div>
                </label>

                <!-- Enterprise Version -->
                <label class="flex items-start gap-4 p-4 border-2 rounded-lg cursor-pointer"
                       id="enterpriseLabel">
                    <input type="radio" name="version" value="enterprise" class="mt-1"
                           @if($systemVersion->version === 'enterprise') checked @endif>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ __('pos.enterprise_version') }} 💎</p>
                        <p class="text-sm text-gray-600 mt-1">{{ __('messages.enterprise_features') ?? 'Full enterprise capabilities' }}</p>
                        <ul class="text-xs text-gray-600 mt-2 space-y-1">
                            <li>✓ {{ __('pos.manual_product') ?? 'Manual product entry' }}</li>
                            <li>✓ {{ __('pos.barcode_scanner') }}</li>
                            <li>✓ {{ __('pos.thermal_printer') }}</li>
                            <li>✓ {{ __('pos.cash_drawer') }}</li>
                            <li>✓ {{ __('messages.advanced_reports') ?? 'Advanced reporting' }}</li>
                            <li>✓ {{ __('messages.multi_branch') ?? 'Multi-branch support' }}</li>
                        </ul>
                    </div>
                </label>
            </div>

            <!-- Feature Toggles -->
            <div class="border-t pt-6 space-y-4">
                <p class="text-lg font-semibold text-gray-900">{{ __('pos.hardware_configuration') }}</p>

                <!-- POS System Activation -->
                <div class="flex items-center justify-between p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">🔑 {{ __('pos.pos_system_activation') ?? 'POS System Activation' }}</p>
                        <p class="text-sm text-gray-600">{{ __('pos.enable_pos_for_business') ?? 'Enable POS system to allow cashier role and hardware features' }}</p>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="pos_enabled" value="1" class="w-5 h-5"
                               @if($systemVersion->pos_enabled) checked @endif>
                        <span class="text-sm font-semibold text-blue-600">{{ __('pos.enabled') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ __('pos.barcode_scanner') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.enable_disable_feature') ?? 'Enable or disable this feature' }}</p>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="barcode_scanner_enabled" value="1" class="w-4 h-4"
                               @if($systemVersion->barcode_scanner_enabled) checked @endif>
                        <span class="text-sm text-gray-600">{{ __('pos.enabled') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ __('pos.thermal_printer') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.enable_disable_feature') ?? 'Enable or disable this feature' }}</p>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="thermal_printer_enabled" value="1" class="w-4 h-4"
                               @if($systemVersion->thermal_printer_enabled) checked @endif>
                        <span class="text-sm text-gray-600">{{ __('pos.enabled') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ __('pos.cash_drawer') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.enable_disable_feature') ?? 'Enable or disable this feature' }}</p>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="cash_drawer_enabled" value="1" class="w-4 h-4"
                               @if($systemVersion->cash_drawer_enabled) checked @endif>
                        <span class="text-sm text-gray-600">{{ __('pos.enabled') }}</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    {{ __('pos.save_changes') }}
                </button>
                <a href="{{ route('superadmin.hardware.show', $business) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                    {{ __('pos.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Warning Message -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-yellow-800">
            ⚠️ <strong>{{ __('messages.note') ?? 'Note' }}:</strong>
            {{ __('messages.downgrading_version_removes_features') ?? 'Downgrading version will remove access to hardware features for users in this business.' }}
        </p>
    </div>
</div>

<script>
    // Update border color on radio selection
    document.querySelectorAll('input[type="radio"][name="version"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('label[id$="Label"]').forEach(label => {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-300');
            });
            
            const parent = this.closest('label');
            if (parent) {
                parent.classList.add('border-blue-500', 'bg-blue-50');
                parent.classList.remove('border-gray-300');
            }
        });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[type="radio"][name="version"]:checked');
        if (checked) {
            checked.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
</script>
@endsection

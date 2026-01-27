@extends('superadmin.hardware.layout')

@section('title', __('pos.hardware_management'))

@section('content')
<div class="space-y-6">
    <!-- Businesses List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('messages.businesses') ?? 'Businesses' }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.business_name') ?? 'Business Name' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.system_version') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.devices') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.device_status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($businesses as $business)
                        @php
                            $systemVersion = $business->systemVersion->first();
                            $deviceCount = $business->hardwareDevices->count();
                            $connectedCount = $business->hardwareDevices->where('is_connected', true)->count();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <div>
                                    <p class="font-semibold">{{ $business->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $business->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($systemVersion)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $systemVersion->version === 'enterprise' ? 'bg-purple-100 text-purple-800' : 
                                           ($systemVersion->version === 'pro' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        @if($systemVersion->version === 'enterprise')
                                            {{ __('pos.enterprise_version') }}
                                        @elseif($systemVersion->version === 'pro')
                                            {{ __('pos.pro_version') }}
                                        @else
                                            {{ __('pos.basic_version') }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="font-semibold text-gray-900">{{ $deviceCount }}</span>
                                <span class="text-gray-500"> {{ __('messages.devices') ?? 'devices' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2 h-2 bg-{{ $connectedCount > 0 ? 'green' : 'red' }}-500 rounded-full"></span>
                                    <span class="text-gray-900">{{ $connectedCount }}/{{ $deviceCount }} {{ __('pos.connected') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('superadmin.hardware.show', $business) }}" 
                                       class="text-blue-600 hover:text-blue-900 font-medium">{{ __('messages.view') ?? 'View' }}</a>
                                    <a href="{{ route('superadmin.hardware.configure-version', $business) }}" 
                                       class="text-green-600 hover:text-green-900 font-medium">{{ __('pos.configure') }}</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                {{ __('messages.no_data_found') ?? 'No data found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $businesses->links() }}
        </div>
    </div>
</div>
@endsection

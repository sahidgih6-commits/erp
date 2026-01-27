@extends('pos.layout')

@section('title', __('pos.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Today's Sales -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">{{ __('pos.daily_sales') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        ৳ {{ number_format($todaysSalesTotal, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        {{ $todaysSalesCount }} {{ __('pos.transactions') }}
                    </p>
                </div>
                <svg class="w-12 h-12 text-blue-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
        </div>

        <!-- Barcode Scanner Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">{{ __('pos.barcode_scanner') }}</p>
                    <p class="text-lg font-semibold mt-2 {{ $barcodeScanner && $barcodeScanner->is_connected ? 'text-green-600' : 'text-gray-500' }}">
                        @if($barcodeScanner)
                            @if($barcodeScanner->is_connected)
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>{{ __('pos.connected') }}
                            @else
                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>{{ __('pos.disconnected') }}
                            @endif
                        @else
                            <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>{{ __('pos.not_configured') }}
                        @endif
                    </p>
                </div>
                <svg class="w-12 h-12 text-purple-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                </svg>
            </div>
        </div>

        <!-- Thermal Printer Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">{{ __('pos.thermal_printer') }}</p>
                    <p class="text-lg font-semibold mt-2 {{ $thermalPrinter && $thermalPrinter->is_connected ? 'text-green-600' : 'text-gray-500' }}">
                        @if($thermalPrinter)
                            @if($thermalPrinter->is_connected)
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>{{ __('pos.connected') }}
                            @else
                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>{{ __('pos.disconnected') }}
                            @endif
                        @else
                            <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>{{ __('pos.not_configured') }}
                        @endif
                    </p>
                </div>
                <svg class="w-12 h-12 text-orange-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V4zm3 2h6v2H6V6zm0 4h6v2H6v-2zm0 4h6v2H6v-2z"></path>
                </svg>
            </div>
        </div>

        <!-- Cash Drawer Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">{{ __('pos.cash_drawer') }}</p>
                    <p class="text-lg font-semibold mt-2 {{ $cashDrawer && $cashDrawer->is_connected ? 'text-green-600' : 'text-gray-500' }}">
                        @if($cashDrawer)
                            @if($cashDrawer->is_connected)
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>{{ __('pos.connected') }}
                            @else
                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>{{ __('pos.disconnected') }}
                            @endif
                        @else
                            <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>{{ __('pos.not_configured') }}
                        @endif
                    </p>
                </div>
                <svg class="w-12 h-12 text-green-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('pos.quick_actions') ?? 'Quick Actions' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
            <a href="{{ route('pos.billing') }}" class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ __('pos.billing') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('pos.new_transaction') ?? 'Create new transaction' }}</p>
                </div>
            </a>

            <a href="{{ route('pos.history') }}" class="flex items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ __('pos.transaction_history') ?? 'Transaction History' }}</h3>
                    <p class="text-sm text-gray-600">{{ __('pos.view_transactions') ?? 'View past transactions' }}</p>
                </div>
            </a>

            <a href="#" class="flex items-center gap-3 p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ __('pos.sales_summary') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('messages.view_reports') ?? 'View sales reports' }}</p>
                </div>
            </a>
        </div>
    </div>

    <!-- System Version Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('pos.system_version') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded">
                <div>
                    <p class="text-sm text-gray-600">{{ __('messages.current_version') ?? 'Current Version' }}</p>
                    <p class="font-semibold text-gray-900 mt-1">
                        @if($systemVersion?->version === 'enterprise')
                            {{ __('pos.enterprise_version') }}
                        @elseif($systemVersion?->version === 'pro')
                            {{ __('pos.pro_version') }}
                        @else
                            {{ __('pos.basic_version') }}
                        @endif
                    </p>
                </div>
                <span class="text-2xl">
                    @if($systemVersion?->version === 'enterprise')
                        💎
                    @elseif($systemVersion?->version === 'pro')
                        ⭐
                    @else
                        📦
                    @endif
                </span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded">
                <div>
                    <p class="text-sm text-gray-600">{{ __('messages.features') ?? 'Features Enabled' }}</p>
                    <ul class="text-sm text-gray-900 mt-1 space-y-1">
                        <li>{{ $systemVersion?->barcode_scanner_enabled ? '✓' : '✗' }} {{ __('pos.barcode_scanner') }}</li>
                        <li>{{ $systemVersion?->thermal_printer_enabled ? '✓' : '✗' }} {{ __('pos.thermal_printer') }}</li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded">
                <div>
                    <p class="text-sm text-gray-600">{{ __('messages.last_update') ?? 'Last Updated' }}</p>
                    <p class="font-semibold text-gray-900 mt-1">
                        @if($systemVersion?->upgraded_at)
                            {{ $systemVersion->upgraded_at->format('d M Y') }}
                        @else
                            {{ __('messages.not_updated') ?? 'Never' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

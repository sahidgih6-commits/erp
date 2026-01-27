@extends('layouts.app')

@section('title', __('reports.title'))

@section('content')
<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('reports.sales_report') }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('manager.reports') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="date_from" class="block text-xs sm:text-sm font-bold text-gray-700 mb-2">{{ __('reports.start_date') }}</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
            </div>
            <div>
                <label for="date_to" class="block text-xs sm:text-sm font-bold text-gray-700 mb-2">{{ __('reports.end_date') }}</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base">
                    {{ __('reports.search') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    @if(auth()->user()->isDueSystemEnabled())
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.total_sales') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($totalSales, 2)) }}</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.total_profit') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($totalProfit, 2)) }}</div>
            <div class="text-xs opacity-75 mt-1">({{ __('reports.with_sales') }})</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.cash_received') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($totalPaid, 2)) }}</div>
            <div class="text-xs opacity-75 mt-1">({{ __('reports.received_amount') }})</div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.total_due') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($totalDue, 2)) }}</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.due_collection') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($dueCollection, 2)) }}</div>
            <div class="text-xs opacity-75 mt-1">({{ __('reports.during_period') }})</div>
        </div>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.total_sales') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($totalSales, 2)) }}</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">{{ __('reports.total_profit') }}</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ bn_number(number_format($totalProfit, 2)) }}</div>
            <div class="text-xs opacity-75 mt-1">({{ __('reports.with_sales') }})</div>
        </div>
    </div>
    @endif

    <!-- Due Customers Section -->
    @if($dueCustomers->count() > 0 && auth()->user()->isDueSystemEnabled())
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ __('reports.due_customers_title') }}</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('reports.due_customers_subtitle') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.date') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.customer') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">{{ __('reports.phone') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.product') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.due') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($dueCustomers as $customer)
                    <tr>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                            {{ $customer->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                            {{ $customer->customer_name ?? '-' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                            {{ $customer->customer_phone ?? '-' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ $customer->product->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-red-600 font-bold">
                            ৳{{ bn_number(number_format($customer->due_amount, 2)) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ __('reports.sales_details_title') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.date') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.voucher') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.product') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">{{ __('reports.customer') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">{{ __('reports.phone') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('reports.salesman') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.quantity') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.total_amount') }}</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('reports.profit') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ $sale->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                            <a href="{{ route('voucher.print', $sale->id) }}" target="_blank" 
                               class="font-mono text-blue-600 hover:text-blue-800 hover:underline font-semibold">
                                🧾 {{ $sale->voucher_number ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                            {{ $sale->product->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                            {{ $sale->customer_name ?? '-' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 hidden sm:table-cell">
                            {{ $sale->customer_phone ?? '-' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden lg:table-cell">
                            {{ $sale->user->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ bn_number($sale->quantity) }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 font-semibold">
                            ৳{{ bn_number(number_format($sale->total_amount, 2)) }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-green-600 font-semibold hidden md:table-cell">
                            ৳{{ bn_number(number_format($sale->profit, 2)) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500 text-sm">
                            {{ __('reports.no_sales_in_period') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

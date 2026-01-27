@extends('layouts.app')

@section('title', __('reports.title'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('reports.sales_report') }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('superadmin.reports') }}" class="flex gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('reports.from') }}</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="mt-1 block border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('reports.to') }}</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="mt-1 block border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('reports.search') }}
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">{{ __('reports.total_sales') }}</div>
            <div class="text-3xl font-bold text-green-600">৳{{ bn_number(number_format($totalSales, 2)) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">{{ __('reports.total_profit') }}</div>
            <div class="text-3xl font-bold text-blue-600">৳{{ bn_number(number_format($totalProfit, 2)) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">{{ __('reports.total_quantity') }}</div>
            <div class="text-3xl font-bold text-gray-900">{{ bn_number($totalQuantity) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">{{ __('reports.stock_value') }}</div>
            <div class="text-3xl font-bold text-purple-600">৳{{ bn_number(number_format($totalStockValue, 2)) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.product') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.salesman') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.quantity') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.total_amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('reports.profit') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sales as $sale)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ bn_number($sale->quantity) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ bn_number(number_format($sale->total_amount, 2)) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">৳{{ bn_number(number_format($sale->profit, 2)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

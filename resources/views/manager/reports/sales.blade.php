@extends('layouts.app')

@section('title', 'বিক্রয় রিপোর্ট')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">বিক্রয় রিপোর্ট</h1>
        <a href="{{ route($routePrefix . '.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            রিপোর্ট মেনুতে ফিরুন
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 text-sm font-bold mb-2">শুরুর তারিখ</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 text-sm font-bold mb-2">শেষ তারিখ</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                ফিল্টার করুন
            </button>
            <a href="{{ route($routePrefix . '.reports.sales') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                রিসেট
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট বিক্রয়</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">৳{{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট লেনদেন</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalTransactions }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">গড় বিক্রয়</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">৳{{ number_format($averageSale, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট বকেয়া</p>
            <p class="text-3xl font-bold text-red-600 mt-2">৳{{ number_format($totalDue, 2) }}</p>
        </div>
    </div>

    <!-- Sales by Payment Method -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">পেমেন্ট মেথড অনুযায়ী বিক্রয়</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পেমেন্ট মেথড</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">লেনদেন</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট বিক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">শতাংশ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($salesByPaymentMethod as $method)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $method->payment_method ?? 'Cash' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $method->count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($method->total, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($method->total / $totalSales) * 100 }}%"></div>
                                </div>
                                <span>{{ number_format(($method->total / $totalSales) * 100, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">কোনো বিক্রয় রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">সর্বাধিক বিক্রিত পণ্য</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বিক্রিত পরিমাণ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট বিক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">গড় মূল্য</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topProducts as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $product->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->total_quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ number_format($product->total_sales, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($product->total_sales / $product->total_quantity, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">কোনো পণ্য রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

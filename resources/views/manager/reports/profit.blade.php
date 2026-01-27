@extends('layouts.app')

@section('title', 'মুনাফা রিপোর্ট')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">মুনাফা রিপোর্ট</h1>
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
        </form>
    </div>

    <!-- Profit Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট বিক্রয়</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">৳{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট খরচ</p>
            <p class="text-3xl font-bold text-red-600 mt-2">৳{{ number_format($totalCost, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট মুনাফা</p>
            <p class="text-3xl font-bold text-green-600 mt-2">৳{{ number_format($totalProfit, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মুনাফা মার্জিন</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($profitMargin, 1) }}%</p>
        </div>
    </div>

    <!-- Profit by Category -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">ক্যাটাগরি অনুযায়ী মুনাফা</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্যাটাগরি</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বিক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">খরচ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মুনাফা</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মার্জিন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($profitByCategory as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $category->name ?? 'Uncategorized' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($category->total_revenue, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">৳{{ number_format($category->total_cost, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ number_format($category->total_profit, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $category->margin >= 30 ? 'bg-green-100 text-green-800' : ($category->margin >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($category->margin, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">কোনো রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Most Profitable Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">সর্বাধিক লাভজনক পণ্য</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বিক্রিত</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বিক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">খরচ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মুনাফা</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মার্জিন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topProfitableProducts as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->quantity_sold }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($product->total_revenue, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">৳{{ number_format($product->total_cost, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold">৳{{ number_format($product->total_profit, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $product->profit_margin >= 30 ? 'bg-green-100 text-green-800' : ($product->profit_margin >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($product->profit_margin, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">কোনো পণ্য রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

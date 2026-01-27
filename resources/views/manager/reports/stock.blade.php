@extends('layouts.app')

@section('title', 'স্টক রিপোর্ট')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">স্টক রিপোর্ট</h1>
        <a href="{{ route($routePrefix . '.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            রিপোর্ট মেনুতে ফিরুন
        </a>
    </div>

    <!-- Stock Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট পণ্য</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট স্টক ভ্যালু</p>
            <p class="text-3xl font-bold text-green-600 mt-2">৳{{ number_format($totalStockValue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">লো স্টক আইটেম</p>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $lowStockCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">আউট অফ স্টক</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $outOfStockCount }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 text-sm font-bold mb-2">ক্যাটাগরি</label>
                <select name="category_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="">সব ক্যাটাগরি</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 text-sm font-bold mb-2">স্টক স্ট্যাটাস</label>
                <select name="stock_status" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="">সব পণ্য</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>লো স্টক</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>আউট অফ স্টক</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                ফিল্টার করুন
            </button>
            <a href="{{ route($routePrefix . '.reports.stock') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                রিসেট
            </a>
        </form>
    </div>

    <!-- Stock Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">পণ্যের স্টক তালিকা</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্যাটাগরি</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বর্তমান স্টক</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্রয় মূল্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বিক্রয় মূল্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">স্টক ভ্যালু</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="{{ $product->stock <= 0 ? 'bg-red-50' : ($product->stock < $product->min_stock_level ? 'bg-yellow-50' : '') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->stock }} {{ $product->unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($product->purchase_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ number_format($product->stock * $product->purchase_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->stock <= 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">স্টক শেষ</span>
                            @elseif($product->stock < $product->min_stock_level)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">লো স্টক</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">যথেষ্ট</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">কোনো পণ্য রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

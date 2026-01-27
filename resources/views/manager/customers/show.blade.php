@extends('layouts.app')

@section('title', 'গ্রাহক বিস্তারিত')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">গ্রাহক বিস্তারিত</h1>
        <div class="flex gap-2">
            <a href="{{ route($routePrefix . '.customers.edit', $customer) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                সম্পাদনা
            </a>
            <a href="{{ route($routePrefix . '.customers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                তালিকায় ফিরুন
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Customer Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">গ্রাহকের তথ্য</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">নাম</p>
                    <p class="font-semibold">{{ $customer->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">ফোন</p>
                    <p class="font-semibold">{{ $customer->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">ইমেইল</p>
                    <p class="font-semibold">{{ $customer->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">ঠিকানা</p>
                    <p class="font-semibold">{{ $customer->address ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">স্ট্যাটাস</p>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $customer->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">আর্থিক সারসংক্ষেপ</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">ক্রেডিট লিমিট</p>
                    <p class="font-semibold text-blue-600">৳{{ number_format($customer->credit_limit, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">বকেয়া</p>
                    <p class="font-semibold text-red-600">৳{{ number_format($customer->total_due, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">মোট ক্রয়</p>
                    <p class="font-semibold text-green-600">৳{{ number_format($customer->total_purchase, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">লয়ালটি পয়েন্ট</p>
                    <p class="font-semibold text-purple-600">{{ $customer->loyalty_points }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">পরিসংখ্যান</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">মোট বিক্রয়</p>
                    <p class="font-semibold">{{ $customer->sales()->count() }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">শেষ ক্রয়</p>
                    <p class="font-semibold">{{ $customer->sales()->latest()->first()?->created_at->format('d M Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">যোগদানের তারিখ</p>
                    <p class="font-semibold">{{ $customer->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">বিক্রয় ইতিহাস</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">তারিখ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ইনভয়েস</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">প্রদত্ত</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বকেয়া</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পেমেন্ট মেথড</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customer->sales()->latest()->get() as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ $sale->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($sale->total, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ number_format($sale->paid, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">৳{{ number_format($sale->due, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $sale->payment_method ?? 'Cash' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">কোনো বিক্রয় রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

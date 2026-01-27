@extends('layouts.app')

@section('title', 'গ্রাহক রিপোর্ট')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">গ্রাহক রিপোর্ট</h1>
        <a href="{{ route($routePrefix . '.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            রিপোর্ট মেনুতে ফিরুন
        </a>
    </div>

    <!-- Customer Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট গ্রাহক</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">সক্রিয় গ্রাহক</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeCustomers }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট বকেয়া</p>
            <p class="text-3xl font-bold text-red-600 mt-2">৳{{ number_format($totalDue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট লয়ালটি পয়েন্ট</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($totalLoyaltyPoints) }}</p>
        </div>
    </div>

    <!-- Top Customers by Purchase -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">শীর্ষ গ্রাহক (ক্রয় অনুযায়ী)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">গ্রাহক</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ফোন</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট ক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বকেয়া</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">লয়ালটি পয়েন্ট</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট লেনদেন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCustomers as $customer)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route($routePrefix . '.customers.show', $customer) }}" class="text-blue-600 hover:underline">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">৳{{ number_format($customer->total_purchase, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">৳{{ number_format($customer->total_due, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">{{ $customer->loyalty_points }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $customer->sales_count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">কোনো গ্রাহক রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Customers with Outstanding Due -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">বকেয়া সহ গ্রাহক</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">গ্রাহক</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ফোন</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্রেডিট লিমিট</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট বকেয়া</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">শেষ ক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customersWithDue as $customer)
                    <tr class="{{ $customer->total_due > $customer->credit_limit ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route($routePrefix . '.customers.show', $customer) }}" class="text-blue-600 hover:underline">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($customer->credit_limit, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-semibold {{ $customer->total_due > $customer->credit_limit ? 'text-red-600' : 'text-orange-600' }}">
                                ৳{{ number_format($customer->total_due, 2) }}
                            </span>
                            @if($customer->total_due > $customer->credit_limit)
                                <span class="ml-2 px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">লিমিট ছাড়িয়েছে</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $customer->sales()->latest()->first()?->created_at->format('d M Y') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route($routePrefix . '.customers.show', $customer) }}" class="text-blue-600 hover:underline">
                                বিস্তারিত
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">বকেয়া সহ কোনো গ্রাহক নেই</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $customersWithDue->links() }}
        </div>
    </div>
</div>
@endsection

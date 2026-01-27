@extends('layouts.app')

@section('title', 'গ্রাহক ব্যবস্থাপনা')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-3xl font-bold text-gray-900">গ্রাহক ব্যবস্থাপনা</h1>
        <a href="{{ route($routePrefix . '.customers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            ➕ নতুন গ্রাহক
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" placeholder="নাম, ফোন বা ইমেইল খুঁজুন..." value="{{ request('search') }}" class="shadow border rounded py-2 px-3 text-gray-700">
            <select name="status" class="shadow border rounded py-2 px-3 text-gray-700">
                <option value="">সব স্ট্যাটাস</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>সক্রিয়</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয়</option>
            </select>
            <label class="flex items-center">
                <input type="checkbox" name="with_due" value="1" {{ request('with_due') ? 'checked' : '' }} class="mr-2">
                <span>শুধু বকেয়া আছে এমন</span>
            </label>
            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                🔍 খুঁজুন
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">নাম</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ফোন</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্রেডিট লিমিট</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বর্তমান বকেয়া</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট কেনাকাটা</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পয়েন্ট</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">স্ট্যাটাস</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                        @if($customer->email)
                            <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->phone ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳{{ number_format($customer->credit_limit, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($customer->current_due > 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                ৳{{ number_format($customer->current_due, 2) }}
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳{{ number_format($customer->total_purchase, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ $customer->loyalty_points }} ⭐
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($customer->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                সক্রিয়
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                নিষ্ক্রিয়
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route($routePrefix . '.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900">দেখুন</a>
                        <a href="{{ route($routePrefix . '.customers.edit', $customer) }}" class="text-indigo-600 hover:text-indigo-900">সম্পাদনা</a>
                        <form action="{{ route($routePrefix . '.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">মুছুন</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        কোন গ্রাহক নেই। <a href="{{ route($routePrefix . '.customers.create') }}" class="text-blue-600">নতুন তৈরি করুন</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection

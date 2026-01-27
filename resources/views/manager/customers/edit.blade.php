@extends('layouts.app')

@section('title', 'গ্রাহক সম্পাদনা')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">গ্রাহক সম্পাদনা</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route($routePrefix . '.customers.update', $customer) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">গ্রাহকের নাম *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">ফোন নম্বর</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">ইমেইল</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <div class="mb-4">
                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">ঠিকানা</label>
                <textarea name="address" id="address" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="credit_limit" class="block text-gray-700 text-sm font-bold mb-2">ক্রেডিট লিমিট (৳)</label>
                <input type="number" step="0.01" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" min="0">
            </div>

            <div class="mb-4">
                <label for="is_active" class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="mr-2">
                    <span class="text-gray-700 text-sm font-bold">সক্রিয়</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route($routePrefix . '.customers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    আপডেট করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

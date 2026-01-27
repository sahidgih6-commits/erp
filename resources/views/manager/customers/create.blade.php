@extends('layouts.app')

@section('title', 'নতুন গ্রাহক')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">নতুন গ্রাহক যোগ করুন</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route($routePrefix . '.customers.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">গ্রাহকের নাম *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">ফোন নম্বর</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">ইমেইল</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <div class="mb-4">
                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">ঠিকানা</label>
                <textarea name="address" id="address" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('address') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="credit_limit" class="block text-gray-700 text-sm font-bold mb-2">ক্রেডিট লিমিট (৳)</label>
                <input type="number" step="0.01" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', 0) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" min="0">
                <p class="text-xs text-gray-500 mt-1">💡 যদি গ্রাহককে বাকিতে পণ্য দিতে চান তাহলে ক্রেডিট লিমিট সেট করুন</p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route($routePrefix . '.customers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

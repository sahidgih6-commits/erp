@extends('layouts.app')

@section('title', 'Open Cash Drawer')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">নতুন ক্যাশ ড্রয়ার সেশন শুরু করুন</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('pos.cash-drawer.store') }}">
            @csrf

            <div class="mb-4">
                <label for="opening_balance" class="block text-gray-700 text-sm font-bold mb-2">শুরুর ব্যালেন্স (৳) *</label>
                <input type="number" step="0.01" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', 0) }}" 
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('opening_balance') border-red-500 @enderror" required min="0">
                @error('opening_balance')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-xs mt-1">ক্যাশ ড্রয়ারে শুরুতে কত টাকা আছে তা লিখুন</p>
            </div>

            <div class="mb-4">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">নোট (ঐচ্ছিক)</label>
                <textarea name="notes" id="notes" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('notes') }}</textarea>
                <p class="text-gray-600 text-xs mt-1">সেশন সম্পর্কে কোনো বিশেষ নোট</p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                <p class="text-blue-900 font-semibold">গুরুত্বপূর্ণ:</p>
                <ul class="list-disc list-inside text-blue-800 text-sm mt-2">
                    <li>সেশন শুরু করার আগে ক্যাশ ড্রয়ারে টাকা গণনা করুন</li>
                    <li>একই সময়ে শুধুমাত্র একটি সেশন সক্রিয় থাকতে পারে</li>
                    <li>শিফট শেষে সেশন বন্ধ করতে ভুলবেন না</li>
                </ul>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('pos.cash-drawer.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    সেশন শুরু করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

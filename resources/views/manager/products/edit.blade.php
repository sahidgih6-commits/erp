@extends('layouts.app')

@section('title', 'পণ্য সম্পাদনা')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">পণ্য সম্পাদনা</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route($routePrefix . '.products.update', $product) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">পণ্যের নাম</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="sku" class="block text-gray-700 text-sm font-bold mb-2">পণ্য কোড (SKU)</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('sku') border-red-500 @enderror" required>
                @error('sku')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="barcode" class="block text-gray-700 text-sm font-bold mb-2">বারকোড (খালি রাখলে স্বয়ংক্রিয়ভাবে তৈরি হবে)</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('barcode') border-red-500 @enderror" placeholder="স্বয়ংক্রিয়">
                @error('barcode')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-600 mt-1">
                    💡 খালি রেখে দিলে সিস্টেম নিজে থেকে একটি ইউনিক বারকোড তৈরি করবে
                </p>
            </div>

            <div class="mb-4">
                <label for="purchase_price" class="block text-gray-700 text-sm font-bold mb-2">ক্রয় মূল্য</label>
                <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('purchase_price') border-red-500 @enderror" required>
                @error('purchase_price')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="sell_price" class="block text-gray-700 text-sm font-bold mb-2">বিক্রয় মূল্য</label>
                <input type="number" step="0.01" name="sell_price" id="sell_price" value="{{ old('sell_price', $product->sell_price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('sell_price') border-red-500 @enderror" required>
                @error('sell_price')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-gray-700 text-sm font-bold mb-2">বর্তমান স্টক</label>
                <p class="text-2xl font-bold text-blue-600">{{ bn_number($product->current_stock) }} টি</p>
            </div>

            @if(auth()->user()->hasRole('owner'))
            <div class="mb-6 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <label class="block text-gray-700 text-sm font-bold mb-3">স্টক সমন্বয় (ঐচ্ছিক)</label>
                <p class="text-sm text-gray-600 mb-3">ভুলবশত বেশি/কম স্টক যোগ হলে এখানে সংশোধন করুন (বিক্রয় হিসেবে গণনা হবে না)</p>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="adjust_type" class="block text-gray-700 text-xs font-semibold mb-1">সমন্বয় ধরন</label>
                        <select name="adjust_type" id="adjust_type" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="">কোনো সমন্বয় নেই</option>
                            <option value="increase">বৃদ্ধি করুন (+)</option>
                            <option value="decrease">হ্রাস করুন (-)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="adjust_quantity" class="block text-gray-700 text-xs font-semibold mb-1">পরিমাণ</label>
                        <input type="number" step="1" min="0" name="adjust_quantity" id="adjust_quantity" value="{{ old('adjust_quantity', 0) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('adjust_quantity') border-red-500 @enderror" placeholder="০">
                    </div>
                </div>
                
                @error('adjust_quantity')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route($routePrefix . '.products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    পণ্য আপডেট করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'নতুন পণ্য তৈরি')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">নতুন পণ্য তৈরি করুন</h1>
        <p class="text-sm text-gray-600 mt-2">পণ্য তৈরির পর স্টক পেজ থেকে স্টক যোগ করুন</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route($routePrefix . '.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">পণ্যের নাম *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">ক্যাটাগরি</label>
                    <select name="category_id" id="category_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('category_id') border-red-500 @enderror">
                        <option value="">ক্যাটাগরি নির্বাচন করুন</option>
                        @foreach(\App\Models\Category::where('business_id', auth()->user()->business_id)->active()->ordered()->get() as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon ?? '' }} {{ $cat->name_bn ?? $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="sku" class="block text-gray-700 text-sm font-bold mb-2">পণ্য কোড (SKU) *</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('sku') border-red-500 @enderror" required>
                    @error('sku')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                <label for="barcode" class="block text-gray-700 text-sm font-bold mb-2">
                    বারকোড 
                    <span class="text-xs text-gray-500 font-normal">(খালি রাখলে স্বয়ংক্রিয়ভাবে তৈরি হবে)</span>
                </label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('barcode') border-red-500 @enderror"
                       placeholder="স্বয়ংক্রিয়">
                @error('barcode')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">💡 খালি রেখে দিলে সিস্টেম নিজে থেকে একটি ইউনিক বারকোড তৈরি করবে</p>
            </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="purchase_price" class="block text-gray-700 text-sm font-bold mb-2">ক্রয় মূল্য *</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('purchase_price') border-red-500 @enderror" required>
                    @error('purchase_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sell_price" class="block text-gray-700 text-sm font-bold mb-2">বিক্রয় মূল্য *</label>
                    <input type="number" step="0.01" name="sell_price" id="sell_price" value="{{ old('sell_price') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('sell_price') border-red-500 @enderror" required>
                    @error('sell_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-gray-700 text-sm font-bold mb-2">একক</label>
                    <select name="unit" id="unit" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('unit') border-red-500 @enderror">
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>পিস (Pcs)</option>
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>কেজি (Kg)</option>
                        <option value="ltr" {{ old('unit') == 'ltr' ? 'selected' : '' }}>লিটার (Ltr)</option>
                        <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>বক্স (Box)</option>
                        <option value="dozen" {{ old('unit') == 'dozen' ? 'selected' : '' }}>ডজন (Dozen)</option>
                    </select>
                    @error('unit')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="min_stock_level" class="block text-gray-700 text-sm font-bold mb-2">সর্বনিম্ন স্টক সতর্কতা</label>
                    <input type="number" step="1" name="min_stock_level" id="min_stock_level" value="{{ old('min_stock_level', 10) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('min_stock_level') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">স্টক এই পরিমাণের নিচে গেলে সতর্কতা দেখাবে</p>
                    @error('min_stock_level')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">পণ্যের ছবি</label>
                    <input type="file" name="image" id="image" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('image') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">সর্বোচ্চ 2MB, JPG/PNG ফরম্যাট</p>
                    @error('image')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                @error('sell_price')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route($routePrefix . '.products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    পণ্য তৈরি করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

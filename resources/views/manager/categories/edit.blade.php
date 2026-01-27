@extends('layouts.app')

@section('title', 'ক্যাটাগরি সম্পাদনা')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">ক্যাটাগরি সম্পাদনা</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route($routePrefix . '.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">নাম (ইংরেজি) *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name_bn" class="block text-gray-700 text-sm font-bold mb-2">নাম (বাংলা)</label>
                    <input type="text" name="name_bn" id="name_bn" value="{{ old('name_bn', $category->name_bn) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">বিবরণ</label>
                <textarea name="description" id="description" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="icon" class="block text-gray-700 text-sm font-bold mb-2">আইকন (ইমোজি)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" placeholder="🍔" class="shadow border rounded w-full py-2 px-3 text-gray-700 text-2xl" maxlength="10">
                </div>

                <div>
                    <label for="sort_order" class="block text-gray-700 text-sm font-bold mb-2">ক্রম</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <div class="mb-4">
                <label for="is_active" class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="mr-2">
                    <span class="text-gray-700 text-sm font-bold">সক্রিয়</span>
                </label>
            </div>

            <div class="mb-6">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">ছবি (ঐচ্ছিক)</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-20 w-20 object-cover rounded">
                    </div>
                @endif
                <input type="file" name="image" id="image" accept="image/*" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                <p class="text-xs text-gray-500 mt-1">সর্বোচ্চ 2MB</p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route($routePrefix . '.categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

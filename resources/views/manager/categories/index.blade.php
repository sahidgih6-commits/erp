@extends('layouts.app')

@section('title', 'ক্যাটাগরি ব্যবস্থাপনা')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">ক্যাটাগরি ব্যবস্থাপনা</h1>
        <a href="{{ route($routePrefix . '.categories.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            ➕ নতুন ক্যাটাগরি
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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">আইকন</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">নাম</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য সংখ্যা</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্রম</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">স্ট্যাটাস</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-2xl">{{ $category->icon ?? '📦' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $category->name_bn ?? $category->name }}</div>
                        <div class="text-sm text-gray-500">{{ $category->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $category->products_count }} পণ্য
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->sort_order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($category->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                সক্রিয়
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                নিষ্ক্রিয়
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route($routePrefix . '.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">সম্পাদনা</a>
                        <form action="{{ route($routePrefix . '.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">মুছুন</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        কোন ক্যাটাগরি নেই। <a href="{{ route($routePrefix . '.categories.create') }}" class="text-blue-600">নতুন তৈরি করুন</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection

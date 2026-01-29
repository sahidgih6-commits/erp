@extends('layouts.app')

@section('title', 'সেলসম্যান পরিচালনা')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">সেলসম্যান পরিচালনা</h1>
        <a href="{{ route('owner.salesmen.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            নতুন সেলসম্যান যোগ করুন
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">আইডি</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">নাম</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ইমেইল</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ফোন</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">তৈরি হয়েছে</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">কর্ম</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($salesmen as $salesman)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $salesman->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $salesman->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $salesman->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $salesman->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $salesman->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('owner.salesmen.edit', $salesman) }}" class="text-blue-600 hover:text-blue-900 mr-3">সম্পাদনা</a>
                        <form action="{{ route('owner.salesmen.destroy', $salesman) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('আপনি কি নিশ্চিত?')">মুছুন</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">কোনো সেলসম্যান পাওয়া যায়নি</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

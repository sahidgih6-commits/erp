@extends('layouts.app')

@section('title', 'Cash Drawer Sessions')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">ক্যাশ ড্রয়ার সেশন</h1>
        @if(!$activeSession)
        <a href="{{ route('pos.cash-drawer.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            নতুন সেশন শুরু করুন
        </a>
        @else
        <a href="{{ route('pos.cash-drawer.close', $activeSession) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            সেশন বন্ধ করুন
        </a>
        @endif
    </div>

    @if($activeSession)
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="font-bold text-blue-900">Active Session</p>
                <p class="text-blue-700">Started: {{ $activeSession->opened_at->format('d M Y H:i A') }}</p>
                <p class="text-blue-700">Opening Balance: ৳{{ number_format($activeSession->opening_balance, 2) }}</p>
            </div>
            <a href="{{ route('pos.cash-drawer.show', $activeSession) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                View Details
            </a>
        </div>
    </div>
    @endif

    <!-- Session History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">সেশন ইতিহাস</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ক্যাশিয়ার</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">শুরুর সময়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">শেষ সময়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">শুরুর ব্যালেন্স</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">শেষ ব্যালেন্স</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট বিক্রয়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">স্ট্যাটাস</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions as $session)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $session->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $session->opened_at->format('d M Y H:i A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $session->closed_at ? $session->closed_at->format('d M Y H:i A') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($session->opening_balance, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $session->closing_balance ? '৳' . number_format($session->closing_balance, 2) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ number_format($session->total_sales, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($session->is_open)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">সক্রিয়</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">বন্ধ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('pos.cash-drawer.show', $session) }}" class="text-blue-600 hover:underline">বিস্তারিত</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">কোনো সেশন রেকর্ড পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection

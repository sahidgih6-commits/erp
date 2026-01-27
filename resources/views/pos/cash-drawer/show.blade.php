@extends('layouts.app')

@section('title', 'Cash Drawer Session Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">সেশন বিস্তারিত</h1>
        <div class="flex gap-2">
            @if($session->is_open)
            <a href="{{ route('pos.cash-drawer.close', $session) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                সেশন বন্ধ করুন
            </a>
            @endif
            <a href="{{ route('pos.cash-drawer.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                তালিকায় ফিরুন
            </a>
        </div>
    </div>

    <!-- Session Info -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">শুরুর ব্যালেন্স</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">৳{{ number_format($session->opening_balance, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">মোট বিক্রয়</p>
            <p class="text-3xl font-bold text-green-600 mt-2">৳{{ number_format($session->total_sales, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">আশা করা ব্যালেন্স</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">৳{{ number_format($session->expected_balance, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">{{ $session->is_open ? 'বর্তমান স্ট্যাটাস' : 'শেষ ব্যালেন্স' }}</p>
            @if($session->is_open)
                <span class="inline-block mt-2 px-4 py-2 text-lg font-bold rounded bg-green-100 text-green-800">সক্রিয়</span>
            @else
                <p class="text-3xl font-bold mt-2 {{ abs($session->difference) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                    ৳{{ number_format($session->closing_balance, 2) }}
                </p>
            @endif
        </div>
    </div>

    <!-- Session Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">সেশনের তথ্য</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">ক্যাশিয়ার</p>
                    <p class="font-semibold">{{ $session->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">শুরুর সময়</p>
                    <p class="font-semibold">{{ $session->opened_at->format('d M Y H:i A') }}</p>
                </div>
                @if(!$session->is_open)
                <div>
                    <p class="text-gray-600 text-sm">শেষ সময়</p>
                    <p class="font-semibold">{{ $session->closed_at->format('d M Y H:i A') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">সময়কাল</p>
                    <p class="font-semibold">{{ $session->opened_at->diff($session->closed_at)->format('%h ঘন্টা %i মিনিট') }}</p>
                </div>
                @else
                <div>
                    <p class="text-gray-600 text-sm">সময়কাল</p>
                    <p class="font-semibold">{{ $session->opened_at->diffForHumans() }}</p>
                </div>
                @endif
            </div>
        </div>

        @if(!$session->is_open)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">ক্লোজিং তথ্য</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">শেষ ব্যালেন্স</p>
                    <p class="font-semibold">৳{{ number_format($session->closing_balance, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">অমিল</p>
                    <p class="font-semibold {{ abs($session->difference) < 0.01 ? 'text-green-600' : ($session->difference > 0 ? 'text-green-600' : 'text-red-600') }}">
                        @if(abs($session->difference) < 0.01)
                            ✓ কোনো অমিল নেই
                        @elseif($session->difference > 0)
                            +৳{{ number_format($session->difference, 2) }} (অতিরিক্ত)
                        @else
                            -৳{{ number_format(abs($session->difference), 2) }} (কম)
                        @endif
                    </p>
                </div>
                @if($session->closing_notes)
                <div>
                    <p class="text-gray-600 text-sm">নোট</p>
                    <p class="font-semibold">{{ $session->closing_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sales during session -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">এই সেশনে বিক্রয়</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">সময়</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ইনভয়েস</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পরিমাণ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">মোট</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পেমেন্ট</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($session->sales as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->created_at->format('H:i A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ $sale->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">৳{{ number_format($sale->total, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->payment_method ?? 'Cash' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">এই সেশনে কোনো বিক্রয় হয়নি</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

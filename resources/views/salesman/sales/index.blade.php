@extends('layouts.app')

@section('title', 'আমার বিক্রয়')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">আমার বিক্রয়</h1>
        <a href="{{ route('salesman.sales.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base">
            নতুন বিক্রয় তৈরি করুন
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">তারিখ</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">ভাউচার</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">পণ্য</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 hidden sm:table-cell">কাস্টমার</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 hidden sm:table-cell">ফোন</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">পরিমাণ</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">মোট</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 hidden md:table-cell">পরিশোধিত</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 hidden md:table-cell">বকেয়া</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 hidden lg:table-cell">লাভ</th>
                    <th class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-500">ছবি</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sales as $sale)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                        <a href="{{ route('voucher.print', $sale->id) }}" target="_blank" 
                           class="font-mono text-blue-600 hover:text-blue-800 hover:underline font-semibold">
                            🧾 {{ $sale->voucher_number }}
                        </a>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ $sale->product->name }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">{{ $sale->customer_name ?? '-' }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 hidden sm:table-cell">{{ $sale->customer_phone ?? '-' }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ $sale->quantity }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-green-600 font-semibold">৳{{ number_format($sale->total_amount, 2) }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-green-600 hidden md:table-cell">৳{{ number_format($sale->paid_amount, 2) }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-bold hidden md:table-cell">
                        <span class="{{ $sale->due_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ৳{{ number_format($sale->due_amount, 2) }}
                        </span>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-blue-600 font-semibold hidden lg:table-cell">৳{{ number_format($sale->profit, 2) }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-center">
                        @if($sale->voucher_image)
                            <button onclick="showVoucherImage('{{ asset('storage/' . $sale->voucher_image) }}', '{{ $sale->voucher_number }}')" 
                                    class="text-blue-600 hover:text-blue-800 text-2xl" title="ভাউচার ছবি দেখুন">
                                📷
                            </button>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-6 py-8 text-center text-gray-500">
                        কোন বিক্রয় নেই
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
</div>

<!-- Voucher Image Modal -->
<div id="voucherImageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4" onclick="closeVoucherImage()">
    <div class="relative max-w-4xl w-full bg-white rounded-lg shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">ভাউচার ছবি</h3>
            <button onclick="closeVoucherImage()" class="text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        </div>
        <div class="p-4">
            <img id="modalImage" src="" alt="Voucher" class="max-w-full h-auto rounded shadow-lg mx-auto">
        </div>
        <div class="p-4 border-t flex justify-end gap-2">
            <a id="downloadLink" href="" download class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                ডাউনলোড করুন
            </a>
            <button onclick="closeVoucherImage()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                বন্ধ করুন
            </button>
        </div>
    </div>
</div>

<script>
function showVoucherImage(imageUrl, voucherNumber) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalTitle').textContent = 'ভাউচার: ' + voucherNumber;
    document.getElementById('downloadLink').href = imageUrl;
    document.getElementById('voucherImageModal').classList.remove('hidden');
}

function closeVoucherImage() {
    document.getElementById('voucherImageModal').classList.add('hidden');
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeVoucherImage();
    }
});
</script>
@endsection

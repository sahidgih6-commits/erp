@extends('layouts.app')

@section('title', 'বকেয়া গ্রাহক তালিকা')

@section('content')
<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">বকেয়া কাস্টমার তালিকা</h1>
        <a href="{{ route('owner.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base">
            ড্যাশবোর্ডে ফিরুন
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="mb-4 sm:mb-6 bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-4 sm:p-6 text-white">
        <div class="text-xs sm:text-sm opacity-90">মোট বকেয়া</div>
        <div class="text-3xl sm:text-4xl font-bold">৳{{ number_format($totalDue, 2) }}</div>
        <div class="text-xs sm:text-sm mt-2">মোট {{ $dueCustomers->count() }} জন গ্রাহকের বকেয়া রয়েছে</div>
    </div>

    <!-- Search Box -->
    <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('owner.due-customers') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="ফোন নম্বর, ভাউচার নম্বর বা কাস্টমার নাম দিয়ে খুঁজুন..."
                       class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 sm:px-6 rounded text-sm sm:text-base whitespace-nowrap">
                খুঁজুন
            </button>
            @if(request('search'))
            <a href="{{ route('owner.due-customers') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base">
                রিসেট
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">কাস্টমার</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">ফোন</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">পণ্য</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">তারিখ</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">বকেয়া</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">পরিশোধের তারিখ</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">স্ট্যাটাস</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ভাউচার</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">একশন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dueCustomers as $sale)
                    <tr class="{{ $sale->expected_clear_date && $sale->expected_clear_date->isPast() ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                            {{ $sale->customer_name ?? 'N/A' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                            {{ $sale->customer_phone ?? 'N/A' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ $sale->product->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden md:table-cell">
                            {{ $sale->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-red-600 font-bold">
                            ৳{{ number_format($sale->due_amount, 2) }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden lg:table-cell">
                            @if($sale->expected_clear_date)
                                {{ $sale->expected_clear_date->format('d/m/Y') }}
                                @if($sale->expected_clear_date->isPast())
                                    <br><span class="text-red-600 text-xs font-semibold">(মেয়াদোত্তীর্ণ)</span>
                                @endif
                            @else
                                <span class="text-gray-400">নির্ধারিত নয়</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm hidden lg:table-cell">
                            @if($sale->payment_status == 'paid')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">পরিশোধিত</span>
                            @elseif($sale->payment_status == 'partial')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">আংশিক</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">অপরিশোধিত</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                            <div class="flex flex-col gap-1 items-start">
                                <a href="{{ route('owner.voucher.print', $sale->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-mono">
                                    {{ $sale->voucher_number ?? 'N/A' }}
                                </a>
                                
                                @if(!empty($sale->voucher_image))
                                    <button type="button" onclick="openImageModal('{{ Storage::url($sale->voucher_image) }}')" class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1 cursor-pointer bg-transparent border-0 p-0 focus:outline-none">
                                        📷 ছবি দেখুন
                                    </button>
                                @endif
                                
                                <form action="{{ route('owner.sales.upload-image') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="voucher_number" value="{{ $sale->voucher_number }}">
                                    <label class="cursor-pointer inline-flex items-center gap-1 text-xs bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded px-2 py-1 text-gray-700 transition-colors">
                                        <span>📤</span> 
                                        <span>আপলোড</span>
                                        <input type="file" name="voucher_image" class="hidden" onchange="this.form.submit()">
                                    </label>
                                </form>

                                @if($sale->profitRealizations->count() > 0)
                                    <div class="text-xs text-gray-500">
                                        @foreach($sale->profitRealizations as $pr)
                                            <a href="{{ route('owner.payment.voucher', $pr->id) }}" target="_blank" class="text-green-600 hover:text-green-900 block">
                                                {{ $pr->payment_voucher_number }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                            <a href="{{ route('owner.payment.record', $sale->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                টাকা নিন
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            কোন বকেয়া নেই
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-90 transition-opacity" aria-hidden="true" onclick="closeImageModal()"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-transparent rounded-lg text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
             <div class="relative">
                <button type="button" class="absolute -top-10 right-0 text-white hover:text-gray-300 focus:outline-none" onclick="closeImageModal()">
                    <span class="sr-only">Close</span>
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img id="modalImage" src="" alt="Voucher Scan" class="w-full h-auto max-h-[85vh] object-contain mx-auto rounded-lg bg-white">
             </div>
        </div>
    </div>
</div>

<script>
    function openImageModal(url) {
        document.getElementById('modalImage').src = url;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling background
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('modalImage').src = '';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
    
    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeImageModal();
        }
    });
</script>

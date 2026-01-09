@extends('layouts.app')

@section('title', 'নতুন বিক্রয়')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem 0.75rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">নতুন বিক্রয় তৈরি করুন</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-4 sm:p-6 lg:p-8">
        @php
            $routePrefix = $baseRoute ?? (auth()->user()?->isOwner() ? 'owner' : (auth()->user()?->isManager() ? 'manager' : 'salesman'));
        @endphp
        <form method="POST" action="{{ route($routePrefix . '.sales.store') }}" id="saleForm">
            @csrf

            <div class="mb-4 sm:mb-6">
                <label for="product_id" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">পণ্য *</label>
                <select name="product_id" id="product_id" class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('product_id') border-red-500 @enderror" required>
                    <option value="">পণ্য নির্বাচন করুন</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-price="{{ $product->sell_price }}" 
                                data-stock="{{ $product->current_stock }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (পণ্য কোড: {{ $product->sku }}) - স্টক: {{ $product->current_stock }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <div>
                    <label for="quantity" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">পরিমাণ *</label>
                    <input type="number" 
                           name="quantity" 
                           id="quantity" 
                           value="{{ old('quantity', 1) }}" 
                           min="1" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('quantity') border-red-500 @enderror" 
                           required
                           onchange="updateTotal()">
                    @error('quantity')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sell_price" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">বিক্রয়মূল্য (৳) *</label>
                    <input type="number" 
                           name="sell_price" 
                           id="sell_price" 
                           value="{{ old('sell_price') }}" 
                           step="0.01"
                           min="0" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('sell_price') border-red-500 @enderror" 
                           required
                           onchange="updateTotal()">
                    <p class="text-xs text-gray-500 mt-1">মূল্য পরিবর্তন করতে পারবেন</p>
                    @error('sell_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-3 sm:p-4 bg-gray-100 rounded flex items-center">
                    <div class="text-base sm:text-lg font-bold text-gray-800">মোট টাকা: <span id="totalAmount" class="text-blue-600">৳০.০০</span></div>
                </div>
            </div>

            <!-- Customer Fields (Always visible for all users) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div>
                    <label for="customer_name" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">
                        কাস্টমার নাম <span id="customer_name_required" class="text-red-500 hidden">*</span>
                    </label>
                    <input type="text" 
                           name="customer_name" 
                           id="customer_name" 
                           value="{{ old('customer_name') }}" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('customer_name') border-red-500 @enderror"
                           placeholder="কাস্টমারের নাম (ঐচ্ছিক)">
                    @error('customer_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_phone" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">
                        কাস্টমার ফোন নম্বর <span id="customer_phone_required" class="text-red-500 hidden">*</span>
                    </label>
                    <input type="text" 
                           name="customer_phone" 
                           id="customer_phone" 
                           value="{{ old('customer_phone') }}" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('customer_phone') border-red-500 @enderror"
                           placeholder="০১৭XXXXXXXXX (ঐচ্ছিক)">
                    @error('customer_phone')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if(auth()->user()->isDueSystemEnabled())
            <!-- বাকিতে Toggle -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_credit" id="is_credit" value="1" class="mr-3 w-5 h-5" onchange="toggleCreditFields()">
                    <span class="text-base sm:text-lg font-bold text-gray-800">বাকিতে বিক্রয়</span>
                </label>
            </div>
            @endif

            @if(auth()->user()->isDueSystemEnabled())
            <!-- Credit Payment Fields (Hidden by default) -->
            <div id="creditFields" class="hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                    <div>
                        <label for="paid_amount" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">পরিশোধিত টাকা *</label>
                        <input type="number" 
                               name="paid_amount" 
                               id="paid_amount" 
                               value="{{ old('paid_amount', 0) }}" 
                               step="0.01"
                               min="0" 
                               class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('paid_amount') border-red-500 @enderror" 
                               onchange="updateDue()">
                        @error('paid_amount')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-3 sm:p-4 bg-yellow-50 rounded border border-yellow-200 flex items-center">
                        <div class="text-base sm:text-lg font-bold text-red-600">বকেয়া: <span id="dueAmount">৳০.০০</span></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="expected_clear_date" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">বকেয়া পরিশোধের তারিখ</label>
                    <input type="date" 
                           name="expected_clear_date" 
                           id="expected_clear_date" 
                           value="{{ old('expected_clear_date') }}" 
                           min="{{ date('Y-m-d') }}"
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('expected_clear_date') border-red-500 @enderror">
                    @error('expected_clear_date')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-center sm:justify-between">
                <a href="{{ route($routePrefix . '.sales.index') }}" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 sm:px-6 rounded text-sm sm:text-base text-center">
                    বাতিল
                </a>
                <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 sm:px-6 rounded text-sm sm:text-base">
                    বিক্রয় তৈরি করুন
                </button>
            </div>
        </form>
    </div>

    @if($products->isEmpty())
        <div class="mt-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p>স্টকে কোন পণ্য নেই। অনুগ্রহ করে আপনার ম্যানেজারের সাথে যোগাযোগ করুন।</p>
        </div>
    @endif
</div>

<script>
const isDueSystemEnabled = {{ auth()->user()->isDueSystemEnabled() ? 'true' : 'false' }};

function toggleCreditFields() {
    const isCreditChecked = document.getElementById('is_credit').checked;
    const creditFields = document.getElementById('creditFields');
    const paidAmount = document.getElementById('paid_amount');
    const customerNameRequired = document.getElementById('customer_name_required');
    const customerPhoneRequired = document.getElementById('customer_phone_required');
    const customerNameInput = document.getElementById('customer_name');
    const customerPhoneInput = document.getElementById('customer_phone');
    
    if (isCreditChecked) {
        creditFields.classList.remove('hidden');
        paidAmount.value = '0';
        
        // Make customer fields required for credit sales
        customerNameRequired.classList.remove('hidden');
        customerPhoneRequired.classList.remove('hidden');
        customerNameInput.setAttribute('required', 'required');
        customerPhoneInput.setAttribute('required', 'required');
        customerNameInput.placeholder = 'কাস্টমারের নাম (আবশ্যক)';
        customerPhoneInput.placeholder = '০১৭XXXXXXXXX (আবশ্যক)';
    } else {
        creditFields.classList.add('hidden');
        const total = parseFloat(document.getElementById('totalAmount').textContent.replace('৳', ''));
        paidAmount.value = total.toFixed(2);
        
        // Make customer fields optional for cash sales
        customerNameRequired.classList.add('hidden');
        customerPhoneRequired.classList.add('hidden');
        customerNameInput.removeAttribute('required');
        customerPhoneInput.removeAttribute('required');
        customerNameInput.placeholder = 'কাস্টমারের নাম (ঐচ্ছিক)';
        customerPhoneInput.placeholder = '০১৭XXXXXXXXX (ঐচ্ছিক)';
    }
    updateDue();
}

function updateTotal() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const sellPriceInput = document.getElementById('sell_price');
    
    if (productSelect.value) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const defaultPrice = parseFloat(selectedOption.dataset.price);
        
        // Auto-fill sell price if empty
        if (!sellPriceInput.value || sellPriceInput.value == 0) {
            sellPriceInput.value = defaultPrice.toFixed(2);
        }
    }
    
    if (productSelect.value && quantityInput.value && sellPriceInput.value) {
        const price = parseFloat(sellPriceInput.value);
        const quantity = parseInt(quantityInput.value);
        const total = price * quantity;
        
        document.getElementById('totalAmount').textContent = '৳' + total.toFixed(2);
        
        // Auto-set paid_amount if not in credit mode or due system disabled
        if (!isDueSystemEnabled) {
            const paidAmountField = document.getElementById('paid_amount');
            if (paidAmountField) {
                paidAmountField.value = total.toFixed(2);
            }
        } else {
            const isCreditChecked = document.getElementById('is_credit') ? document.getElementById('is_credit').checked : false;
            if (!isCreditChecked) {
                const paidAmountField = document.getElementById('paid_amount');
                if (paidAmountField) {
                    paidAmountField.value = total.toFixed(2);
                }
            }
        }
        
        updateDue();
    }
}

function updateDue() {
    if (!isDueSystemEnabled) return;
    
    const totalText = document.getElementById('totalAmount').textContent;
    const total = parseFloat(totalText.replace('৳', ''));
    const paidField = document.getElementById('paid_amount');
    const paid = paidField ? parseFloat(paidField.value) || 0 : total;
    const due = total - paid;
    
    const dueElement = document.getElementById('dueAmount');
    if (dueElement) {
        dueElement.textContent = '৳' + due.toFixed(2);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for product dropdown
    $('#product_id').select2({
        placeholder: 'পণ্য খুঁজুন...',
        allowClear: true,
        language: {
            noResults: function() {
                return 'কোন পণ্য পাওয়া যায়নি';
            },
            searching: function() {
                return 'খুঁজছি...';
            }
        }
    }).on('change', function() {
        updateTotal();
    });
    
    updateTotal();
    
    // If due system is disabled, hide credit toggle
    if (!isDueSystemEnabled) {
        const paidAmount = document.getElementById('paid_amount');
        if (paidAmount) {
            paidAmount.value = '0';
        }
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

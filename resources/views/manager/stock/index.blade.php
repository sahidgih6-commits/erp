@extends('layouts.app')

@section('title', 'স্টক ব্যবস্থাপনা')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">স্টক ব্যবস্থাপনা</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
            <h2 class="text-lg sm:text-xl font-semibold">স্টক যোগ করুন</h2>
            <button type="button" id="toggleProductMode" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base w-full sm:w-auto">
                নতুন পণ্য তৈরি করুন
            </button>
        </div>
        
        <!-- Existing Product Form -->
        @php
            $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        @endphp
        <form method="POST" action="{{ route($routePrefix . '.stock.store') }}" id="existingProductForm">
            @csrf

            <!-- Barcode Scanner Input -->
            <div class="mb-4">
                <label for="barcode_scanner" class="block text-gray-700 text-sm font-bold mb-2">
                    📷 বারকোড স্ক্যান করুন (পণ্য স্বয়ংক্রিয়ভাবে নির্বাচিত হবে)
                </label>
                <input type="text" id="barcode_scanner" 
                       placeholder="এখানে বারকোড স্ক্যান করুন বা টাইপ করুন..."
                       class="shadow border border-blue-300 rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-600 mt-1">
                    💡 স্ক্যানারে বারকোড স্ক্যান করুন অথবা ম্যানুয়ালি নিচে থেকে পণ্য বেছে নিন
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">পণ্য নির্বাচন করুন *</label>
                    <select name="product_id" id="product_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('product_id') border-red-500 @enderror" required>
                        <option value="">পণ্য নির্বাচন করুন</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-current-stock="{{ $product->current_stock }}"
                                    data-purchase-price="{{ $product->purchase_price }}"
                                    data-sell-price="{{ $product->sell_price }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (পণ্য কোড: {{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p id="current_stock_display" class="text-sm text-gray-600 mt-1"></p>
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">পরিমাণ *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('quantity') border-red-500 @enderror" required>
                    @error('quantity')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_price" class="block text-gray-700 text-sm font-bold mb-2">ক্রয়মূল্য (৳) *</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('purchase_price') border-red-500 @enderror" required>
                    @error('purchase_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sell_price" class="block text-gray-700 text-sm font-bold mb-2">বিক্রয়মূল্য (৳) *</label>
                    <input type="number" step="0.01" name="sell_price" id="sell_price" value="{{ old('sell_price') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('sell_price') border-red-500 @enderror" required>
                    @error('sell_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="supplier_name" class="block text-gray-700 text-sm font-bold mb-2">সরবরাহকারীর নাম (ঐচ্ছিক)</label>
                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('supplier_name') border-red-500 @enderror" placeholder="সরবরাহকারীর নাম">
                    @error('supplier_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="supplier_phone" class="block text-gray-700 text-sm font-bold mb-2">সরবরাহকারীর ফোন (ঐচ্ছিক)</label>
                    <input type="text" name="supplier_phone" id="supplier_phone" value="{{ old('supplier_phone') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('supplier_phone') border-red-500 @enderror" placeholder="01XXXXXXXXX">
                    @error('supplier_phone')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    স্টক যোগ করুন
                </button>
            </div>
        </form>

        <!-- New Product Form -->
        <form method="POST" action="{{ route($routePrefix . '.stock.store') }}" id="newProductForm" class="hidden">
            @csrf
            <input type="hidden" name="create_new_product" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="new_product_name" class="block text-gray-700 text-sm font-bold mb-2">পণ্যের নাম *</label>
                    <input type="text" name="new_product_name" id="new_product_name" value="{{ old('new_product_name') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('new_product_name') border-red-500 @enderror">
                    @error('new_product_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_product_sku" class="block text-gray-700 text-sm font-bold mb-2">পণ্য কোড *</label>
                    <input type="text" name="new_product_sku" id="new_product_sku" value="{{ old('new_product_sku') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('new_product_sku') border-red-500 @enderror">
                    @error('new_product_sku')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="new_quantity" class="block text-gray-700 text-sm font-bold mb-2">পরিমাণ *</label>
                    <input type="number" name="quantity" id="new_quantity" value="{{ old('quantity') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_purchase_price" class="block text-gray-700 text-sm font-bold mb-2">ক্রয়মূল্য (৳) *</label>
                    <input type="number" step="0.01" name="purchase_price" id="new_purchase_price" value="{{ old('purchase_price') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('purchase_price') border-red-500 @enderror">
                    @error('purchase_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_product_price" class="block text-gray-700 text-sm font-bold mb-2">বিক্রয়মূল্য (৳) *</label>
                    <input type="number" step="0.01" name="new_product_price" id="new_product_price" value="{{ old('new_product_price') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('new_product_price') border-red-500 @enderror">
                    @error('new_product_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                        পণ্য তৈরি ও স্টক যোগ করুন
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="new_supplier_name" class="block text-gray-700 text-sm font-bold mb-2">সরবরাহকারীর নাম (ঐচ্ছিক)</label>
                    <input type="text" name="supplier_name" id="new_supplier_name" value="{{ old('supplier_name') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="সরবরাহকারীর নাম">
                </div>

                <div>
                    <label for="new_supplier_phone" class="block text-gray-700 text-sm font-bold mb-2">সরবরাহকারীর ফোন (ঐচ্ছিক)</label>
                    <input type="text" name="supplier_phone" id="new_supplier_phone" value="{{ old('supplier_phone') }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="01XXXXXXXXX">
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <h2 class="text-lg sm:text-xl font-semibold p-4 sm:p-6 pb-0">স্টক এন্ট্রি ইতিহাস</h2>
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">তারিখ</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">পণ্য</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">পরিমাণ</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500">ক্রয়মূল্য</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 hidden sm:table-cell">যোগকারী</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockEntries as $entry)
                <tr>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ bn_number($entry->created_at->format('d/m/Y H:i')) }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ $entry->product->name }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ bn_number($entry->quantity) }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">৳{{ bn_number(number_format($entry->purchase_price, 2)) }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm hidden sm:table-cell">{{ $entry->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">কোন স্টক এন্ট্রি নেই</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $stockEntries->links() }}
    </div>
</div>

<script>
    // Barcode scanner auto-select product
    const barcodeInput = document.getElementById('barcode_scanner');
    let barcodeTimeout;
    
    barcodeInput.addEventListener('input', function(e) {
        clearTimeout(barcodeTimeout);
        const barcode = this.value.trim();
        
        if (barcode.length >= 3) {
            barcodeTimeout = setTimeout(() => {
                selectProductByBarcode(barcode);
            }, 500); // Wait 500ms after last input
        }
    });
    
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(barcodeTimeout);
            const barcode = this.value.trim();
            selectProductByBarcode(barcode);
        }
    });
    
    function selectProductByBarcode(barcode) {
        if (!barcode) return;
        
        const products = @json($products);
        const product = products.find(p => p.barcode === barcode || p.sku === barcode);
        
        if (product) {
            // Select the product in dropdown
            document.getElementById('product_id').value = product.id;
            
            // Trigger change event to auto-fill prices
            const event = new Event('change');
            document.getElementById('product_id').dispatchEvent(event);
            
            // Clear barcode input
            barcodeInput.value = '';
            barcodeInput.style.borderColor = '#10b981'; // Green
            barcodeInput.style.backgroundColor = '#d1fae5'; // Light green
            
            // Focus on quantity
            document.getElementById('quantity').focus();
            
            // Reset styling after 2 seconds
            setTimeout(() => {
                barcodeInput.style.borderColor = '';
                barcodeInput.style.backgroundColor = '';
            }, 2000);
        } else {
            // Product not found
            barcodeInput.style.borderColor = '#ef4444'; // Red
            barcodeInput.style.backgroundColor = '#fee2e2'; // Light red
            
            setTimeout(() => {
                barcodeInput.style.borderColor = '';
                barcodeInput.style.backgroundColor = '';
                barcodeInput.value = '';
            }, 2000);
        }
    }

    const toggleBtn = document.getElementById('toggleProductMode');
    const existingForm = document.getElementById('existingProductForm');
    const newForm = document.getElementById('newProductForm');
    let showingExisting = true;

    toggleBtn.addEventListener('click', function() {
        if (showingExisting) {
            existingForm.classList.add('hidden');
            newForm.classList.remove('hidden');
            toggleBtn.textContent = 'বিদ্যমান পণ্য নির্বাচন করুন';
            toggleBtn.classList.remove('bg-green-500', 'hover:bg-green-700');
            toggleBtn.classList.add('bg-blue-500', 'hover:bg-blue-700');
            showingExisting = false;
        } else {
            newForm.classList.add('hidden');
            existingForm.classList.remove('hidden');
            toggleBtn.textContent = 'নতুন পণ্য তৈরি করুন';
            toggleBtn.classList.remove('bg-blue-500', 'hover:bg-blue-700');
            toggleBtn.classList.add('bg-green-500', 'hover:bg-green-700');
            showingExisting = true;
        }
    });

    // Auto-fill product details when selected
    const productSelect = document.getElementById('product_id');
    const products = @json($products);
    const stockDisplay = document.getElementById('current_stock_display');
    
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const currentStock = selectedOption.dataset.currentStock;
            const purchasePrice = selectedOption.dataset.purchasePrice;
            const sellPrice = selectedOption.dataset.sellPrice;
            
            document.getElementById('purchase_price').value = purchasePrice || '';
            document.getElementById('sell_price').value = sellPrice || '';
            
            if (currentStock) {
                stockDisplay.textContent = 'বর্তমান স্টক: ' + currentStock;
            } else {
                stockDisplay.textContent = '';
            }
        } else {
            document.getElementById('purchase_price').value = '';
            document.getElementById('sell_price').value = '';
            stockDisplay.textContent = '';
        }
    });
</script>
@endsection

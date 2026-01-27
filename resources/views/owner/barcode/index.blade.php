@extends('layouts.app')

@section('title', 'Barcode Printer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">🏷️ {{ __('pos.barcode_printer') ?? 'Barcode Printer' }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('pos.print_barcode_labels') ?? 'Print barcode labels for your products' }}</p>
                </div>
                <a href="{{ route('owner.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    {{ __('pos.back') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form id="barcodeForm" action="{{ route('owner.barcode.generate') }}" method="POST" target="_blank">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Selection -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">{{ __('pos.select_products') ?? 'Select Products' }}</h2>
                        
                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text" 
                                   id="productSearch" 
                                   placeholder="{{ __('pos.search_products') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Product List -->
                        <div class="space-y-2 max-h-96 overflow-y-auto" id="productList">
                            @foreach($products as $product)
                                <div class="product-item border border-gray-200 rounded-lg p-3 hover:bg-blue-50 transition" 
                                     data-name="{{ strtolower($product->name) }}"
                                     data-sku="{{ strtolower($product->sku ?? '') }}"
                                     data-barcode="{{ strtolower($product->barcode ?? '') }}">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" 
                                               class="product-checkbox w-5 h-5 text-blue-600"
                                               data-product-id="{{ $product->id }}"
                                               data-product-name="{{ $product->name }}"
                                               data-product-sku="{{ $product->sku }}"
                                               data-product-barcode="{{ $product->barcode }}"
                                               data-product-price="{{ $product->sell_price }}">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                SKU: {{ $product->sku ?? 'N/A' }} | 
                                                Barcode: {{ $product->barcode ?? 'Auto' }} | 
                                                Price: ৳{{ $product->sell_price }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">{{ __('pos.quantity') }}:</label>
                                            <input type="number" 
                                                   class="quantity-input w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                                   data-product-id="{{ $product->id }}"
                                                   value="1" 
                                                   min="1"
                                                   disabled>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($products->isEmpty())
                            <p class="text-center text-gray-500 py-8">{{ __('pos.no_products') ?? 'No products found' }}</p>
                        @endif
                    </div>
                </div>

                <!-- Settings & Preview -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        <h2 class="text-lg font-semibold mb-4">{{ __('pos.print_settings') ?? 'Print Settings' }}</h2>
                        
                        <!-- Label Size -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pos.label_size') ?? 'Label Size' }}
                            </label>
                            <select name="label_size" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                <option value="small">{{ __('pos.small') ?? 'Small' }} (40x25mm)</option>
                                <option value="medium" selected>{{ __('pos.medium') ?? 'Medium' }} (50x30mm)</option>
                                <option value="large">{{ __('pos.large') ?? 'Large' }} (60x40mm)</option>
                            </select>
                        </div>

                        <!-- Include Options -->
                        <div class="mb-4 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="include_name" value="1" checked class="w-4 h-4 text-blue-600 mr-2">
                                <span class="text-sm text-gray-700">{{ __('pos.include_product_name') ?? 'Include Product Name' }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="include_price" value="1" checked class="w-4 h-4 text-blue-600 mr-2">
                                <span class="text-sm text-gray-700">{{ __('pos.include_price') ?? 'Include Price' }}</span>
                            </label>
                        </div>

                        <!-- Selected Products Summary -->
                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                            <p class="text-sm text-gray-600">{{ __('pos.selected_products') ?? 'Selected Products' }}:</p>
                            <p class="text-2xl font-bold text-blue-600"><span id="selectedCount">0</span></p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('pos.total_labels') ?? 'Total Labels' }}: <span id="totalLabels">0</span></p>
                        </div>

                        <!-- Hidden inputs for selected products -->
                        <div id="selectedProducts"></div>

                        <!-- Action Buttons -->
                        <button type="submit" 
                                id="printButton"
                                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            🖨️ {{ __('pos.generate_print') ?? 'Generate & Print' }}
                        </button>

                        <button type="button" 
                                onclick="selectAll()"
                                class="w-full mt-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            {{ __('pos.select_all') ?? 'Select All' }}
                        </button>

                        <button type="button" 
                                onclick="clearSelection()"
                                class="w-full mt-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                            {{ __('pos.clear_selection') ?? 'Clear Selection' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Product search functionality
    document.getElementById('productSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const products = document.querySelectorAll('.product-item');
        
        products.forEach(product => {
            const name = product.dataset.name;
            const sku = product.dataset.sku;
            const barcode = product.dataset.barcode;
            
            if (name.includes(searchTerm) || sku.includes(searchTerm) || barcode.includes(searchTerm)) {
                product.classList.remove('hidden');
            } else {
                product.classList.add('hidden');
            }
        });
    });

    // Enable/disable quantity input based on checkbox
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`input.quantity-input[data-product-id="${productId}"]`);
            quantityInput.disabled = !this.checked;
            updateSelection();
        });
    });

    // Update selection summary
    function updateSelection() {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        let totalLabels = 0;
        
        const selectedProductsDiv = document.getElementById('selectedProducts');
        selectedProductsDiv.innerHTML = '';
        
        checkboxes.forEach((checkbox, index) => {
            const productId = checkbox.dataset.productId;
            const quantityInput = document.querySelector(`input.quantity-input[data-product-id="${productId}"]`);
            const quantity = parseInt(quantityInput.value) || 1;
            totalLabels += quantity;
            
            // Add hidden inputs
            selectedProductsDiv.innerHTML += `
                <input type="hidden" name="products[${index}][id]" value="${productId}">
                <input type="hidden" name="products[${index}][quantity]" value="${quantity}">
            `;
        });
        
        document.getElementById('selectedCount').textContent = checkboxes.length;
        document.getElementById('totalLabels').textContent = totalLabels;
        document.getElementById('printButton').disabled = checkboxes.length === 0;
    }

    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', updateSelection);
    });

    // Select all
    function selectAll() {
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            if (!checkbox.closest('.product-item').classList.contains('hidden')) {
                checkbox.checked = true;
                const productId = checkbox.dataset.productId;
                const quantityInput = document.querySelector(`input.quantity-input[data-product-id="${productId}"]`);
                quantityInput.disabled = false;
            }
        });
        updateSelection();
    }

    // Clear selection
    function clearSelection() {
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            const productId = checkbox.dataset.productId;
            const quantityInput = document.querySelector(`input.quantity-input[data-product-id="${productId}"]`);
            quantityInput.disabled = true;
            quantityInput.value = 1;
        });
        updateSelection();
    }
</script>
@endsection

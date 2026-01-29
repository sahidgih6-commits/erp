@extends('pos.layout')

@section('title', __('pos.billing'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 h-screen bg-gray-50">
    <!-- Products Section -->
    <div class="lg:col-span-3 flex flex-col">
        <!-- Product Search Bar -->
        <div class="bg-white p-4 shadow">
            <div class="flex gap-2 mb-4">
                <input type="text" 
                       id="productSearch" 
                       placeholder="{{ __('pos.scan_product') }}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       autofocus>
                <button onclick="clearSearch()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    {{ __('pos.clear_cart') }}
                </button>
            </div>
            
            <!-- Auto-add Status Indicator -->
            <div id="scanStatus" class="hidden mb-2 p-2 rounded text-sm"></div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2" id="productGrid">
                @forelse($products as $product)
                    <div class="bg-gray-50 p-3 rounded-lg cursor-pointer hover:bg-blue-50 transition border" 
                         onclick="addProductToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->sell_price }})">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-600">SKU: {{ $product->sku }}</p>
                        <p class="text-lg font-bold text-blue-600 mt-1">৳ {{ $product->sell_price }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('pos.quantity') }}: {{ $product->current_stock }}</p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500">
                        {{ __('messages.no_products_found') ?? 'No products available' }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="flex-1 overflow-hidden mt-4">
            <div class="bg-white rounded-lg shadow h-full p-4">
                <p class="text-sm text-gray-600 mb-2">{{ __('pos.sales_summary') }}</p>
                <div id="todaysSummary" class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">{{ __('messages.today') ?? 'Today' }}</p>
                        <p class="text-2xl font-bold text-blue-600">৳ <span id="totalSales">0</span></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">{{ __('pos.transactions') }}</p>
                        <p class="text-2xl font-bold text-green-600"><span id="transactionCount">0</span></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">{{ __('pos.payment_method') }}</p>
                        <div class="text-sm mt-1">
                            <span class="text-gray-600">{{ __('messages.cash') ?? 'Cash' }}: <span id="cashSales">৳0</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Section (Sticky) -->
    <div class="lg:col-span-1 flex flex-col bg-white rounded-lg shadow-xl overflow-hidden">
        <!-- Cart Header -->
        <div class="bg-blue-600 text-white p-4">
            <h2 class="text-lg font-bold">{{ __('pos.billing') }}</h2>
            <p class="text-sm opacity-90">{{ __('pos.transaction_number') ?? 'Transaction' }}: <span id="transNo">New</span></p>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 border-b border-gray-200" id="cartItems">
            <div class="text-center text-gray-500 py-8">
                {{ __('pos.empty_cart') }}
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="bg-gray-50 p-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span>{{ __('pos.subtotal') }}:</span>
                <span class="font-semibold">৳ <span id="subtotal">0.00</span></span>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span>{{ __('pos.discount') }}:</span>
                    <select id="discountType" onchange="updateTotals()" class="px-2 py-1 border border-gray-300 rounded text-xs">
                        <option value="percent">শতাংশ (%)</option>
                        <option value="fixed">টাকা (৳)</option>
                    </select>
                </div>
                <div class="flex justify-between items-center">
                    <span></span>
                    <div class="flex items-center gap-2">
                        <input type="number" 
                               id="discountInput" 
                               value="0" 
                               min="0" 
                               step="0.1" 
                               onchange="updateTotals()" 
                               oninput="updateTotals()"
                               placeholder="0"
                               class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-right">
                        <span class="font-semibold text-xs text-red-600">-৳ <span id="discountAmount">0.00</span></span>
                    </div>
                </div>
            </div>
            <div class="flex justify-between text-gray-400">
                <span>{{ __('pos.tax') }} ({{ __('messages.disabled') ?? 'Disabled' }}):</span>
                <span class="font-semibold">৳ <span id="taxAmount">0.00</span></span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2 text-blue-600">
                <span>{{ __('pos.grand_total') }}:</span>
                <span>৳ <span id="total">0.00</span></span>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="p-4 border-t border-gray-200 space-y-3">
            <!-- Customer Info (Optional) -->
            <div class="bg-blue-50 p-3 rounded-lg space-y-2">
                <label class="block text-xs font-semibold text-gray-700">{{ __('sales.customer') }} ({{ __('messages.optional') ?? 'Optional' }})</label>
                <input type="text" 
                       id="customerName" 
                       placeholder="{{ __('sales.customer') }}"
                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded">
                <input type="text" 
                       id="customerPhone" 
                       placeholder="{{ __('sales.phone') }}"
                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded">
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('pos.payment_method') }}</label>
                <select id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="cash">{{ __('pos.cash_payment') }}</option>
                    <option value="card">{{ __('pos.card_payment') }}</option>
                    <option value="mobile">{{ __('pos.mobile_payment') }}</option>
                </select>
            </div>

            <!-- Amount Tendered -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('pos.amount_tendered') }}</label>
                <input type="number" 
                       id="amountTendered" 
                       placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                       @input="calculateChange">
            </div>

            <!-- Change -->
            <div class="bg-green-50 p-2 rounded">
                <p class="text-xs text-gray-600">{{ __('pos.change') }}</p>
                <p class="text-lg font-bold text-green-600">৳ <span id="change">0.00</span></p>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-2 pt-2">
                <button onclick="clearCart()" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm font-semibold">
                    {{ __('pos.clear_cart') }}
                </button>
                <button onclick="processPayment()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-semibold">
                    {{ __('pos.process_payment') }}
                </button>
            </div>
            
            <!-- Print Last Receipt Button -->
            <div id="lastReceiptSection" class="hidden">
                <button onclick="printLastReceipt()" class="w-full px-3 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    শেষ রসিদ প্রিন্ট করুন
                </button>
            </div>

            <!-- Drawer Button (if enabled) -->
            @if($cashDrawer)
                <button onclick="openDrawer()" class="w-full px-3 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-semibold">
                    {{ __('pos.cash_drawer') }} 🔓
                </button>
            @endif
        </div>

        <!-- Hardware Status -->
        <div class="bg-gray-100 p-3 text-xs border-t border-gray-200">
            <p class="font-semibold mb-2">{{ __('pos.device_status') }}</p>
            <div class="space-y-1">
                @if($barcodeScanner)
                    <p class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 {{ $barcodeScanner->is_connected ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                        {{ __('pos.barcode_scanner') }}: {{ $barcodeScanner->is_connected ? __('pos.connected') : __('pos.disconnected') }}
                    </p>
                @endif
                @if($thermalPrinter)
                    <p class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 {{ $thermalPrinter->is_connected ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                        {{ __('pos.thermal_printer') }}: {{ $thermalPrinter->is_connected ? __('pos.connected') : __('pos.disconnected') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cart Data (Hidden) -->
<input type="hidden" id="cartData" value='[]'>

<script>
    let cart = [];
    const CSRF = "{{ csrf_token() }}";
    const businessId = "{{ $business->id }}";
    let lastTransactionId = null;
    
    // Add product to cart
    function addProductToCart(productId, productName, price) {
        const existingItem = cart.find(item => item.product_id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                product_id: productId,
                quantity: 1,
                price: parseFloat(price)
            });
        }
        
        updateCart();
        document.getElementById('productSearch').focus();
    }
    
    // Remove product from cart
    function removeProductFromCart(productId) {
        cart = cart.filter(item => item.product_id !== productId);
        updateCart();
    }
    
    // Update quantity
    function updateQuantity(productId, quantity) {
        const item = cart.find(item => item.product_id === productId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                removeProductFromCart(productId);
            } else {
                updateCart();
            }
        }
    }
    
    // Update cart display
    function updateCart() {
        const cartItemsDiv = document.getElementById('cartItems');
        
        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<div class="text-center text-gray-500 py-8">' + "{{ __('pos.empty_cart') }}" + '</div>';
            updateTotals();
            return;
        }
        
        let html = '<div class="space-y-2">';
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            html += `
                <div class="bg-gray-50 p-2 rounded flex justify-between items-center">
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Product #${item.product_id}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <input type="number" value="${item.quantity}" min="1" 
                                   onchange="updateQuantity(${item.product_id}, this.value)"
                                   class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                            <span class="text-xs text-gray-600">x ৳${item.price.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-blue-600">৳${itemTotal.toFixed(2)}</p>
                        <button onclick="removeProductFromCart(${item.product_id})" 
                                class="text-xs text-red-600 hover:text-red-900">Remove</button>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        cartItemsDiv.innerHTML = html;
        updateTotals();
    }
    
    // Update totals
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discountInput = document.getElementById('discountInput');
        const discountType = document.getElementById('discountType').value;
        const discountValue = discountInput ? parseFloat(discountInput.value) || 0 : 0;
        
        // Calculate discount based on type
        let discount = 0;
        if (discountType === 'percent') {
            discount = (subtotal * discountValue) / 100;
        } else {
            discount = discountValue;
        }
        
        const tax = 0; // VAT/Tax disabled
        const total = Math.max(0, subtotal - discount + tax);
        
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('discountAmount').textContent = discount.toFixed(2);
        document.getElementById('taxAmount').textContent = tax.toFixed(2);
        document.getElementById('total').textContent = total.toFixed(2);
        
        calculateChange();
        
        // Update hidden cart data
        document.getElementById('cartData').value = JSON.stringify(cart);
    }
    
    // Calculate change
    function calculateChange() {
        const total = parseFloat(document.getElementById('total').textContent);
        const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
        const change = Math.max(0, tendered - total);
        document.getElementById('change').textContent = change.toFixed(2);
    }
    
    // Clear cart
    function clearCart() {
        if (confirm("{{ __('pos.confirm_clear') ?? 'Clear cart?' }}")) {
            cart = [];
            document.getElementById('amountTendered').value = '';
            document.getElementById('paymentMethod').value = 'cash';
            updateCart();
        }
    }
    
    // Process payment
    function processPayment() {
        if (cart.length === 0) {
            alert("{{ __('pos.empty_cart') }}");
            return;
        }
        
        const total = parseFloat(document.getElementById('total').textContent);
        const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
        
        if (tendered < total) {
            alert("{{ __('pos.insufficient_amount') ?? 'Insufficient amount' }}");
            return;
        }
        
        const payload = {
            items: cart,
            subtotal: parseFloat(document.getElementById('subtotal').textContent),
            discount: parseFloat(document.getElementById('discountAmount').textContent),
            discount_type: document.getElementById('discountType').value,
            discount_value: parseFloat(document.getElementById('discountInput').value) || 0,
            tax: parseFloat(document.getElementById('taxAmount').textContent),
            total: total,
            payment_method: document.getElementById('paymentMethod').value,
            amount_tendered: tendered,
            notes: 'POS Transaction',
            customer_name: document.getElementById('customerName').value || null,
            customer_phone: document.getElementById('customerPhone').value || null
        };
        
        fetch('{{ route("pos.transaction.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("{{ __('pos.transaction_saved') }}");
                printReceiptAndReset(data.transaction_id);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('messages.error_occurred') ?? "Error occurred" }}');
        });
    }
    
    // Print receipt and reset
    function printReceiptAndReset(transactionId) {
        // Save last transaction ID
        lastTransactionId = transactionId;
        document.getElementById('lastReceiptSection').classList.remove('hidden');
        
        // Open receipt in popup window with specific features
        const receiptUrl = '{{ route("pos.receipt.view", "") }}/' + transactionId;
        window.open(receiptUrl, 'POSReceipt', 'width=400,height=700,scrollbars=yes,location=no,menubar=no,toolbar=no');
        
        // Print receipt if printer enabled
        @if($canPrintReceipt)
            fetch(`{{ route('pos.receipt.print', '') }}/${transactionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({ paper_size: '80mm' })
            });
        @endif
        
        // Reset cart
        cart = [];
        document.getElementById('amountTendered').value = '';
        document.getElementById('paymentMethod').value = 'cash';
        document.getElementById('customerName').value = '';
        document.getElementById('customerPhone').value = '';
        document.getElementById('discountInput').value = '0';
        document.getElementById('discountType').value = 'percent';
        updateCart();
        
        // Fetch and update summary
        fetchSummary();
    }
    
    // Print last receipt
    function printLastReceipt() {
        if (!lastTransactionId) {
            alert('কোন রসিদ পাওয়া যায়নি');
            return;
        }
        const receiptUrl = '{{ route("pos.receipt.view", "") }}/' + lastTransactionId;
        window.open(receiptUrl, 'POSReceiptReprint', 'width=400,height=700,scrollbars=yes,location=no,menubar=no,toolbar=no');
    }
    
    // Open cash drawer
    function openDrawer() {
        fetch('{{ route("pos.drawer.open") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("{{ __('pos.drawer_opened') }}");
            }
        });
    }
    
    // Fetch daily summary
    function fetchSummary() {
        fetch('{{ route("pos.summary") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalSales').textContent = data.total_sales.toFixed(2);
                document.getElementById('transactionCount').textContent = data.transaction_count;
                document.getElementById('cashSales').textContent = '৳' + data.cash_sales.toFixed(2);
            });
    }
    
    // Clear search
    function clearSearch() {
        document.getElementById('productSearch').value = '';
        hideScanStatus();
    }
    
    // Show scan status message
    function showScanStatus(message, type = 'success') {
        const statusDiv = document.getElementById('scanStatus');
        statusDiv.className = type === 'success' 
            ? 'mb-2 p-2 rounded text-sm bg-green-100 text-green-700 border border-green-300'
            : 'mb-2 p-2 rounded text-sm bg-red-100 text-red-700 border border-red-300';
        statusDiv.textContent = message;
        statusDiv.classList.remove('hidden');
        
        // Auto-hide after 2 seconds
        setTimeout(() => {
            hideScanStatus();
        }, 2000);
    }
    
    // Hide scan status
    function hideScanStatus() {
        const statusDiv = document.getElementById('scanStatus');
        statusDiv.classList.add('hidden');
    }
    
    // Auto-add product when barcode is scanned
    function searchAndAddProduct(searchTerm) {
        if (!searchTerm || searchTerm.length < 3) {
            return;
        }
        
        // Search for product by SKU or barcode
        fetch(`{{ route('pos.product.search') }}?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.products && data.products.length > 0) {
                    const product = data.products[0]; // Get first match
                    
                    // Check stock availability
                    if (product.current_stock <= 0) {
                        showScanStatus("{{ __('pos.product_out_of_stock') }}", 'error');
                        clearSearch();
                        return;
                    }
                    
                    // Auto-add to cart
                    addProductToCart(product.id, product.name, product.sell_price);
                    
                    // Show success message
                    showScanStatus(`✓ ${product.name} {{ __('messages.added') ?? 'added' }}!`, 'success');
                    
                    // Clear search box for next scan
                    clearSearch();
                    document.getElementById('productSearch').focus();
                } else {
                    showScanStatus("{{ __('pos.no_barcode_found') }}", 'error');
                    setTimeout(() => {
                        clearSearch();
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                showScanStatus('{{ __("messages.error_occurred") ?? "Error" }}', 'error');
            });
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        fetchSummary();
        setInterval(fetchSummary, 30000); // Update every 30 seconds
        
        // Add barcode scanner auto-add functionality
        const searchInput = document.getElementById('productSearch');
        let searchTimeout;
        
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            
            // If Enter key pressed, immediately search and add
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    searchAndAddProduct(searchTerm);
                }
                return;
            }
            
            // Otherwise, wait for user to stop typing (for manual search)
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim();
                if (searchTerm && searchTerm.length >= 3) {
                    // Auto-trigger after 500ms of no typing (barcode scanners are fast)
                    searchAndAddProduct(searchTerm);
                }
            }, 500);
        });
    });
</script>
@endsection

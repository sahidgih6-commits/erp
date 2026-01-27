@extends('pos.layout')

@section('title', 'Enhanced POS Billing')

@section('content')
<div class="h-screen flex flex-col bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">🛒 Enhanced POS System</h1>
        <div class="flex gap-4">
            <span class="bg-white text-blue-600 px-4 py-2 rounded font-semibold">{{ auth()->user()->name }}</span>
            <button id="cashDrawerBtn" class="bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded font-semibold">Cash Drawer</button>
        </div>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <!-- Left: Product Section -->
        <div class="w-2/3 p-4 overflow-y-auto">
            <!-- Category Filter -->
            <div class="mb-4">
                <div class="flex gap-2 flex-wrap">
                    <button class="category-btn px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600 active" data-category="all">
                        All Products
                    </button>
                    @foreach($categories as $category)
                    <button class="category-btn px-4 py-2 rounded bg-gray-200 hover:bg-gray-300" data-category="{{ $category->id }}">
                        {{ $category->icon }} {{ $category->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-4">
                <input type="text" id="productSearch" placeholder="Search products... (or scan barcode)" 
                    class="w-full p-3 border rounded-lg" autofocus>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-cols-3 gap-4">
                @foreach($products as $product)
                <div class="product-card bg-white p-4 rounded-lg shadow hover:shadow-lg cursor-pointer transition" 
                    data-category="{{ $product->category_id }}"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-stock="{{ $product->stock }}"
                    data-barcode="{{ $product->barcode }}">
                    <div class="text-center">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover mx-auto mb-2 rounded">
                        @else
                        <div class="w-20 h-20 bg-gray-200 mx-auto mb-2 rounded flex items-center justify-center text-3xl">
                            📦
                        </div>
                        @endif
                        <h3 class="font-semibold text-sm mb-1">{{ $product->name }}</h3>
                        <p class="text-green-600 font-bold">৳{{ number_format($product->price, 2) }}</p>
                        <p class="text-xs text-gray-500">Stock: {{ $product->stock }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Cart Section -->
        <div class="w-1/3 bg-white p-4 flex flex-col shadow-lg">
            <h2 class="text-xl font-bold mb-4">Cart Items</h2>
            
            <!-- Cart Items -->
            <div id="cartItems" class="flex-1 overflow-y-auto mb-4">
                <!-- Cart items will be dynamically added here -->
            </div>

            <!-- Discount Section -->
            <div class="mb-4 border-t pt-4">
                <div class="flex gap-2 mb-2">
                    <select id="discountType" class="border rounded px-2 py-1">
                        <option value="percentage">% Discount</option>
                        <option value="fixed">Fixed Discount</option>
                    </select>
                    <input type="number" id="discountValue" placeholder="0" class="border rounded px-2 py-1 flex-1" min="0" step="0.01">
                    <button id="applyDiscount" class="bg-orange-500 text-white px-4 py-1 rounded hover:bg-orange-600">Apply</button>
                </div>
                <p class="text-sm text-gray-600">Discount: <span id="discountAmount">৳0.00</span></p>
            </div>

            <!-- Totals -->
            <div class="border-t pt-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Subtotal:</span>
                    <span id="subtotal">৳0.00</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Discount:</span>
                    <span id="totalDiscount" class="text-red-600">৳0.00</span>
                </div>
                <div class="flex justify-between text-xl font-bold">
                    <span>Total:</span>
                    <span id="grandTotal">৳0.00</span>
                </div>
            </div>

            <!-- Customer Selection -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Customer:</label>
                <select id="customerSelect" class="w-full border rounded px-2 py-2">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-credit="{{ $customer->credit_limit }}" data-due="{{ $customer->total_due }}">
                        {{ $customer->name }} - {{ $customer->phone }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Method -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Payment Method:</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($paymentMethods as $method)
                    <button class="payment-method-btn border-2 border-gray-300 rounded px-4 py-2 hover:border-blue-500" 
                        data-method="{{ $method->type }}">
                        @if($method->icon) {{ $method->icon }} @endif {{ $method->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Paid Amount -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Paid Amount:</label>
                <input type="number" id="paidAmount" class="w-full border rounded px-3 py-2" placeholder="0.00" step="0.01" min="0">
                <p class="text-sm text-gray-600 mt-1">Change: <span id="changeAmount">৳0.00</span></p>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-2">
                <button id="holdBtn" class="bg-yellow-500 text-white px-4 py-3 rounded font-semibold hover:bg-yellow-600">
                    Hold
                </button>
                <button id="recallBtn" class="bg-purple-500 text-white px-4 py-3 rounded font-semibold hover:bg-purple-600">
                    Recall
                </button>
                <button id="clearBtn" class="bg-red-500 text-white px-4 py-3 rounded font-semibold hover:bg-red-600">
                    Clear
                </button>
                <button id="payBtn" class="bg-green-500 text-white px-4 py-3 rounded font-semibold hover:bg-green-600">
                    Pay
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let selectedPaymentMethod = 'cash';
let discount = { type: 'percentage', value: 0 };
let heldTransactions = JSON.parse(localStorage.getItem('heldTransactions') || '[]');

// Add product to cart
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function() {
        const product = {
            id: this.dataset.id,
            name: this.dataset.name,
            price: parseFloat(this.dataset.price),
            stock: parseInt(this.dataset.stock)
        };
        addToCart(product);
    });
});

function addToCart(product) {
    const existing = cart.find(item => item.id === product.id);
    if (existing) {
        if (existing.quantity < product.stock) {
            existing.quantity++;
        } else {
            alert('Insufficient stock!');
            return;
        }
    } else {
        cart.push({ ...product, quantity: 1 });
    }
    renderCart();
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    renderCart();
}

function updateQuantity(productId, quantity) {
    const item = cart.find(i => i.id === productId);
    if (item) {
        if (quantity <= 0) {
            removeFromCart(productId);
        } else if (quantity <= item.stock) {
            item.quantity = quantity;
            renderCart();
        } else {
            alert('Insufficient stock!');
        }
    }
}

function renderCart() {
    const cartContainer = document.getElementById('cartItems');
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="text-gray-500 text-center">Cart is empty</p>';
    } else {
        cartContainer.innerHTML = cart.map(item => `
            <div class="mb-3 p-2 border rounded">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-semibold">${item.name}</h4>
                        <p class="text-sm text-gray-600">৳${item.price.toFixed(2)} each</p>
                    </div>
                    <button onclick="removeFromCart('${item.id}')" class="text-red-500 hover:text-red-700">×</button>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <button onclick="updateQuantity('${item.id}', ${item.quantity - 1})" class="bg-gray-200 px-2 py-1 rounded">-</button>
                    <input type="number" value="${item.quantity}" 
                        onchange="updateQuantity('${item.id}', parseInt(this.value))" 
                        class="w-16 text-center border rounded px-2 py-1" min="1" max="${item.stock}">
                    <button onclick="updateQuantity('${item.id}', ${item.quantity + 1})" class="bg-gray-200 px-2 py-1 rounded">+</button>
                    <span class="ml-auto font-semibold">৳${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            </div>
        `).join('');
    }
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discountAmount = 0;
    
    if (discount.value > 0) {
        if (discount.type === 'percentage') {
            discountAmount = subtotal * (discount.value / 100);
        } else {
            discountAmount = discount.value;
        }
    }
    
    const total = subtotal - discountAmount;
    
    document.getElementById('subtotal').textContent = '৳' + subtotal.toFixed(2);
    document.getElementById('discountAmount').textContent = '৳' + discountAmount.toFixed(2);
    document.getElementById('totalDiscount').textContent = '৳' + discountAmount.toFixed(2);
    document.getElementById('grandTotal').textContent = '৳' + total.toFixed(2);
    
    updateChange();
}

function updateChange() {
    const total = parseFloat(document.getElementById('grandTotal').textContent.replace('৳', ''));
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = paid - total;
    document.getElementById('changeAmount').textContent = '৳' + (change >= 0 ? change.toFixed(2) : '0.00');
}

// Apply discount
document.getElementById('applyDiscount').addEventListener('click', function() {
    discount.type = document.getElementById('discountType').value;
    discount.value = parseFloat(document.getElementById('discountValue').value) || 0;
    updateTotals();
});

// Payment method selection
document.querySelectorAll('.payment-method-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('border-blue-500', 'bg-blue-50'));
        this.classList.add('border-blue-500', 'bg-blue-50');
        selectedPaymentMethod = this.dataset.method;
    });
});

// Category filter
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('bg-blue-500', 'text-white', 'active'));
        document.querySelectorAll('.category-btn').forEach(b => b.classList.add('bg-gray-200'));
        this.classList.add('bg-blue-500', 'text-white', 'active');
        this.classList.remove('bg-gray-200');
        
        const category = this.dataset.category;
        document.querySelectorAll('.product-card').forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Product search
document.getElementById('productSearch').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const barcode = card.dataset.barcode.toLowerCase();
        if (name.includes(search) || barcode.includes(search)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Paid amount change
document.getElementById('paidAmount').addEventListener('input', updateChange);

// Pay button
document.getElementById('payBtn').addEventListener('click', function() {
    if (cart.length === 0) {
        alert('Cart is empty!');
        return;
    }
    
    const total = parseFloat(document.getElementById('grandTotal').textContent.replace('৳', ''));
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    
    if (paid < total) {
        const confirm = window.confirm(`Payment is less than total. Create due of ৳${(total - paid).toFixed(2)}?`);
        if (!confirm) return;
    }
    
    // Calculate discount amount
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discountAmount = 0;
    if (discount.value > 0) {
        if (discount.type === 'percentage') {
            discountAmount = subtotal * (discount.value / 100);
        } else {
            discountAmount = discount.value;
        }
    }
    
    // Submit the sale
    const formData = {
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        customer_id: document.getElementById('customerSelect').value || null,
        payment_method: selectedPaymentMethod,
        discount_type: discount.type,
        discount_amount: discountAmount,
        paid_amount: paid
    };
    
    fetch('{{ route("pos.enhanced-billing.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sale completed successfully!');
            cart = [];
            renderCart();
            document.getElementById('paidAmount').value = '';
            document.getElementById('customerSelect').value = '';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error processing sale: ' + error);
    });
});

// Hold transaction
document.getElementById('holdBtn').addEventListener('click', function() {
    if (cart.length === 0) {
        alert('Cart is empty!');
        return;
    }
    
    const transaction = {
        id: Date.now(),
        items: [...cart],
        discount: {...discount},
        customer: document.getElementById('customerSelect').value,
        timestamp: new Date().toISOString()
    };
    
    heldTransactions.push(transaction);
    localStorage.setItem('heldTransactions', JSON.stringify(heldTransactions));
    
    alert('Transaction held successfully!');
    cart = [];
    renderCart();
});

// Recall transaction
document.getElementById('recallBtn').addEventListener('click', function() {
    if (heldTransactions.length === 0) {
        alert('No held transactions!');
        return;
    }
    
    const list = heldTransactions.map((t, i) => `${i + 1}. ${new Date(t.timestamp).toLocaleString()} - ${t.items.length} items`).join('\n');
    const index = prompt('Select transaction to recall:\n' + list) - 1;
    
    if (index >= 0 && index < heldTransactions.length) {
        const transaction = heldTransactions[index];
        cart = transaction.items;
        discount = transaction.discount;
        document.getElementById('customerSelect').value = transaction.customer;
        renderCart();
        
        heldTransactions.splice(index, 1);
        localStorage.setItem('heldTransactions', JSON.stringify(heldTransactions));
    }
});

// Clear cart
document.getElementById('clearBtn').addEventListener('click', function() {
    if (confirm('Clear all items from cart?')) {
        cart = [];
        renderCart();
        document.getElementById('paidAmount').value = '';
        discount = { type: 'percentage', value: 0 };
        document.getElementById('discountValue').value = '';
    }
});

// Initial render
renderCart();
</script>
@endsection

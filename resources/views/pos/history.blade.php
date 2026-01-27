@extends('pos.layout')

@section('title', __('pos.transactions'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('pos.transaction_history') ?? 'Transaction History' }}</h1>
        <a href="{{ route('pos.billing') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            + {{ __('pos.new_transaction') ?? 'New Transaction' }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.start_date') ?? 'Start Date' }}</label>
                <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.end_date') ?? 'End Date' }}</label>
                <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('pos.payment_method') }}</label>
                <select id="paymentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">{{ __('messages.all') ?? 'All' }}</option>
                    <option value="cash">{{ __('pos.cash_payment') }}</option>
                    <option value="card">{{ __('pos.card_payment') }}</option>
                    <option value="mobile">{{ __('pos.mobile_payment') }}</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button onclick="filterTransactions()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    {{ __('messages.filter') ?? 'Filter' }}
                </button>
                <button onclick="resetFilter()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                    {{ __('messages.reset') ?? 'Reset' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.receipt_number') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.date_time') ?? 'Date & Time' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.user') ?? 'User' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.payment_method') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.items') ?? 'Items' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.status') ?? 'Status' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-gray-900">
                                    {{ $transaction->transaction_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $transaction->completed_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $transaction->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                ৳ {{ number_format($transaction->total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                       ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                    @if($transaction->payment_method === 'cash')
                                        {{ __('pos.cash_payment') }}
                                    @elseif($transaction->payment_method === 'card')
                                        {{ __('pos.card_payment') }}
                                    @else
                                        {{ __('pos.mobile_payment') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="bg-gray-100 px-2 py-1 rounded">
                                    {{ count($transaction->items ?? []) }} {{ __('messages.items') ?? 'items' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($transaction->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button onclick="viewDetails({{ $transaction->id }})" 
                                        class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ __('messages.view') ?? 'View' }}
                                </button>
                                @if($transaction->receipt_printed)
                                    <button onclick="reprintReceipt({{ $transaction->id }})" 
                                            class="text-green-600 hover:text-green-900 font-medium">
                                        {{ __('pos.reprint_receipt') }}
                                    </button>
                                @else
                                    <button onclick="printReceipt({{ $transaction->id }})" 
                                            class="text-orange-600 hover:text-orange-900 font-medium">
                                        {{ __('pos.print_receipt') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                {{ __('messages.no_transactions_found') ?? 'No transactions found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<!-- Modal for Details -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('messages.transaction_details') ?? 'Transaction Details' }}</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
        </div>
        <div id="modalContent" class="p-6">
            <!-- Content loaded via JavaScript -->
        </div>
    </div>
</div>

<script>
    const CSRF = "{{ csrf_token() }}";

    function viewDetails(transactionId) {
        // Fetch transaction details
        fetch(`/pos/transaction/${transactionId}`)
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600 uppercase">{{ __('pos.receipt_number') }}</p>
                                <p class="font-mono text-sm font-semibold">${data.transaction_number}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 uppercase">{{ __('messages.date_time') ?? 'Date & Time' }}</p>
                                <p class="text-sm font-medium">${new Date(data.completed_at).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 uppercase">{{ __('messages.user') ?? 'User' }}</p>
                                <p class="text-sm font-medium">${data.user.name}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 uppercase">{{ __('pos.payment_method') }}</p>
                                <p class="text-sm font-medium capitalize">${data.payment_method}</p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold mb-2">{{ __('messages.items') ?? 'Items' }}</p>
                            <div class="space-y-2">
                `;

                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        const itemTotal = (item.quantity * item.price).toFixed(2);
                        html += `
                            <div class="flex justify-between text-sm">
                                <span>Product #${item.product_id} × ${item.quantity}</span>
                                <span class="font-semibold">৳ ${itemTotal}</span>
                            </div>
                        `;
                    });
                }

                html += `
                            </div>
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>{{ __('pos.subtotal') }}:</span>
                                <span>৳ ${parseFloat(data.subtotal).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>{{ __('pos.tax') }}:</span>
                                <span>৳ ${parseFloat(data.tax).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-blue-600">
                                <span>{{ __('pos.grand_total') }}:</span>
                                <span>৳ ${parseFloat(data.total).toFixed(2)}</span>
                            </div>
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>{{ __('pos.amount_tendered') }}:</span>
                                <span class="font-semibold">৳ ${parseFloat(data.amount_tendered || data.total).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between text-sm bg-green-50 p-2 rounded">
                                <span>{{ __('pos.change') }}:</span>
                                <span class="font-semibold text-green-600">৳ ${parseFloat(data.change || 0).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('detailsModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('messages.error_loading_details') ?? "Error loading details" }}');
            });
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function printReceipt(transactionId) {
        fetch(`{{ route('pos.receipt.print', '') }}/${transactionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ paper_size: '80mm' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("{{ __('pos.print_successful') }}");
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function reprintReceipt(transactionId) {
        printReceipt(transactionId);
    }

    function filterTransactions() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const payment = document.getElementById('paymentFilter').value;

        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (payment) params.append('payment_method', payment);

        window.location.href = `{{ route('pos.history') }}?${params.toString()}`;
    }

    function resetFilter() {
        document.getElementById('startDate').value = '{{ now()->format('Y-m-d') }}';
        document.getElementById('endDate').value = '{{ now()->format('Y-m-d') }}';
        document.getElementById('paymentFilter').value = '';
        window.location.href = '{{ route('pos.history') }}';
    }

    // Close modal on outside click
    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection

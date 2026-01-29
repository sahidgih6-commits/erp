<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - {{ $transaction->transaction_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: {{ $template->receipt_font_size ?? '12px' }};
            line-height: 1.4;
            color: #000;
            background: #fff;
            width: {{ $template->receipt_paper_size ?? '80mm' }};
            margin: 0 auto;
            padding: 5mm;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .logo {
            max-width: 80px;
            margin: 0 auto 5px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-info {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .header-text {
            margin-top: 5px;
            font-size: 11px;
            font-style: italic;
        }
        
        .receipt-meta {
            margin: 10px 0;
            font-size: 11px;
        }
        
        .receipt-meta table {
            width: 100%;
        }
        
        .receipt-meta td {
            padding: 2px 0;
        }
        
        .receipt-items {
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 2px solid #000;
        }
        
        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .receipt-items th {
            text-align: left;
            padding: 5px 0;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }
        
        .receipt-items td {
            padding: 3px 0;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals {
            margin: 10px 0;
        }
        
        .totals table {
            width: 100%;
        }
        
        .totals td {
            padding: 3px 0;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px !important;
        }
        
        .payment-info {
            margin: 10px 0;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }
        
        @media print {
            body {
                width: {{ $template->receipt_paper_size ?? '80mm' }};
            }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        @if($template && ($template->receipt_show_logo ?? true) && $template->logo_url)
            <img src="{{ $template->logo_url }}" alt="Logo" class="logo">
        @endif
        
        <div class="company-name">{{ $template->company_name ?? $business->name }}</div>
        
        <div class="company-info">
            @if($template && $template->company_address)
                {{ $template->company_address }}<br>
            @endif
            @if($template && $template->company_phone)
                Tel: {{ $template->company_phone }}
            @endif
        </div>
        
        @if($template && $template->receipt_header_text)
            <div class="header-text">{{ $template->receipt_header_text }}</div>
        @endif
    </div>
    
    <div class="receipt-meta">
        <table>
            <tr>
                <td><strong>Receipt#:</strong></td>
                <td class="text-right">{{ $transaction->transaction_number }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td class="text-right">{{ $transaction->completed_at->format('d/m/Y h:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Cashier:</strong></td>
                <td class="text-right">{{ $transaction->user->name }}</td>
            </tr>
            @if(($template->receipt_show_customer ?? true) && isset($customerName) && $customerName != 'POS Customer')
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td class="text-right">{{ $customerName }}</td>
                </tr>
                @if($customerPhone)
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td class="text-right">{{ $customerPhone }}</td>
                    </tr>
                @endif
            @endif
        </table>
    </div>
    
    <div class="receipt-items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    @php
                        $product = \App\Models\Product::find($item['product_id']);
                    @endphp
                    <tr>
                        <td>{{ $product->name ?? 'Unknown' }}</td>
                        <td class="text-right">{{ $item['quantity'] }}</td>
                        <td class="text-right">৳{{ number_format($item['price'], 2) }}</td>
                        <td class="text-right">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">৳{{ number_format($transaction->subtotal, 2) }}</td>
            </tr>
            @if($transaction->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-৳{{ number_format($transaction->discount, 2) }}</td>
                </tr>
            @endif
            @if($transaction->tax > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">৳{{ number_format($transaction->tax, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>TOTAL:</td>
                <td class="text-right">৳{{ number_format($transaction->total, 2) }}</td>
            </tr>
        </table>
    </div>
    
    @if($template->receipt_show_payment_method ?? true)
        <div class="payment-info">
            <table>
                <tr>
                    <td><strong>Payment Method:</strong></td>
                    <td class="text-right">{{ ucfirst($transaction->payment_method) }}</td>
                </tr>
                <tr>
                    <td><strong>Paid:</strong></td>
                    <td class="text-right">৳{{ number_format($transaction->amount_tendered, 2) }}</td>
                </tr>
                @if($transaction->change > 0)
                    <tr>
                        <td><strong>Change:</strong></td>
                        <td class="text-right">৳{{ number_format($transaction->change, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
    
    <div class="footer">
        @if($template && $template->receipt_footer_text)
            {{ $template->receipt_footer_text }}<br>
        @endif
        Thank you for your business!<br>
        Please come again
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>

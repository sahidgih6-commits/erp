@php
    $primaryColor = $template->primary_color ?? '#1e40af';
    $secondaryColor = $template->secondary_color ?? '#3b82f6';
    $fontSize = $template->font_size ?? '13px';
    $pageMargin = $template->page_margin ?? '5mm';
@endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ভাউচার - {{ $sale->voucher_number }}</title>
    <style>
        @media print {
            body { 
                margin: 0;
                padding: 0;
            }
            .no-print { display: none; }
            @page { 
                size: A4;
                margin: {{ $pageMargin }};
            }
            .voucher {
                box-shadow: none;
                border: 1px solid #000;
                page-break-inside: avoid;
                min-height: auto;
                padding: {{ $pageMargin }};
            }
            .signature-section {
                margin-top: 25px;
            }
            .signature-line {
                margin-top: 35px;
            }
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 100px;
                color: rgba(0, 0, 0, 0.05);
                z-index: -1;
                pointer-events: none;
                white-space: nowrap;
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', Arial, sans-serif;
            background: #f5f5f5;
            padding: 8px;
            line-height: 1.4;
            font-size: {{ $fontSize }};
            position: relative;
        }
        .voucher {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 8mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border: 1px solid #333;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.03);
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
            font-weight: bold;
        }
        .voucher > * {
            position: relative;
            z-index: 1;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .company-logo {
            max-width: 120px;
            max-height: 80px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: {{ $primaryColor }};
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .company-details {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.5;
        }
        .header-text {
            font-size: 10px;
            font-style: italic;
            color: #6b7280;
            margin-top: 4px;
            font-weight: 500;
        }
        .voucher-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            color: white;
            padding: 5px;
            margin: 8px 0;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .voucher-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 8px;
            background: #f9fafb;
            border-left: 3px solid {{ $secondaryColor }};
        }
        .voucher-number {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
        }
        .voucher-date {
            font-size: 12px;
            color: #4b5563;
        }
        .section {
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 1px solid {{ $secondaryColor }};
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            padding: 6px;
            background: #f9fafb;
            border-radius: 4px;
        }
        .info-item {
            display: flex;
            gap: 6px;
            font-size: 12px;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            min-width: 100px;
        }
        .info-value {
            color: #111827;
            font-weight: 500;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-size: 12px;
        }
        .table th {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            color: white;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid {{ $secondaryColor }};
            font-weight: 600;
            font-size: calc({{ $fontSize }} + 0px);
        }
        .table td {
            padding: 4px 6px;
            border: 1px solid #d1d5db;
            background: white;
            font-size: 12px;
        }
        .table tbody tr:hover {
            background: #f9fafb;
        }
        .total-section {
            margin-top: 10px;
            padding: 8px;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px solid {{ $secondaryColor }};
            border-radius: 4px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12px;
            border-bottom: 1px dashed #d1d5db;
        }
        .total-row:last-child {
            border-bottom: none;
        }
        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            border-top: 2px double #000;
            padding-top: 6px;
            margin-top: 5px;
            border-bottom: none;
        }
        .total-row.due {
            color: #dc2626;
        }
        .signature-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 15px;
            padding-top: 8px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 25px;
            padding-top: 5px;
            font-weight: 600;
            font-size: 11px;
            color: #374151;
        }
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        .footer-highlight {
            margin-bottom: 4px;
            font-size: 10px;
            color: #1f2937;
            font-weight: 600;
        }
        .print-button {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
            transition: all 0.3s;
        }
        .print-button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            box-shadow: 0 6px 8px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-left: 10px;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-due {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 15px;">
        <div style="display: flex; gap: 10px; justify-content: center; align-items: center; flex-wrap: wrap;">
            <a href="{{ route(auth()->user()->getRoleNames()->first() . '.dashboard') }}" 
               style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3); transition: all 0.3s; display: inline-block;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px rgba(16, 185, 129, 0.4)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(16, 185, 129, 0.3)'">
                🏠 ড্যাশবোর্ডে ফিরুন / Back to Dashboard
            </a>
            <button class="print-button" onclick="window.print()">🖨️ প্রিন্ট করুন / Print</button>
        </div>
    </div>

    <div class="voucher">
        <!-- Watermark -->
        @if($template && $template->show_watermark && $template->watermark_text)
            <div class="watermark">{{ $template->watermark_text }}</div>
        @endif

        <!-- Header -->
        <div class="header">
            @if($template)
                @if($template->logo_url)
                    <img src="{{ $template->logo_url }}" alt="Company Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $template->company_name }}</div>
                <div class="company-details">
                    @if($template->company_address)
                        <div>📍 {{ $template->company_address }}</div>
                    @endif
                    @if($template->company_phone)
                        <div>📞 {{ $template->company_phone }}</div>
                    @endif
                </div>
                @if($template->header_text)
                    <div class="header-text">{{ $template->header_text }}</div>
                @endif
            @else
                <div class="company-name">বিক্রয় ভাউচার</div>
            @endif
        </div>

        <!-- Voucher Title -->
        <div class="voucher-title">
            SALES INVOICE / বিক্রয় চালান
        </div>

        <!-- Voucher Meta -->
        <div class="voucher-meta">
            <div class="voucher-number">
                ভাউচার নং: {{ $sale->voucher_number }}
                @if($sale->due_amount > 0)
                    <span class="status-badge status-due">বকেয়া / DUE</span>
                @else
                    <span class="status-badge status-paid">পরিশোধিত / PAID</span>
                @endif
            </div>
            <div class="voucher-date">
                📅 {{ $sale->created_at->format('d/m/Y h:i A') }}
            </div>
        </div>

        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">গ্রাহকের তথ্য / Customer Information</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">নাম / Name:</span>
                    <span class="info-value">{{ $sale->customer_name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ফোন / Phone:</span>
                    <span class="info-value">{{ $sale->customer_phone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">সেলসম্যান / Salesman:</span>
                    <span class="info-value">{{ $sale->user->name }}</span>
                </div>
                @if($sale->expected_clear_date)
                <div class="info-item">
                    <span class="info-label">পরিশোধের তারিখ:</span>
                    <span class="info-value">{{ $sale->expected_clear_date->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="section">
            <div class="section-title">পণ্যের বিবরণ / Product Details</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>পণ্যের নাম / Product Name</th>
                        <th>কোড / SKU</th>
                        <th style="text-align: center;">পরিমাণ / Qty</th>
                        <th style="text-align: right;">একক মূল্য / Price</th>
                        <th style="text-align: right;">মোট / Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                        $grandPaid = 0;
                        $grandDue = 0;
                    @endphp
                    @foreach($allSales as $saleItem)
                        @php
                            $grandTotal += $saleItem->total_amount;
                            $grandPaid += $saleItem->paid_amount;
                            $grandDue += $saleItem->due_amount;
                        @endphp
                        <tr>
                            <td><strong>{{ $saleItem->product->name }}</strong></td>
                            <td>{{ $saleItem->product->sku }}</td>
                            <td style="text-align: center;"><strong>{{ $saleItem->quantity }}</strong></td>
                            <td style="text-align: right;">৳{{ number_format($saleItem->sell_price, 2) }}</td>
                            <td style="text-align: right;"><strong>৳{{ number_format($saleItem->total_amount, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Payment Summary -->
        <div class="total-section">
            <div class="total-row">
                <span><strong>মোট টাকা / Total Amount:</strong></span>
                <span><strong>৳{{ number_format($grandTotal, 2) }}</strong></span>
            </div>
            <div class="total-row">
                <span>পরিশোধিত / Paid:</span>
                <span>৳{{ number_format($grandPaid, 2) }}</span>
            </div>
            @if($grandDue > 0)
            <div class="total-row grand due">
                <span>বকেয়া / Due Amount:</span>
                <span>৳{{ number_format($grandDue, 2) }}</span>
            </div>
            @else
            <div class="total-row grand">
                <span>✓ সম্পূর্ণ পরিশোধিত / FULLY PAID</span>
                <span>✓</span>
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    বিক্রেতার স্বাক্ষর<br>
                    <small style="font-weight: 400;">Seller's Signature</small>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    ক্রেতার স্বাক্ষর<br>
                    <small style="font-weight: 400;">Buyer's Signature</small>
                </div>
            </div>
        </div>

        <!-- Offline Voucher Image -->
        @if($sale->voucher_image)
        <div class="section" style="margin-top: 15px; border: 2px dashed #d1d5db; padding: 10px; background: #f9fafb;">
            <div class="section-title" style="text-align: center; margin-bottom: 8px; color: {{ $primaryColor }};">
                📸 অফলাইন ভাউচার ছবি / Offline Voucher Image
            </div>
            <div style="text-align: center;">
                <img src="{{ asset('storage/' . $sale->voucher_image) }}" 
                     alt="Voucher Image" 
                     style="max-width: 100%; max-height: 300px; border: 1px solid #d1d5db; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            @if($template && $template->footer_text)
                <div class="footer-highlight">
                    {{ $template->footer_text }}
                </div>
            @endif
            <div>এই ভাউচারটি কম্পিউটার দ্বারা তৈরি এবং স্বাক্ষরের প্রয়োজন নেই।</div>
            <div>This is a computer-generated invoice and does not require a signature.</div>
            <div style="margin-top: 8px; font-weight: 600;">মুদ্রণের সময় / Printed: {{ now()->format('d/m/Y h:i A') }}</div>
        </div>
    </div>
</body>
</html>

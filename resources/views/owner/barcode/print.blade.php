<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcodes</title>
    <style>
        @page {
            size: auto;
            margin: 5mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
        }
        
        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            gap: 3mm;
            padding: 5mm;
        }
        
        .barcode-label {
            border: 1px solid #000;
            text-align: center;
            padding: 2mm;
            background: white;
            page-break-inside: avoid;
        }
        
        /* 20x10mm - Mini */
        .label-20x10 {
            width: 20mm;
            height: 10mm;
        }
        .label-20x10 .product-name {
            font-size: 5pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-20x10 .barcode-svg {
            height: 6mm;
        }
        .label-20x10 .barcode-text {
            font-size: 4pt;
            margin-top: 0.5mm;
        }
        .label-20x10 .price {
            font-size: 5pt;
            font-weight: bold;
            margin-top: 0.5mm;
        }
        
        /* 30x20mm - Small */
        .label-30x20 {
            width: 30mm;
            height: 20mm;
        }
        .label-30x20 .product-name {
            font-size: 6pt;
            font-weight: bold;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-30x20 .barcode-svg {
            height: 8mm;
        }
        .label-30x20 .barcode-text {
            font-size: 5pt;
            margin-top: 0.5mm;
        }
        .label-30x20 .price {
            font-size: 7pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        
        /* 40x30mm - Medium */
        .label-40x30 {
            width: 40mm;
            height: 30mm;
        }
        .label-40x30 .product-name {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-40x30 .barcode-svg {
            height: 12mm;
        }
        .label-40x30 .barcode-text {
            font-size: 6pt;
            margin-top: 1mm;
        }
        .label-40x30 .price {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        
        /* 50x30mm - Standard */
        .label-50x30 {
            width: 50mm;
            height: 30mm;
        }
        .label-50x30 .product-name {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-50x30 .barcode-svg {
            height: 14mm;
        }
        .label-50x30 .barcode-text {
            font-size: 7pt;
            margin-top: 1mm;
        }
        .label-50x30 .price {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        
        /* 60x40mm - Large */
        .label-60x40 {
            width: 60mm;
            height: 40mm;
        }
        .label-60x40 .product-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        .label-60x40 .barcode-svg {
            height: 18mm;
        }
        .label-60x40 .barcode-text {
            font-size: 8pt;
            margin-top: 1mm;
        }
        .label-60x40 .price {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 2mm;
        }
        
        /* 70x50mm - Extra Large */
        .label-70x50 {
            width: 70mm;
            height: 50mm;
        }
        .label-70x50 .product-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        .label-70x50 .barcode-svg {
            height: 22mm;
        }
        .label-70x50 .barcode-text {
            font-size: 10pt;
            margin-top: 1mm;
        }
        .label-70x50 .price {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 2mm;
        }
        
        /* 100x50mm - Wide */
        .label-100x50 {
            width: 100mm;
            height: 50mm;
        }
        .label-100x50 .product-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        .label-100x50 .barcode-svg {
            height: 25mm;
        }
        .label-100x50 .barcode-text {
            font-size: 12pt;
            margin-top: 1mm;
        }
        .label-100x50 .price {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 2mm;
        }
        
        .barcode-svg svg {
            width: 100%;
            height: 100%;
        }
        
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none;
            }
        }
        
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            background: white;
            padding: 15px;
            border: 2px solid #333;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .print-controls button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .btn-print {
            background: #4CAF50;
            color: white;
        }
        
        .btn-close {
            background: #f44336;
            color: white;
        }
    </style>
    
    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print Labels</button>
        <button class="btn-close" onclick="window.close()">✖ Close</button>
    </div>

    <div class="barcode-container">
        @foreach($products as $item)
            @for($i = 0; $i < $item['quantity']; $i++)
                <div class="barcode-label label-{{ $labelSize }}">
                    @if($includeName)
                        <div class="product-name">{{ Str::limit($item['product']->name, 30) }}</div>
                    @endif
                    
                    <div class="barcode-svg">
                        <svg class="barcode-{{ $item['product']->id }}-{{ $i }}"></svg>
                    </div>
                    
                    <div class="barcode-text">{{ $item['product']->barcode ?? $item['product']->sku ?? sprintf('%08d', $item['product']->id) }}</div>
                    
                    @if($includePrice)
                        <div class="price">৳{{ number_format($item['product']->sell_price, 2) }}</div>
                    @endif
                </div>
            @endfor
        @endforeach
    </div>

    <script>
        // Generate barcodes using JsBarcode
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($products as $item)
                @for($i = 0; $i < $item['quantity']; $i++)
                    try {
                        const barcodeValue = '{{ $item['product']->barcode ?? $item['product']->sku ?? sprintf('%08d', $item['product']->id) }}';
                        JsBarcode(".barcode-{{ $item['product']->id }}-{{ $i }}", barcodeValue, {
                            format: "CODE128",
                            width: 2,
                            height: 40,
                            displayValue: false,
                            margin: 2
                        });
                    } catch (e) {
                        console.error('Barcode generation error:', e);
                    }
                @endfor
            @endforeach
            
            // Auto-print dialog after barcodes are generated
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>

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
        
        /* Small Label: 40x25mm */
        .label-small {
            width: 40mm;
            height: 25mm;
        }
        .label-small .product-name {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-small .barcode-svg {
            height: 10mm;
        }
        .label-small .barcode-text {
            font-size: 6pt;
            margin-top: 1mm;
        }
        .label-small .price {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        
        /* Medium Label: 50x30mm */
        .label-medium {
            width: 50mm;
            height: 30mm;
        }
        .label-medium .product-name {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-medium .barcode-svg {
            height: 12mm;
        }
        .label-medium .barcode-text {
            font-size: 7pt;
            margin-top: 1mm;
        }
        .label-medium .price {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        
        /* Large Label: 60x40mm */
        .label-large {
            width: 60mm;
            height: 40mm;
        }
        .label-large .product-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        .label-large .barcode-svg {
            height: 15mm;
        }
        .label-large .barcode-text {
            font-size: 8pt;
            margin-top: 1mm;
        }
        .label-large .price {
            font-size: 12pt;
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

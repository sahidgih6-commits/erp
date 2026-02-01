<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcodes</title>
    <style>
        /* Page size matching sticker dimensions */
        @page {
            margin: 0mm;
            padding: 0mm;
            @if($labelSize == '20x10')
                size: 20mm 10mm portrait;
            @elseif($labelSize == '30x20')
                size: 30mm 20mm portrait;
            @elseif($labelSize == '40x30')
                size: 40mm 30mm portrait;
            @elseif($labelSize == '45x35')
                size: 45mm 35mm portrait;
            @elseif($labelSize == '50x30')
                size: 50mm 30mm portrait;
            @elseif($labelSize == '60x40')
                size: 60mm 40mm portrait;
            @elseif($labelSize == '70x50')
                size: 70mm 50mm portrait;
            @elseif($labelSize == '100x50')
                size: 100mm 50mm portrait;
            @else
                size: 45mm 35mm portrait;
            @endif
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            padding: 0;
        }
        
        .barcode-label {
            border: none;
            text-align: center;
            padding: 0;
            background: white;
            page-break-after: always;
            break-after: page;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            overflow: visible;
            position: relative;
            @if($labelSize == '20x10')
                width: 20mm;
                height: 10mm;
            @elseif($labelSize == '30x20')
                width: 30mm;
                height: 20mm;
            @elseif($labelSize == '40x30')
                width: 40mm;
                height: 30mm;
            @elseif($labelSize == '45x35')
                width: 45mm;
                height: 35mm;
            @elseif($labelSize == '50x30')
                width: 50mm;
                height: 30mm;
            @elseif($labelSize == '60x40')
                width: 60mm;
                height: 40mm;
            @elseif($labelSize == '70x50')
                width: 70mm;
                height: 50mm;
            @elseif($labelSize == '100x50')
                width: 100mm;
                height: 50mm;
            @else
                width: 45mm;
                height: 35mm;
            @endif
        }
        
        .barcode-content {
            transform: translate({{ $offsetX ?? 0 }}mm, {{ $offsetY ?? 0 }}mm);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .product-name {
            margin: 0;
            padding: 0;
            line-height: 1;
        }
        
        .barcode-svg {
            margin: 0;
            padding: 0;
            line-height: 0;
        }
        
        .barcode-text {
            margin: 0;
            padding: 0;
            line-height: 1;
        }
        
        .price {
            margin: 0;
            padding: 0;
            line-height: 1;
        }
        
        /* 20x10mm - Mini */
        .label-20x10 {
            width: 20mm;
            height: 10mm;
            padding: 0;
        }
        .label-20x10 .product-name {
            font-size: 5pt;
            font-weight: bold;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-20x10 .barcode-svg {
            height: 6mm;
            margin: 0;
        }
        .label-20x10 .barcode-text {
            font-size: 4pt;
            margin: 0;
        }
        .label-20x10 .price {
            font-size: 5pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 30x20mm - Small */
        .label-30x20 {
            width: 30mm;
            height: 20mm;
            padding: 0;
        }
        .label-30x20 .product-name {
            font-size: 6pt;
            font-weight: bold;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-30x20 .barcode-svg {
            height: 10mm;
            margin: 0;
        }
        .label-30x20 .barcode-text {
            font-size: 5pt;
            margin: 0;
        }
        .label-30x20 .price {
            font-size: 6pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 40x30mm - Medium */
        .label-40x30 {
            width: 40mm;
            height: 30mm;
            padding: 0;
        }
        .label-40x30 .product-name {
            font-size: 7pt;
            font-weight: bold;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-40x30 .barcode-svg {
            height: 15mm;
            margin: 0;
        }
        .label-40x30 .barcode-text {
            font-size: 6pt;
            margin: 0;
        }
        .label-40x30 .price {
            font-size: 7pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 45x35mm - Your Custom Size */
        .label-45x35 {
            width: 45mm;
            height: 35mm;
            padding: 0;
        }
        .label-45x35 .product-name {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-45x35 .barcode-svg {
            height: 18mm;
            margin: 0;
        }
        .label-45x35 .barcode-text {
            font-size: 7pt;
            margin: 0;
        }
        .label-45x35 .price {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 50x30mm - Standard */
        .label-50x30 {
            width: 50mm;
            height: 30mm;
            padding: 0;
        }
        .label-50x30 .product-name {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .label-50x30 .barcode-svg {
            height: 16mm;
            margin: 0;
        }
        .label-50x30 .barcode-text {
            font-size: 7pt;
            margin: 0;
        }
        .label-50x30 .price {
            font-size: 9pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 60x40mm - Large */
        .label-60x40 {
            width: 60mm;
            height: 40mm;
            padding: 0;
        }
        .label-60x40 .product-name {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
        }
        .label-60x40 .barcode-svg {
            height: 20mm;
            margin: 0;
        }
        .label-60x40 .barcode-text {
            font-size: 8pt;
            margin: 0;
        }
        .label-60x40 .price {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 70x50mm - Extra Large */
        .label-70x50 {
            width: 70mm;
            height: 50mm;
            padding: 0;
        }
        .label-70x50 .product-name {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
        }
        .label-70x50 .barcode-svg {
            height: 25mm;
            margin: 0;
        }
        .label-70x50 .barcode-text {
            font-size: 10pt;
            margin: 0;
        }
        .label-70x50 .price {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
        }
        
        /* 100x50mm - Wide */
        .label-100x50 {
            width: 100mm;
            height: 50mm;
            padding: 0;
        }
        .label-100x50 .product-name {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }
        .label-100x50 .barcode-svg {
            height: 28mm;
            margin: 0;
        }
        .label-100x50 .barcode-text {
            font-size: 12pt;
            margin: 0;
        }
        .label-100x50 .price {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
        }
        
        .barcode-svg svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        
        @media print {
            @page {
                margin: 0mm;
            }
            html, body {
                background: white;
                margin: 0mm !important;
                padding: 0mm !important;
                width: 100%;
                height: 100%;
            }
            .no-print {
                display: none !important;
            }
            .barcode-container {
                margin: 0mm !important;
                padding: 0mm !important;
                display: block;
            }
            .barcode-label {
                page-break-after: always;
                break-after: page;
                page-break-inside: avoid;
                break-inside: avoid;
                margin: 0mm !important;
                padding: 0mm !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                overflow: visible !important;
            }
            .barcode-content {
                transform: translate({{ $offsetX ?? 0 }}mm, {{ $offsetY ?? 0 }}mm) !important;
            }
            .barcode-label:last-child {
                page-break-after: auto;
                break-after: auto;
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
                    <div class="barcode-content">
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
                </div>
            @endfor
        @endforeach
    </div>

    <script>
        // Generate barcodes using JsBarcode
        document.addEventListener('DOMContentLoaded', function() {
            const labelSize = '{{ $labelSize }}';
            
            // Configure barcode settings based on label size
            // Reduced margins for compact printing, but still scannable
            const barcodeSettings = {
                '20x10': { width: 2, height: 20, fontSize: 8, margin: 0.5 },
                '30x20': { width: 2.5, height: 30, fontSize: 10, margin: 1 },
                '40x30': { width: 3, height: 40, fontSize: 12, margin: 1 },
                '45x35': { width: 3, height: 48, fontSize: 13, margin: 1 },
                '50x30': { width: 3, height: 45, fontSize: 12, margin: 1 },
                '60x40': { width: 3.5, height: 55, fontSize: 14, margin: 1.5 },
                '70x50': { width: 4, height: 65, fontSize: 16, margin: 1.5 },
                '100x50': { width: 4.5, height: 75, fontSize: 18, margin: 2 }
            };
            
            const settings = barcodeSettings[labelSize] || barcodeSettings['45x35'];
            
            @foreach($products as $item)
                @for($i = 0; $i < $item['quantity']; $i++)
                    try {
                        const barcodeValue = '{{ $item['product']->barcode ?? $item['product']->sku ?? sprintf('%08d', $item['product']->id) }}';
                        JsBarcode(".barcode-{{ $item['product']->id }}-{{ $i }}", barcodeValue, {
                            format: "CODE128",
                            width: settings.width,
                            height: settings.height,
                            displayValue: false,
                            margin: settings.margin,
                            marginTop: settings.margin,
                            marginBottom: settings.margin,
                            marginLeft: settings.margin,
                            marginRight: settings.margin,
                            fontSize: settings.fontSize,
                            textAlign: "center",
                            textPosition: "bottom",
                            textMargin: 0,
                            background: "#ffffff",
                            lineColor: "#000000",
                            valid: function(valid) {
                                if (!valid) {
                                    console.error('Invalid barcode:', barcodeValue);
                                }
                            }
                        });
                    } catch (e) {
                        console.error('Barcode generation error:', e);
                    }
                @endfor
            @endforeach
            
            // Auto-print dialog after barcodes are generated
            setTimeout(function() {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>

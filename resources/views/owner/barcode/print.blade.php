<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcodes</title>
    <style>
        /* Page size matching sticker dimensions */
        @page {
            margin: 0;
            padding: 0;
            @if($labelSize == '20x10')
                size: 20mm 10mm portrait;
            @elseif($labelSize == '30x20')
                size: 30mm 20mm portrait;
            @elseif($labelSize == '40x30')
                size: 40mm 30mm portrait;
            @elseif($labelSize == '50x30')
                size: 50mm 30mm portrait;
            @elseif($labelSize == '60x40')
                size: 60mm 40mm portrait;
            @elseif($labelSize == '70x50')
                size: 70mm 50mm portrait;
            @elseif($labelSize == '100x50')
                size: 100mm 50mm portrait;
            @else
                size: 50mm 30mm portrait;
            @endif
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            background: white;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .barcode-container {
            display: block;
            margin: 0;
            padding: 0;
        }
        
        .barcode-label {
            border: none;
            text-align: center;
            padding: 1mm;
            background: white;
            page-break-after: always;
            break-after: page;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            overflow: hidden;
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
            display: block;
        }
        
        @media print {
            html, body {
                background: white;
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
            }
            .no-print {
                display: none !important;
            }
            .barcode-container {
                margin: 0;
                padding: 0;
            }
            .barcode-label {
                page-break-after: always;
                break-after: page;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .barcode-label:last-child {
                page-break-after: auto;
                break-after: auto;
            }
            
            /* Ensure proper orientation for thermal printers */
            @page {
                @if($labelSize == '20x10')
                    size: 20mm 10mm portrait;
                @elseif($labelSize == '30x20')
                    size: 30mm 20mm portrait;
                @elseif($labelSize == '40x30')
                    size: 40mm 30mm portrait;
                @elseif($labelSize == '50x30')
                    size: 50mm 30mm portrait;
                @elseif($labelSize == '60x40')
                    size: 60mm 40mm portrait;
                @elseif($labelSize == '70x50')
                    size: 70mm 50mm portrait;
                @elseif($labelSize == '100x50')
                    size: 100mm 50mm portrait;
                @endif
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
            const labelSize = '{{ $labelSize }}';
            
            // Configure barcode settings based on label size
            const barcodeSettings = {
                '20x10': { width: 1, height: 15, fontSize: 8 },
                '30x20': { width: 1.5, height: 25, fontSize: 10 },
                '40x30': { width: 2, height: 35, fontSize: 12 },
                '50x30': { width: 2, height: 40, fontSize: 12 },
                '60x40': { width: 2.5, height: 50, fontSize: 14 },
                '70x50': { width: 2.5, height: 60, fontSize: 16 },
                '100x50': { width: 3, height: 70, fontSize: 18 }
            };
            
            const settings = barcodeSettings[labelSize] || barcodeSettings['50x30'];
            
            @foreach($products as $item)
                @for($i = 0; $i < $item['quantity']; $i++)
                    try {
                        const barcodeValue = '{{ $item['product']->barcode ?? $item['product']->sku ?? sprintf('%08d', $item['product']->id) }}';
                        JsBarcode(".barcode-{{ $item['product']->id }}-{{ $i }}", barcodeValue, {
                            format: "CODE128",
                            width: settings.width,
                            height: settings.height,
                            displayValue: false,
                            margin: 0,
                            marginTop: 0,
                            marginBottom: 0,
                            marginLeft: 2,
                            marginRight: 2,
                            fontSize: settings.fontSize,
                            textAlign: "center",
                            textPosition: "bottom",
                            textMargin: 1,
                            background: "#ffffff",
                            lineColor: "#000000"
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

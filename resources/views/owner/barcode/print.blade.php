<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcodes - {{ $labelSize }}</title>
    
    <!-- QZ Tray for Direct Printing -->
    <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2/qz-tray.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-sha256@0.9.0/build/sha256.min.js"></script>
    
    <style>
        /* Page size - Fixed to 45mm x 35mm for all labels */
        @page {
            size: 45mm 35mm portrait;
            margin: 0mm;
            padding: 0mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            margin: 0;
            padding: 0;
            width: 45mm;
            height: 35mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 0;
            padding: 0;
            width: 45mm;
            height: 35mm;
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
            width: 45mm;
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
            width: 45mm;
            height: 35mm;
            margin-bottom: {{ $stickerGap ?? 0 }}mm;
        }
        
        .barcode-content {
            transform: translate({{ $offsetX ?? 0 }}mm, {{ $offsetY ?? 0 }}mm);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* Shrink by abs(offset) so shifted content never overflows paper edge */
            max-width: calc(100% - {{ abs($offsetX ?? 0) }}mm);
            max-height: calc(100% - {{ abs($offsetY ?? 0) }}mm);
            overflow: hidden;
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
        
        .paper-size-warning {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            z-index: 999;
            max-width: 450px;
            font-size: 14px;
            line-height: 1.6;
            border: 3px solid white;
        }
        
        .paper-size-warning strong {
            display: block;
            font-size: 20px;
            margin-bottom: 12px;
            text-align: center;
        }
        
        .paper-size-warning code {
            background: rgba(255,255,255,0.25);
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .paper-size-warning ol {
            margin: 15px 0 15px 20px;
            text-align: left;
        }
        
        .paper-size-warning li {
            margin: 8px 0;
        }
        
        .paper-size-warning button {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            margin-top: 15px;
            width: 100%;
        }
        
        .paper-size-warning button:hover {
            background: #f0f0f0;
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
                margin: 0mm 0mm {{ $stickerGap ?? 0 }}mm 0mm !important;
                padding: 0mm !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                overflow: visible !important;
            }
            .barcode-content {
                transform: translate({{ $offsetX ?? 0 }}mm, {{ $offsetY ?? 0 }}mm) !important;
                max-width: calc(100% - {{ abs($offsetX ?? 0) }}mm) !important;
                max-height: calc(100% - {{ abs($offsetY ?? 0) }}mm) !important;
                overflow: hidden !important;
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
        
        .paper-size-warning {
            position: fixed;
            top: 60px;
            right: 10px;
            background: #ff9800;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            z-index: 999;
            max-width: 300px;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .paper-size-warning strong {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .paper-size-warning code {
            background: rgba(0,0,0,0.2);
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
    
    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="paper-size-warning no-print">
        <strong>⚠️ IMPORTANT: Set Paper Size!</strong>
        In print dialog, change "Paper size" to:<br>
        @if($labelSize == '20x10')
            <code>20mm × 10mm</code>
        @elseif($labelSize == '30x20')
            <code>30mm × 20mm</code>
        @elseif($labelSize == '40x30')
            <code>40mm × 30mm</code>
        @elseif($labelSize == '45x35')
            <code>45mm × 35mm</code>
        @elseif($labelSize == '50x30')
            <code>50mm × 30mm</code>
        @elseif($labelSize == '60x40')
            <code>60mm × 40mm</code>
        @elseif($labelSize == '70x50')
            <code>70mm × 50mm</code>
        @elseif($labelSize == '100x50')
            <code>100mm × 50mm</code>
        @else
            <code>45mm × 35mm</code>
        @endif
        <br><small>or configure custom size in printer settings</small>
    </div>
    
    <div class="print-controls no-print">
        <button class="btn-print" onclick="printWithQZ()" style="background: #4CAF50;">🖨️ Print to Rongta RP400H</button>
        <button class="btn-print" onclick="checkQZStatus()" style="background: #FF9800;">🔍 Check QZ Status</button>
        <button class="btn-print" onclick="window.print()" style="background: #2196F3;">🖨️ Browser Print (Fallback)</button>
        <button class="btn-close" onclick="window.close()">✖ Close</button>
    </div>

    <div class="paper-size-warning no-print" id="sizeWarning">
        <strong>⚠️ IMPORTANT: Set Paper Size</strong>
        <div style="text-align: center; margin: 15px 0;">
            Required size: <code>45mm × 35mm</code>
        </div>
        <ol>
            <li>Press <strong>Ctrl+P</strong> to open print dialog</li>
            <li>Click <strong>"More settings"</strong></li>
            <li>Under <strong>"Paper size"</strong>, select your Rongta 45×35mm option</li>
            <li>If not available, configure custom size in printer properties</li>
        </ol>
        <button onclick="document.getElementById('sizeWarning').style.display='none'; window.print();">
            Got it! Open Print Dialog
        </button>
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
        });
        
        // QZ Tray Integration for Professional Printing
        function checkQZStatus() {
            let status = '🔍 QZ TRAY STATUS CHECK\n\n';
            
            // Check if QZ library loaded
            if (typeof qz === 'undefined') {
                status += '❌ QZ Tray Library: NOT LOADED\n';
                status += '   → Check internet connection\n';
                status += '   → Refresh the page\n\n';
            } else {
                status += '✅ QZ Tray Library: LOADED (v' + qz.version + ')\n\n';
                
                // Check if QZ Tray application is running
                if (qz.websocket.isActive()) {
                    status += '✅ QZ Tray App: RUNNING\n\n';
                    
                    // Try to get printers
                    qz.printers.find().then(function(printers) {
                        status += '🖨️ DETECTED PRINTERS:\n';
                        if (printers.length === 0) {
                            status += '   ❌ No printers found!\n\n';
                            status += '📝 TROUBLESHOOTING:\n';
                            status += '   1. Install Rongta RP400H driver\n';
                            status += '   2. Connect USB cable\n';
                            status += '   3. Power ON printer (24V)\n';
                            status += '   4. Check Windows "Devices and Printers"\n';
                        } else {
                            printers.forEach((p, i) => {
                                let isRongta = p.toLowerCase().includes('rongta') || p.toLowerCase().includes('rp400');
                                status += '   ' + (i + 1) + '. ' + p + (isRongta ? ' ✅ (Detected!)' : '') + '\n';
                            });
                        }
                        alert(status);
                    }).catch(function(err) {
                        status += '❌ Error getting printers: ' + err + '\n';
                        alert(status);
                    });
                    return;
                } else {
                    status += '❌ QZ Tray App: NOT RUNNING\n\n';
                    status += '📥 SETUP STEPS:\n';
                    status += '   1. Download: https://qz.io/download/\n';
                    status += '   2. Install QZ Tray\n';
                    status += '   3. Start QZ Tray application\n';
                    status += '   4. Look for QZ icon in system tray\n';
                }
            }
            
            alert(status);
        }
        
        function printWithQZ() {
            console.log('QZ Tray version:', typeof qz !== 'undefined' ? qz.version : 'Not loaded');
            
            if (typeof qz === 'undefined') {
                alert('QZ Tray library not loaded!\n\nPlease:\n1. Check your internet connection\n2. Refresh the page\n\nFalling back to browser print...');
                window.print();
                return;
            }
            
            if (!qz.websocket.isActive()) {
                qz.websocket.connect().then(function() {
                    console.log('✅ QZ Tray connected successfully');
                    findPrinter();
                }).catch(function(err) {
                    console.error('QZ Tray connection error:', err);
                    alert('❌ QZ Tray Not Running!\n\n📥 SETUP INSTRUCTIONS:\n\n1. Download QZ Tray:\n   → https://qz.io/download/\n\n2. Install QZ Tray application\n\n3. Start QZ Tray:\n   → Look for QZ icon in system tray (bottom-right)\n   → If not running, open QZ Tray from Start menu\n\n4. Install Rongta RP400H Driver:\n   → Connect USB cable\n   → Power ON printer (24V adapter)\n   → Windows should auto-detect\n   → Or download from Rongta website\n\n⚠️ After setup, refresh this page and try again.\n\n🖨️ For now, using browser print...');
                    window.print();
                });
            } else {
                console.log('✅ QZ Tray already connected');
                findPrinter();
            }
        }
        
        function findPrinter() {
            qz.printers.find().then(function(printers) {
                console.log('Available printers:', printers);
                
                // Look for Rongta RP400H specifically
                let printer = printers.find(p => {
                    const name = p.toLowerCase();
                    return name.includes('rp400h') || 
                           name.includes('rp-400h') ||
                           name.includes('rp400') || 
                           name.includes('rp-400') ||
                           name.includes('rongta');
                });
                
                // If RP400H not found, look for any thermal/label printer
                if (!printer) {
                    printer = printers.find(p => {
                        const name = p.toLowerCase();
                        return name.includes('thermal') ||
                               name.includes('label') ||
                               name.includes('barcode') ||
                               name.includes('transfer');
                    });
                }
                
                // If still not found, show selection dialog
                if (!printer && printers.length > 0) {
                    let printerList = '🖨️ Select Your Rongta RP400H Printer:\n\n';
                    printers.forEach((p, i) => {
                        printerList += (i + 1) + '. ' + p + '\n';
                    });
                    printerList += '\nEnter printer number (1-' + printers.length + '):';
                    
                    let selection = prompt(printerList);
                    if (selection && !isNaN(selection)) {
                        let index = parseInt(selection) - 1;
                        if (index >= 0 && index < printers.length) {
                            printer = printers[index];
                        }
                    }
                }
                
                if (!printer && printers.length > 0) {
                    printer = printers[0];
                }
                
                if (printer) {
                    console.log('Selected printer:', printer);
                    if (confirm('Print ' + {{ count($products) }} + ' label(s) to:\n' + printer + '?')) {
                        printLabels(printer);
                    }
                } else {
                    alert('❌ Rongta RP400H Not Found!\n\n✅ Please check:\n   1. Printer is powered ON (24V adapter connected)\n   2. USB cable connected to computer\n   3. Rongta RP400H driver installed\n   4. Printer shows as "Ready" in Windows Devices\n\n📥 Download driver: Search "Rongta RP400H driver" online\n\n⚠️ Falling back to browser print...');
                    window.print();
                }
            }).catch(function(err) {
                console.error('Printer detection error:', err);
                alert('Could not detect printers.\nPlease install QZ Tray from https://qz.io/download/\n\nFalling back to browser print...');
                window.print();
            });
        }
        
        function printLabels(printer) {
            const labelSize = '{{ $labelSize }}';
            const offsetX   = {{ $offsetX ?? 0 }};
            const offsetY   = {{ $offsetY ?? 0 }};
            const parts  = labelSize.split('x');
            const labelW = parseFloat(parts[0]) || 50;
            const labelH = parseFloat(parts[1]) || 30;
            const pageH  = labelH; // always labelH — Rongta gap sensor handles physical spacing

            // Proportional font sizes (pt) relative to label height
            const namePt   = Math.max(6,  +(labelH * 0.22).toFixed(1));
            const codePt   = Math.max(5,  +(labelH * 0.17).toFixed(1));
            const pricePt  = Math.max(7,  +(labelH * 0.25).toFixed(1));
            const barcodeH = +(labelH * 0.52).toFixed(1);
            const contentW = +Math.max(15, (labelW - 2 - Math.abs(offsetX))).toFixed(1);

            let config = qz.configs.create(printer, {
                density: 203,
                size: { width: labelW, height: pageH, units: 'mm' },
                margins: { top: 0, right: 0, bottom: 0, left: 0, units: 'mm' },
                orientation: 'portrait',
                scaleContent: true
            });

            // offsetX / offsetY used EXACTLY as shown in the canvas preview — no gap adjustment
            const printCss = [
                '* { margin:0; padding:0; box-sizing:border-box; }',
                '@page { size:' + labelW + 'mm ' + pageH + 'mm; margin:0; }',
                'html,body { width:' + labelW + 'mm; height:' + pageH + 'mm; background:white; }',
                'body { font-family:Arial,sans-serif; display:flex; align-items:center; justify-content:center; overflow:hidden; }',
                '.barcode-label { width:' + labelW + 'mm; height:' + labelH + 'mm;',
                '  display:flex; flex-direction:column; justify-content:center; align-items:center;',
                '  background:white; overflow:hidden; }',
                '.barcode-content { transform:translate(' + offsetX + 'mm,' + offsetY + 'mm) !important;',
                '  display:flex; flex-direction:column; align-items:center; justify-content:center;',
                '  max-width:' + contentW + 'mm; }',
                '.product-name { font-size:' + namePt + 'pt; font-weight:bold; margin-bottom:0.5mm;',
                '  white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:' + contentW + 'mm; text-align:center; }',
                '.barcode-svg { height:' + barcodeH + 'mm; width:auto; display:block; margin:0 auto; }',
                '.barcode-svg svg { height:100% !important; width:auto !important; display:block; }',
                '.barcode-text { font-size:' + codePt + 'pt; margin-top:0.3mm; text-align:center; }',
                '.price { font-size:' + pricePt + 'pt; font-weight:bold; margin-top:0.3mm; text-align:center; }'
            ].join(' ');

            let printData = [];
            document.querySelectorAll('.barcode-label').forEach(function(label) {
                printData.push({
                    type: 'pixel',
                    format: 'html',
                    flavor: 'plain',
                    data: '<!DOCTYPE html><html><head><meta charset="UTF-8">' +
                          '<style>' + printCss + '</style></head>' +
                          '<body>' + label.outerHTML + '</body></html>'
                });
            });

            qz.print(config, printData).then(function() {
                alert('Labels printed successfully!');
            }).catch(function(err) {
                console.error(err);
                alert('Print failed! Using browser print...');
                window.print();
            });
        }
    </script>
</body>
</html>

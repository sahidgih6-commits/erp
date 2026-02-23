@extends('layouts.app')

@section('title', 'Barcode Printer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">🏷️ {{ __('pos.barcode_printer') ?? 'Barcode Sticker Printer' }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('pos.print_barcode_labels') ?? 'Print barcode labels for your products' }}</p>
                </div>
                <a href="{{ route('owner.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    {{ __('pos.back') }}
                </a>
            </div>
        </div>
        
        <!-- Hardware Status -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Barcode Printer Status</h3>
                        @if($barcodePrinter)
                            <p class="text-sm {{ $barcodePrinter->is_connected ? 'text-green-600' : 'text-red-600' }}">
                                <span class="inline-block w-2 h-2 {{ $barcodePrinter->is_connected ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-2"></span>
                                {{ $barcodePrinter->device_name }} - {{ $barcodePrinter->is_connected ? 'Connected' : 'Disconnected' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $barcodePrinter->brand ?? 'Generic' }} {{ $barcodePrinter->model ?? '' }} | {{ $barcodePrinter->connection_type }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">
                                <span class="inline-block w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                No printer configured
                            </p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('pos.hardware.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    ⚙️ Configure Hardware
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form id="barcodeForm" action="{{ route('owner.barcode.generate') }}" method="POST" target="_blank">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Selection -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">{{ __('pos.select_products') ?? 'Select Products' }}</h2>
                        
                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text" 
                                   id="productSearch" 
                                   placeholder="{{ __('pos.search_products') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Product List -->
                        <div class="space-y-2 max-h-96 overflow-y-auto" id="productList">
                            @foreach($products as $product)
                                <div class="product-item border border-gray-200 rounded-lg p-3 hover:bg-blue-50 transition" 
                                     data-name="{{ strtolower($product->name) }}"
                                     data-sku="{{ strtolower($product->sku ?? '') }}"
                                     data-barcode="{{ strtolower($product->barcode ?? '') }}">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" 
                                               class="product-checkbox w-5 h-5 text-blue-600"
                                               data-product-id="{{ $product->id }}"
                                               data-product-name="{{ $product->name }}"
                                               data-product-sku="{{ $product->sku }}"
                                               data-product-barcode="{{ $product->barcode }}"
                                               data-product-price="{{ $product->sell_price }}">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                SKU: {{ $product->sku ?? 'N/A' }} | 
                                                Barcode: {{ $product->barcode ?? 'Auto' }} | 
                                                Price: ৳{{ $product->sell_price }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">{{ __('pos.quantity') }}:</label>
                                            <input type="number" 
                                                   class="quantity-input w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                                   data-product-id="{{ $product->id }}"
                                                   value="1" 
                                                   min="1"
                                                   disabled>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($products->isEmpty())
                            <p class="text-center text-gray-500 py-8">{{ __('pos.no_products') ?? 'No products found' }}</p>
                        @endif
                    </div>
                </div>

                <!-- Settings & Preview -->
                <div class="lg:col-span-1">
                  <div class="sticky top-4 space-y-4">

                    <!-- ── STICKER SIZE ── -->
                    <div class="bg-white rounded-2xl shadow p-5">
                      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Sticker Size</p>
                      <select name="label_size" id="labelSizeSelect"
                              class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500"
                              required onchange="rebuildCanvas()">
                        <option value="20x10"  data-width="20"  data-height="10">20 × 10 mm — Mini</option>
                        <option value="30x20"  data-width="30"  data-height="20">30 × 20 mm — Small</option>
                        <option value="38x24"  data-width="38"  data-height="24" selected>38 × 24 mm — Your Roll</option>
                        <option value="40x30"  data-width="40"  data-height="30">40 × 30 mm — Medium</option>
                        <option value="45x35"  data-width="45"  data-height="35">45 × 35 mm — Custom</option>
                        <option value="50x30"  data-width="50"  data-height="30">50 × 30 mm — Standard</option>
                        <option value="60x40"  data-width="60"  data-height="40">60 × 40 mm — Large</option>
                        <option value="70x50"  data-width="70"  data-height="50">70 × 50 mm — XL</option>
                        <option value="100x50" data-width="100" data-height="50">100 × 50 mm — Wide</option>
                      </select>
                      <div class="mt-2 flex items-center gap-2 text-xs text-blue-700 bg-blue-50 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
                        <span>Selected: <strong id="sizeInfo">38mm × 24mm</strong> — ensure roll matches</span>
                      </div>
                    </div>

                    <!-- ── LIVE PREVIEW — ALL STICKERS ── -->
                    <div class="bg-gray-900 rounded-2xl shadow-xl p-5">
                      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Print Preview  <span class="text-gray-600 normal-case font-normal">— all selected stickers</span></p>
                      <p class="text-xs text-gray-500 mb-3">White = printable area &nbsp;·&nbsp; Gray = sticker border &nbsp;·&nbsp; Hatched = gap between stickers</p>

                      <!-- Scrollable sticker strip -->
                      <div id="stickerStrip" class="flex flex-col items-center overflow-y-auto" style="max-height:420px; scrollbar-width:thin;">
                        <!-- Stickers will be injected here by JS -->
                        <div class="text-gray-600 text-xs py-8">Select products to see preview</div>
                      </div>

                      <!-- Offset readout badges -->
                      <div class="flex justify-center gap-3 mt-3">
                        <div class="flex items-center gap-1 bg-gray-800 rounded-lg px-3 py-1">
                          <span class="text-gray-400 text-xs">X</span>
                          <span id="offsetXDisplay" class="text-white font-mono text-xs font-bold">0mm</span>
                        </div>
                        <div class="flex items-center gap-1 bg-gray-800 rounded-lg px-3 py-1">
                          <span class="text-gray-400 text-xs">Y</span>
                          <span id="offsetYDisplay" class="text-white font-mono text-xs font-bold">0mm</span>
                        </div>
                        <div class="flex items-center gap-1 bg-gray-800 rounded-lg px-3 py-1">
                          <span class="text-gray-400 text-xs">Gap</span>
                          <span id="gapDisplay" class="text-yellow-400 font-mono text-xs font-bold">0mm</span>
                        </div>
                      </div>
                    </div>

                    <!-- ── POSITION FINE-TUNE ── -->
                    <div class="bg-white rounded-2xl shadow p-5">
                      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Position Fine-Tune</p>

                      <!-- D-Pad -->
                      <div class="grid grid-cols-3 gap-1.5 w-36 mx-auto mb-4">
                        <div></div>
                        <button type="button" onclick="nudge(0,-1)"
                          class="aspect-square rounded-xl bg-gray-100 hover:bg-blue-100 active:bg-blue-200 flex items-center justify-center text-gray-700 font-bold text-lg transition">▲</button>
                        <div></div>
                        <button type="button" onclick="nudge(-1,0)"
                          class="aspect-square rounded-xl bg-gray-100 hover:bg-blue-100 active:bg-blue-200 flex items-center justify-center text-gray-700 font-bold text-lg transition">◀</button>
                        <button type="button" onclick="resetPosition()"
                          class="aspect-square rounded-xl bg-red-50 hover:bg-red-100 active:bg-red-200 flex items-center justify-center text-red-500 font-bold transition"
                          title="Reset to center">⊙</button>
                        <button type="button" onclick="nudge(1,0)"
                          class="aspect-square rounded-xl bg-gray-100 hover:bg-blue-100 active:bg-blue-200 flex items-center justify-center text-gray-700 font-bold text-lg transition">▶</button>
                        <div></div>
                        <button type="button" onclick="nudge(0,1)"
                          class="aspect-square rounded-xl bg-gray-100 hover:bg-blue-100 active:bg-blue-200 flex items-center justify-center text-gray-700 font-bold text-lg transition">▼</button>
                        <div></div>
                      </div>

                      <!-- Step size selector -->
                      <div class="flex items-center justify-center gap-2">
                        <span class="text-xs text-gray-500">Step:</span>
                        <div class="flex gap-1">
                          <button type="button" onclick="setStep(0.5)"  id="step-0.5" class="step-btn px-2 py-1 rounded-lg border text-xs font-mono transition">0.5</button>
                          <button type="button" onclick="setStep(1)"    id="step-1"   class="step-btn px-2 py-1 rounded-lg border text-xs font-mono transition active">1</button>
                          <button type="button" onclick="setStep(2)"    id="step-2"   class="step-btn px-2 py-1 rounded-lg border text-xs font-mono transition">2</button>
                          <button type="button" onclick="setStep(5)"    id="step-5"   class="step-btn px-2 py-1 rounded-lg border text-xs font-mono transition">5</button>
                        </div>
                        <span class="text-xs text-gray-500">mm</span>
                      </div>

                      <input type="hidden" name="offset_x" id="offsetX" value="0">
                      <input type="hidden" name="offset_y" id="offsetY" value="0">
                    </div>

                    <!-- ── SIDE MARGINS (Left & Right) ── -->
                    <div class="bg-white rounded-2xl shadow p-5">
                      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Side Margins (Left &amp; Right)</p>
                      <p class="text-xs text-gray-500 mb-3">Equal inset from both sides — use when sticker has a physical border or gutters</p>
                      <div class="flex items-center gap-3">
                        <button type="button" onclick="nudgeSideMargin(-0.5)"
                          class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-red-100 text-gray-700 text-xl font-bold flex items-center justify-center transition">−</button>
                        <div class="flex-1">
                          <input type="range" id="sideMarginSlider" min="0" max="10" step="0.5" value="0"
                                 class="w-full accent-indigo-500"
                                 oninput="setSideMargin(parseFloat(this.value))">
                          <div class="flex justify-between text-xs text-gray-400 mt-0.5">
                            <span>0mm</span><span>5mm</span><span>10mm</span>
                          </div>
                        </div>
                        <button type="button" onclick="nudgeSideMargin(0.5)"
                          class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-green-100 text-gray-700 text-xl font-bold flex items-center justify-center transition">+</button>
                      </div>
                      <div class="flex justify-between mt-2">
                        <span class="text-xs text-gray-500">Each side: <strong id="sideMarginDisplay" class="text-indigo-600">0mm</strong></span>
                        <button type="button" onclick="setSideMargin(0)" class="text-xs text-gray-400 hover:text-gray-600">Reset</button>
                      </div>
                      <input type="hidden" name="side_margin" id="sideMarginInput" value="0">
                    </div>

                    <!-- ── GAP BETWEEN STICKERS ── -->
                    <div class="bg-white rounded-2xl shadow p-5">
                      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Gap Between Stickers</p>
                      <p class="text-xs text-gray-500 mb-3">Positive = content shifts UP on label to compensate printer over-advance</p>

                      <div class="flex items-center gap-3">
                        <button type="button" onclick="nudgeGap(-1)"
                          class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-red-100 text-gray-700 text-xl font-bold flex items-center justify-center transition">−</button>

                        <div class="flex-1">
                          <input type="range" id="gapSlider" min="-10" max="10" step="1" value="0"
                                 class="w-full accent-yellow-500"
                                 oninput="setGap(parseInt(this.value))">
                          <div class="flex justify-between text-xs text-gray-400 mt-0.5">
                            <span>-10mm</span><span>0</span><span>+10mm</span>
                          </div>
                        </div>

                        <button type="button" onclick="nudgeGap(1)"
                          class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-green-100 text-gray-700 text-xl font-bold flex items-center justify-center transition">+</button>
                      </div>

                      <button type="button" onclick="setGap(0)"
                        class="mt-3 w-full py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs text-gray-600 font-medium transition">Reset Gap to 0</button>

                      <input type="hidden" name="sticker_gap" id="stickerGap" value="0">
                    </div>

                    <!-- ── INCLUDE OPTIONS ── -->
                    <div class="bg-white rounded-2xl shadow p-5">
                      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Content Options</p>
                      <label class="flex items-center gap-3 cursor-pointer group mb-3">
                        <div class="relative">
                          <input type="checkbox" name="include_name" id="includeName" value="1" checked class="sr-only peer">
                          <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                          <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition"></div>
                        </div>
                        <span class="text-sm text-gray-700">Include Product Name</span>
                      </label>
                      <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                          <input type="checkbox" name="include_price" id="includePrice" value="1" checked class="sr-only peer">
                          <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                          <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition"></div>
                        </div>
                        <span class="text-sm text-gray-700">Include Price</span>
                      </label>
                    </div>

                    <!-- ── SUMMARY + ACTIONS ── -->
                    <div class="bg-white rounded-2xl shadow p-5">
                      <div class="flex items-center justify-between mb-4">
                        <div>
                          <p class="text-xs text-gray-500">{{ __('pos.selected_products') ?? 'Products' }}</p>
                          <p class="text-3xl font-black text-blue-600"><span id="selectedCount">0</span></p>
                        </div>
                        <div class="text-right">
                          <p class="text-xs text-gray-500">Total Labels</p>
                          <p class="text-3xl font-black text-gray-800"><span id="totalLabels">0</span></p>
                        </div>
                      </div>

                      <div id="selectedProducts"></div>

                      <button type="submit" id="printButton"
                              class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-xl shadow-lg shadow-blue-200 disabled:opacity-40 disabled:cursor-not-allowed transition text-sm"
                              disabled>
                        🖨️ {{ __('pos.generate_print') ?? 'Generate & Print' }}
                      </button>

                      <div class="grid grid-cols-2 gap-2 mt-2">
                        <button type="button" onclick="selectAll()"
                          class="py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium rounded-xl text-sm transition">
                          ✅ Select All
                        </button>
                        <button type="button" onclick="clearSelection()"
                          class="py-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-xl text-sm transition">
                          ✕ Clear
                        </button>
                      </div>
                    </div>

                  </div><!-- /sticky -->
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    /* ─── State ─────────────────────────────────────── */
    let offsetX  = 0, offsetY  = 0, gap = 0, stepMm = 1;
    let labelWmm = 45, labelHmm = 35;
    const CANVAS_W = 236; // fixed canvas pixel width

    /* ─── Canvas builders ────────────────────────────── */
    function getScale() { return CANVAS_W / labelWmm; }

    function rebuildCanvas() {
        const sel = document.getElementById('labelSizeSelect');
        const opt = sel.options[sel.selectedIndex];
        labelWmm = parseFloat(opt.dataset.width);
        labelHmm = parseFloat(opt.dataset.height);
        document.getElementById('sizeInfo').textContent = labelWmm + 'mm × ' + labelHmm + 'mm';
        renderStickerStrip();
    }

    function placeContent() {
        // Now handled by renderStickerStrip
        renderStickerStrip();
    }

    function applyGapZone() {
        document.getElementById('gapDisplay').textContent = gap + 'mm';
        renderStickerStrip();
    }

    function renderStickerStrip() {
        const strip = document.getElementById('stickerStrip');
        const scale = getScale();
        const sW = CANVAS_W;
        const sH = Math.round(labelHmm * scale);
        const showName  = document.getElementById('includeName').checked;
        const showPrice = document.getElementById('includePrice').checked;
        const gapPx = Math.max(0, Math.round(gap * scale));

        // Gather all selected products × quantity
        const items = [];
        document.querySelectorAll('.product-checkbox:checked').forEach(cb => {
            const qty = parseInt(document.querySelector(`input.quantity-input[data-product-id="${cb.dataset.productId}"]`).value) || 1;
            for (let i = 0; i < qty; i++) {
                items.push({
                    name: cb.dataset.productName || 'Product',
                    code: cb.dataset.productBarcode || cb.dataset.productSku || '00000000',
                    price: 'Tk' + parseFloat(cb.dataset.productPrice || 0).toFixed(2)
                });
            }
        });

        if (items.length === 0) {
            strip.innerHTML = '<div class="text-gray-600 text-xs py-8">Select products to see preview</div>';
            return;
        }

        // Sizing
        const fName  = Math.max(7, Math.round(sH * 0.09));
        const fCode  = Math.max(5, fName - 2);
        const fPrice = Math.max(7, fName);
        const barcodeH = Math.round(sH * 0.38);
        const sidePx = Math.round(sideMargin * scale);
        const contentW = sW - 12 - sidePx * 2; // 6px backing each side + sideMargin

        let html = '';
        items.forEach((item, idx) => {
            // Sticker
            html += `<div style="width:${sW}px; flex-shrink:0;">`;
            // Backing (gray border = physical sticker edge)
            html += `<div style="background:#e5e7eb; border-radius:6px; padding:4px ${6 + sidePx}px; box-shadow:0 1px 4px rgba(0,0,0,.25);">`;
            // White printable area
            html += `<div style="background:white; border-radius:3px; width:100%; height:${sH}px; display:flex; flex-direction:column; align-items:center; justify-content:center; overflow:hidden; position:relative;">`;
            // Content with offset
            html += `<div style="transform:translate(${offsetX * scale}px, ${offsetY * scale}px); display:flex; flex-direction:column; align-items:center; justify-content:center; max-width:${contentW}px; overflow:hidden;">`;
            if (showName) {
                html += `<div style="font-size:${fName}px; font-weight:bold; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:${contentW}px;">${item.name}</div>`;
            }
            // Barcode placeholder
            html += `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 40" style="display:block; height:${barcodeH}px; width:auto; max-width:${contentW}px; margin:1px 0;">`;
            html += '<rect x="0" width="3" height="40" fill="#000"/><rect x="5" width="2" height="40" fill="#000"/><rect x="9" width="4" height="40" fill="#000"/><rect x="15" width="1" height="40" fill="#000"/>';
            html += '<rect x="18" width="3" height="40" fill="#000"/><rect x="23" width="2" height="40" fill="#000"/><rect x="27" width="4" height="40" fill="#000"/><rect x="33" width="1" height="40" fill="#000"/>';
            html += '<rect x="36" width="2" height="40" fill="#000"/><rect x="40" width="3" height="40" fill="#000"/><rect x="45" width="1" height="40" fill="#000"/><rect x="48" width="4" height="40" fill="#000"/>';
            html += '<rect x="54" width="2" height="40" fill="#000"/><rect x="58" width="3" height="40" fill="#000"/><rect x="63" width="1" height="40" fill="#000"/><rect x="66" width="2" height="40" fill="#000"/>';
            html += '<rect x="70" width="4" height="40" fill="#000"/><rect x="76" width="1" height="40" fill="#000"/><rect x="79" width="3" height="40" fill="#000"/><rect x="84" width="2" height="40" fill="#000"/>';
            html += '<rect x="88" width="1" height="40" fill="#000"/><rect x="91" width="4" height="40" fill="#000"/><rect x="97" width="2" height="40" fill="#000"/><rect x="101" width="3" height="40" fill="#000"/>';
            html += '<rect x="106" width="1" height="40" fill="#000"/><rect x="109" width="2" height="40" fill="#000"/><rect x="113" width="4" height="40" fill="#000"/><rect x="119" width="1" height="40" fill="#000"/>';
            html += '</svg>';
            html += `<div style="font-size:${fCode}px; color:#6b7280;">${item.code}</div>`;
            if (showPrice) {
                html += `<div style="font-size:${fPrice}px; font-weight:bold; color:#111827;">${item.price}</div>`;
            }
            html += '</div>'; // content
            html += '</div>'; // white area
            html += '</div>'; // backing
            html += '</div>'; // sticker wrapper

            // Gap between stickers (not after last one)
            if (idx < items.length - 1 && gapPx > 0) {
                html += `<div style="width:${sW}px; height:${gapPx}px; background:repeating-linear-gradient(45deg,#374151 0,#374151 3px,transparent 3px,transparent 10px); border-left:1px dashed #4b5563; border-right:1px dashed #4b5563; display:flex; align-items:center; justify-content:center;">`;
                html += `<span style="font-size:8px; color:#9ca3af; background:rgba(31,41,55,.7); padding:0 4px; border-radius:3px;">${gap}mm</span>`;
                html += '</div>';
            }
        });

        strip.innerHTML = html;
    }

    /* ─── Controls ───────────────────────────────────── */
    function nudge(dx, dy) {
        offsetX = +(offsetX + dx * stepMm).toFixed(1);
        offsetY = +(offsetY + dy * stepMm).toFixed(1);
        saveAndRender();
    }

    function setStep(s) {
        stepMm = s;
        document.querySelectorAll('.step-btn').forEach(b => {
            b.classList.toggle('bg-blue-600', b.id === 'step-' + s);
            b.classList.toggle('text-white',  b.id === 'step-' + s);
            b.classList.toggle('border-blue-600', b.id === 'step-' + s);
            b.classList.toggle('bg-white',    b.id !== 'step-' + s);
            b.classList.toggle('text-gray-600', b.id !== 'step-' + s);
        });
    }

    function resetPosition() {
        offsetX = 0; offsetY = 0;
        saveAndRender();
        localStorage.removeItem('barcode_offset_x');
        localStorage.removeItem('barcode_offset_y');
    }

    function nudgeGap(d) { setGap(gap + d); }

    function setGap(v) {
        gap = Math.max(-10, Math.min(10, v));
        document.getElementById('stickerGap').value  = gap;
        document.getElementById('gapSlider').value   = gap;
        document.getElementById('gapDisplay').textContent = gap + 'mm';
        localStorage.setItem('barcode_sticker_gap', gap);
        renderStickerStrip();
    }

    let sideMargin = 0;
    function nudgeSideMargin(d) { setSideMargin(sideMargin + d); }
    function setSideMargin(v) {
        sideMargin = Math.max(0, Math.min(10, +parseFloat(v).toFixed(1)));
        document.getElementById('sideMarginInput').value   = sideMargin;
        document.getElementById('sideMarginSlider').value  = sideMargin;
        document.getElementById('sideMarginDisplay').textContent = sideMargin + 'mm';
        localStorage.setItem('barcode_side_margin', sideMargin);
        renderStickerStrip();
    }

    function saveAndRender() {
        document.getElementById('offsetX').value = offsetX;
        document.getElementById('offsetY').value = offsetY;
        document.getElementById('offsetXDisplay').textContent = offsetX + 'mm';
        document.getElementById('offsetYDisplay').textContent = offsetY + 'mm';
        localStorage.setItem('barcode_offset_x', offsetX);
        localStorage.setItem('barcode_offset_y', offsetY);
        placeContent();
    }

    /* ─── Product list ───────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function() {
        // restore saved values
        offsetX = parseFloat(localStorage.getItem('barcode_offset_x') || 0);
        offsetY = parseFloat(localStorage.getItem('barcode_offset_y') || 0);
        gap     = parseFloat(localStorage.getItem('barcode_sticker_gap') || 0);

        document.getElementById('offsetX').value = offsetX;
        document.getElementById('offsetY').value = offsetY;
        document.getElementById('stickerGap').value = gap;
        document.getElementById('gapSlider').value  = gap;

        const savedSM = parseFloat(localStorage.getItem('barcode_side_margin') || 0);
        sideMargin = savedSM;
        document.getElementById('sideMarginInput').value  = savedSM;
        document.getElementById('sideMarginSlider').value = savedSM;
        document.getElementById('sideMarginDisplay').textContent = savedSM + 'mm';

        setStep(1);
        rebuildCanvas();
        saveAndRender();
        setGap(gap);
        setSideMargin(savedSM);
        updateSelection();

        // watch toggles
        document.getElementById('includeName').addEventListener('change', renderStickerStrip);
        document.getElementById('includePrice').addEventListener('change', renderStickerStrip);
    });

    // Search
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(p => {
                p.classList.toggle('hidden',
                    !p.dataset.name.includes(q) &&
                    !p.dataset.sku.includes(q) &&
                    !p.dataset.barcode.includes(q));
            });
        });

        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                document.querySelector(`input.quantity-input[data-product-id="${this.dataset.productId}"]`).disabled = !this.checked;
                updateSelection();
            });
        });

        document.querySelectorAll('.quantity-input').forEach(inp => inp.addEventListener('change', updateSelection));
    });

    function updateSelection() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        let total = 0;
        const div = document.getElementById('selectedProducts');
        div.innerHTML = '';
        checked.forEach((cb, i) => {
            const qty = parseInt(document.querySelector(`input.quantity-input[data-product-id="${cb.dataset.productId}"]`).value) || 1;
            total += qty;
            div.innerHTML += `<input type="hidden" name="products[${i}][id]" value="${cb.dataset.productId}">
                              <input type="hidden" name="products[${i}][quantity]" value="${qty}">`;
        });
        document.getElementById('selectedCount').textContent = checked.length;
        document.getElementById('totalLabels').textContent   = total;
        document.getElementById('printButton').disabled = checked.length === 0;

        // Rebuild the sticker strip preview
        renderStickerStrip();
    }

    function selectAll() {
        document.querySelectorAll('.product-item:not(.hidden) .product-checkbox').forEach(cb => {
            cb.checked = true;
            document.querySelector(`input.quantity-input[data-product-id="${cb.dataset.productId}"]`).disabled = false;
        });
        updateSelection();
    }

    function clearSelection() {
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.checked = false;
            const qi = document.querySelector(`input.quantity-input[data-product-id="${cb.dataset.productId}"]`);
            qi.disabled = true; qi.value = 1;
        });
        updateSelection();
    }

    // rebuild canvas on resize
    window.addEventListener('resize', rebuildCanvas);
</script>
@endsection

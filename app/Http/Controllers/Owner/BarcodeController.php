<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    /**
     * Display barcode printing interface.
     */
    public function index()
    {
        $business = Auth::user()->business;
        $systemVersion = $business->systemVersion;

        // Check if POS is enabled
        if (!$systemVersion || !$systemVersion->isPOSEnabled()) {
            return redirect()->route('owner.dashboard')
                ->with('error', 'Barcode printing is only available when POS system is enabled.');
        }
        
        // Get barcode printer hardware if configured
        $barcodePrinter = $business->hardwareDevices()
            ->where('device_type', 'thermal_printer')
            ->where('is_enabled', true)
            ->first();

        $products = $business->products()
            ->select('id', 'name', 'sku', 'barcode', 'sell_price', 'current_stock')
            ->orderBy('name')
            ->get();

        return view('owner.barcode.index', compact('products', 'barcodePrinter', 'systemVersion'));
    }

    /**
     * Generate barcode labels for selected products.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'label_size' => 'required|in:20x10,30x20,40x30,50x30,60x40,70x50,100x50',
            'include_price' => 'boolean',
            'include_name' => 'boolean',
        ]);

        $business = Auth::user()->business;
        $systemVersion = $business->systemVersion;

        // Check if POS is enabled
        if (!$systemVersion || !$systemVersion->isPOSEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode printing is only available when POS system is enabled.'
            ], 403);
        }

        $selectedProducts = collect($request->products)->map(function ($item) use ($business) {
            $product = $business->products()->find($item['id']);
            return [
                'product' => $product,
                'quantity' => $item['quantity']
            ];
        })->filter(function ($item) {
            return $item['product'] !== null;
        });

        return view('owner.barcode.print', [
            'products' => $selectedProducts,
            'labelSize' => $request->label_size,
            'includePrice' => $request->boolean('include_price', true),
            'includeName' => $request->boolean('include_name', true),
        ]);
    }

    /**
     * Quick print barcode for single product.
     */
    public function quickPrint(Product $product, Request $request)
    {
        // Ensure product belongs to user's business
        if ($product->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized');
        }

        $quantity = $request->input('quantity', 1);
        $labelSize = $request->input('label_size', '50x30');

        $selectedProducts = collect([
            [
                'product' => $product,
                'quantity' => $quantity
            ]
        ]);

        return view('owner.barcode.print', [
            'products' => $selectedProducts,
            'labelSize' => $labelSize,
            'includePrice' => true,
            'includeName' => true,
        ]);
    }
}

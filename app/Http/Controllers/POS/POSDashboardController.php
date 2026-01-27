<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\HardwareDevice;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\ReceiptPrint;
use App\Models\SystemVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSDashboardController extends Controller
{
    /**
     * Display the main POS dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        // Get system version
        $systemVersion = $business->systemVersion()->first() 
            ?? SystemVersion::create([
                'business_id' => $business->id,
                'version' => 'basic',
            ]);

        // Get available hardware devices
        $barcodeScanner = $business->hardwareDevices()
            ->where('device_type', 'barcode_scanner')
            ->where('is_enabled', true)
            ->first();

        $thermalPrinter = $business->hardwareDevices()
            ->where('device_type', 'thermal_printer')
            ->where('is_enabled', true)
            ->first();

        $cashDrawer = $business->hardwareDevices()
            ->where('device_type', 'cash_drawer')
            ->where('is_enabled', true)
            ->first();

        // Get today's sales summary
        $todaysSales = $business->posTransactions()
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->get();

        $todaysSalesTotal = $todaysSales->sum('total');
        $todaysSalesCount = $todaysSales->count();

        // Get products for quick access
        $products = $business->products()
            ->where('current_stock', '>', 0)
            ->select('id', 'name', 'sku', 'sell_price', 'current_stock')
            ->limit(50)
            ->get();

        return view('pos.dashboard', [
            'business' => $business,
            'systemVersion' => $systemVersion,
            'barcodeScanner' => $barcodeScanner,
            'thermalPrinter' => $thermalPrinter,
            'cashDrawer' => $cashDrawer,
            'todaysSalesTotal' => $todaysSalesTotal,
            'todaysSalesCount' => $todaysSalesCount,
            'products' => $products,
            'hardwareStatus' => [
                'barcode_scanner' => $barcodeScanner?->getStatusLabel(),
                'thermal_printer' => $thermalPrinter?->getStatusLabel(),
                'cash_drawer' => $cashDrawer?->getStatusLabel(),
            ],
        ]);
    }

    /**
     * Show the POS billing interface.
     */
    public function billing()
    {
        $user = Auth::user();
        $business = $user->business;

        $systemVersion = $business->systemVersion()->first();

        $barcodeScanner = $business->hardwareDevices()
            ->where('device_type', 'barcode_scanner')
            ->where('is_enabled', true)
            ->first();

        $thermalPrinter = $business->hardwareDevices()
            ->where('device_type', 'thermal_printer')
            ->where('is_enabled', true)
            ->first();

        $cashDrawer = $business->hardwareDevices()
            ->where('device_type', 'cash_drawer')
            ->where('is_enabled', true)
            ->first();

        $products = $business->products()
            ->select('id', 'name', 'sku', 'sell_price', 'current_stock')
            ->get();

        return view('pos.billing', [
            'business' => $business,
            'systemVersion' => $systemVersion,
            'barcodeScanner' => $barcodeScanner,
            'thermalPrinter' => $thermalPrinter,
            'cashDrawer' => $cashDrawer,
            'products' => $products,
            'canScanBarcode' => $systemVersion && $systemVersion->canUseBarcodeScanner(),
            'canPrintReceipt' => $systemVersion && $systemVersion->canUseThermalPrinter(),
            'canOpenDrawer' => $systemVersion && $systemVersion->canUseCashDrawer(),
        ]);
    }

    /**
     * Create a new POS transaction.
     */
    public function createTransaction(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile',
            'amount_tendered' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $business = $user->business;

        try {
            DB::beginTransaction();

            // Create POS transaction
            $transaction = $business->posTransactions()->create([
                'user_id' => $user->id,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'total' => $request->total,
                'payment_method' => $request->payment_method,
                'amount_tendered' => $request->amount_tendered,
                'change' => max(0, $request->amount_tendered - $request->total),
                'status' => 'completed',
                'items' => $request->items,
                'notes' => $request->notes,
                'completed_at' => now(),
            ]);

            // Reduce stock for each item
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('current_stock', $item['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('pos.transaction_saved'),
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Print receipt for a transaction.
     */
    public function printReceipt(Request $request, POSTransaction $transaction)
    {
        $request->validate([
            'paper_size' => 'required|in:58mm,80mm',
        ]);

        // Check if user has permission to print
        if ($transaction->user_id !== Auth::id() && !Auth::user()->hasRole('manager|superadmin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Create receipt print record
            $receipt = $transaction->receiptPrints()->create([
                'business_id' => $transaction->business_id,
                'paper_size' => $request->paper_size,
                'status' => 'pending',
            ]);

            // In production, this would send to actual printer
            // For now, mark as completed
            $receipt->markAsPrinted();

            // Update transaction
            $transaction->update(['receipt_printed' => true]);

            return response()->json([
                'success' => true,
                'message' => __('pos.print_successful'),
                'receipt' => $receipt,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('pos.print_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Open cash drawer.
     */
    public function openDrawer(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        // Check if user has permission
        if (!$user->can('open_cash_drawer')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $cashDrawer = $business->hardwareDevices()
                ->where('device_type', 'cash_drawer')
                ->where('is_enabled', true)
                ->firstOrFail();

            // Log the action
            $business->hardwareAuditLogs()->create([
                'user_id' => $user->id,
                'hardware_device_id' => $cashDrawer->id,
                'device_type' => 'cash_drawer',
                'action' => 'open_drawer',
                'status' => 'success',
                'logged_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('pos.drawer_opened'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search products by barcode or SKU.
     */
    public function searchProduct(Request $request)
    {
        $query = $request->input('q') ?: $request->input('query');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query required',
                'products' => []
            ]);
        }

        $business = Auth::user()->business;

        $products = $business->products()
            ->where(function ($q) use ($query) {
                $q->where('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'sku', 'barcode', 'sell_price', 'current_stock')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
            'count' => $products->count()
        ]);
    }

    /**
     * Get today's transaction summary.
     */
    public function getSummary()
    {
        $business = Auth::user()->business;

        $summary = [
            'total_sales' => $business->posTransactions()
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->sum('total'),
            'transaction_count' => $business->posTransactions()
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'cash_sales' => $business->posTransactions()
                ->where('status', 'completed')
                ->where('payment_method', 'cash')
                ->whereDate('completed_at', today())
                ->sum('total'),
            'card_sales' => $business->posTransactions()
                ->where('status', 'completed')
                ->where('payment_method', 'card')
                ->whereDate('completed_at', today())
                ->sum('total'),
            'mobile_sales' => $business->posTransactions()
                ->where('status', 'completed')
                ->where('payment_method', 'mobile')
                ->whereDate('completed_at', today())
                ->sum('total'),
        ];

        return response()->json($summary);
    }

    /**
     * Get transaction history.
     */
    public function history()
    {
        $user = Auth::user();
        $business = $user->business;

        $transactions = $business->posTransactions()
            ->with(['user', 'receiptPrints'])
            ->latest('completed_at')
            ->paginate(15);

        return view('pos.history', [
            'transactions' => $transactions,
        ]);
    }
}

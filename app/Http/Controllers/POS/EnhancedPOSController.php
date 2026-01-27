<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CashDrawerSession;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnhancedPOSController extends Controller
{
    public function billing()
    {
        $businessId = $this->getBusinessId();

        // Check if cash drawer session is open
        $activeSession = CashDrawerSession::where('business_id', $businessId)
            ->where('user_id', auth()->id())
            ->open()
            ->first();

        if (!$activeSession) {
            return redirect()->route('pos.cash-drawer.create')
                ->with('warning', 'দয়া করে প্রথমে ক্যাশ ড্রয়ার খুলুন');
        }

        $categories = Category::where('business_id', $businessId)
            ->active()
            ->ordered()
            ->get();

        $products = Product::where('business_id', $businessId)
            ->where('current_stock', '>', 0)
            ->get();

        $customers = \App\Models\Customer::where('business_id', $businessId)
            ->active()
            ->get();

        $paymentMethods = PaymentMethod::where('business_id', $businessId)
            ->active()
            ->ordered()
            ->get();

        // Get hold transactions for recall
        $holdTransactions = Sale::where('user_id', auth()->id())
            ->where('status', 'hold')
            ->latest()
            ->get();

        return view('pos.enhanced-billing', compact('categories', 'products', 'customers', 'paymentMethods', 'holdTransactions', 'activeSession'));
    }

    public function searchProducts(Request $request)
    {
        $businessId = $this->getBusinessId();
        $search = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $query = Product::where('business_id', $businessId)
            ->where('current_stock', '>', 0);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', $search);
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->with('category')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
            'count' => $products->count(),
        ]);
    }

    public function checkout(Request $request)
    {
        $businessId = $this->getBusinessId();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalProfit = 0;

            // Calculate totals
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("পর্যাপ্ত স্টক নেই: {$product->name}");
                }

                $itemTotal = $item['price'] * $item['quantity'];
                $totalAmount += $itemTotal;
                $totalProfit += ($item['price'] - $product->purchase_price) * $item['quantity'];
            }

            // Apply discount
            $discountAmount = $validated['discount_amount'] ?? 0;
            if (($validated['discount_type'] ?? null) === 'percentage' && $discountAmount > 0) {
                $discountAmount = ($totalAmount * $discountAmount) / 100;
            }

            $finalAmount = $totalAmount - $discountAmount;
            $changeAmount = max(0, $validated['paid_amount'] - $finalAmount);

            // Prevent division by zero
            $itemCount = count($validated['items']);
            if ($itemCount === 0) {
                throw new \Exception('কার্টে পণ্য নেই');
            }

            // Create sales and update stock
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemTotal = $item['price'] * $item['quantity'];
                $itemDiscount = $totalAmount > 0 ? (($itemTotal / $totalAmount) * $discountAmount) : 0;

                $sale = Sale::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'sell_price' => $item['price'],
                    'total_amount' => $itemTotal - $itemDiscount,
                    'payment_method' => $validated['payment_method'],
                    'paid_amount' => ($validated['paid_amount'] / $itemCount),
                    'change_amount' => ($changeAmount / $itemCount),
                    'discount_amount' => $itemDiscount,
                    'discount_type' => $validated['discount_type'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'profit' => ($item['price'] - $product->purchase_price) * $item['quantity'],
                    'status' => 'completed',
                    'payment_status' => 'paid',
                ]);

                // Reduce stock
                $product->reduceStock($item['quantity']);
            }

            // Update customer if exists
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
                if ($customer) {
                    $customer->increment('total_purchase', $finalAmount);
                    
                    // Calculate loyalty points (1 point per 100 BDT)
                    $points = floor($finalAmount / 100);
                    $customer->increment('loyalty_points', $points);
                }
            }

            // Update cash drawer session
            $session = CashDrawerSession::where('business_id', $businessId)
                ->where('user_id', auth()->id())
                ->open()
                ->first();

            if ($session) {
                $session->increment('total_sales', $finalAmount);
                $session->increment('transaction_count');

                switch ($validated['payment_method']) {
                    case 'cash':
                        $session->increment('total_cash', $finalAmount);
                        break;
                    case 'card':
                        $session->increment('total_card', $finalAmount);
                        break;
                    default:
                        $session->increment('total_mobile', $finalAmount);
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'বিক্রয় সফলভাবে সম্পন্ন হয়েছে',
                'total' => $finalAmount,
                'change' => $changeAmount,
                'invoice_id' => $sale->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function hold(Request $request)
    {
        $businessId = $this->getBusinessId();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemTotal = $item['price'] * $item['quantity'];
                $totalAmount += $itemTotal;

                Sale::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity' => $item['quantity'],
                    'sell_price' => $item['price'],
                    'total_amount' => $itemTotal,
                    'note' => $validated['note'] ?? 'Hold Transaction',
                    'status' => 'hold',
                    'payment_status' => 'pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'লেনদেন হোল্ডে রাখা হয়েছে',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function recall($id)
    {
        $transaction = Sale::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'hold')
            ->with('product')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
        ]);
    }

    public function cancelHold($id)
    {
        $transaction = Sale::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'hold')
            ->firstOrFail();

        $transaction->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'হোল্ড বাতিল করা হয়েছে',
        ]);
    }

    protected function getBusinessId()
    {
        return auth()->user()->business_id;
    }
}

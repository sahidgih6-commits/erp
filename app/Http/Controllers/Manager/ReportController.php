<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $businessId = $this->getBusinessId();
        
        // Calculate quick stats for dashboard
        $todaySales = Sale::whereHas('product', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $monthlySales = Sale::whereHas('product', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $totalCustomers = Customer::where('business_id', $businessId)->count();
        
        $totalDue = Customer::where('business_id', $businessId)->sum('current_due');
        
        return view('manager.reports.index', compact('todaySales', 'monthlySales', 'totalCustomers', 'totalDue'));
    }

    public function sales(Request $request)
    {
        $businessId = $this->getBusinessId();
        
        $startDate = $request->get('start_date', today()->startOfMonth());
        $endDate = $request->get('end_date', today());

        $sales = Sale::whereHas('product', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('product', 'user', 'customer')
            ->get();

        $totalSales = $sales->sum('total');
        $totalTransactions = $sales->count();
        $averageSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        $totalDue = $sales->sum('due');

        // Group by payment method
        $salesByPaymentMethod = $sales->groupBy('payment_method')->map(function($items, $method) {
            return (object) [
                'payment_method' => $method,
                'count' => $items->count(),
                'total' => $items->sum('total'),
            ];
        })->values();

        // Top selling products
        $topProducts = $sales->groupBy('product_id')->map(function($items) {
            $product = $items->first()->product;
            return (object) [
                'product' => $product,
                'total_quantity' => $items->sum('quantity'),
                'total_sales' => $items->sum('total'),
            ];
        })->sortByDesc('total_sales')->take(10)->values();

        return view('manager.reports.sales', compact(
            'totalSales', 'totalTransactions', 'averageSale', 'totalDue', 
            'salesByPaymentMethod', 'topProducts'
        ));
    }

    public function stock(Request $request)
    {
        $businessId = $this->getBusinessId();
        
        $categoryId = $request->get('category_id');
        $stockStatus = $request->get('stock_status');

        $query = Product::where('business_id', $businessId)->with('category');
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($stockStatus == 'low') {
            $query->where('stock', '<', DB::raw('min_stock_level'));
        } elseif ($stockStatus == 'out') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->paginate(50);
        $categories = \App\Models\Category::where('business_id', $businessId)->get();

        $totalProducts = Product::where('business_id', $businessId)->count();
        $totalStockValue = Product::where('business_id', $businessId)
            ->get()
            ->sum(function($p) {
                return $p->stock * $p->purchase_price;
            });
        $lowStockCount = Product::where('business_id', $businessId)
            ->where('stock', '<', DB::raw('min_stock_level'))
            ->count();
        $outOfStockCount = Product::where('business_id', $businessId)
            ->where('stock', '<=', 0)
            ->count();

        return view('manager.reports.stock', compact(
            'products', 'categories', 'totalProducts', 'totalStockValue', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function profit(Request $request)
    {
        $businessId = $this->getBusinessId();
        
        $startDate = $request->get('start_date', today()->startOfMonth());
        $endDate = $request->get('end_date', today());

        // Sales revenue & cost
        $sales = Sale::whereHas('product', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('product')
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalCost = $sales->sum(function($sale) {
            return $sale->quantity * ($sale->product->purchase_price ?? 0);
        });
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Group by category
        $profitByCategory = $sales->groupBy(function($sale) {
            return $sale->product->category_id;
        })->map(function($items) use ($businessId) {
            $categoryId = $items->first()->product->category_id;
            $category = $categoryId ? \App\Models\Category::find($categoryId) : null;
            $revenue = $items->sum('total');
            $cost = $items->sum(function($sale) {
                return $sale->quantity * ($sale->product->purchase_price ?? 0);
            });
            $profit = $revenue - $cost;
            
            return (object) [
                'name' => $category?->name ?? 'Uncategorized',
                'total_revenue' => $revenue,
                'total_cost' => $cost,
                'total_profit' => $profit,
                'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
            ];
        })->values();

        // Top profitable products
        $topProfitableProducts = $sales->groupBy('product_id')->map(function($items) {
            $product = $items->first()->product;
            $revenue = $items->sum('total');
            $cost = $items->sum(function($sale) {
                return $sale->quantity * ($sale->product->purchase_price ?? 0);
            });
            $profit = $revenue - $cost;
            
            return (object) [
                'name' => $product->name,
                'quantity_sold' => $items->sum('quantity'),
                'total_revenue' => $revenue,
                'total_cost' => $cost,
                'total_profit' => $profit,
                'profit_margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
            ];
        })->sortByDesc('total_profit')->take(10)->values();

        return view('manager.reports.profit', compact(
            'totalRevenue', 'totalCost', 'totalProfit', 'profitMargin', 
            'profitByCategory', 'topProfitableProducts'
        ));
    }

    public function customers(Request $request)
    {
        $businessId = $this->getBusinessId();

        $customers = Customer::where('business_id', $businessId)->get();

        $totalCustomers = $customers->count();
        $activeCustomers = $customers->where('is_active', true)->count();
        $totalDue = $customers->sum('current_due');
        $totalLoyaltyPoints = $customers->sum('loyalty_points');

        $topCustomers = $customers->sortByDesc('total_purchase')->take(10);
        $customersWithDue = Customer::where('business_id', $businessId)
            ->where('current_due', '>', 0)
            ->orderBy('current_due', 'desc')
            ->paginate(20);

        return view('manager.reports.customers', compact(
            'totalCustomers', 'activeCustomers', 'totalDue', 'totalLoyaltyPoints',
            'topCustomers', 'customersWithDue'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'pdf');

        // Implementation for PDF/Excel export
        // This would use packages like dompdf or maatwebsite/excel

        return back()->with('info', 'রিপোর্ট এক্সপোর্ট ফিচার শীঘ্রই আসছে');
    }

    protected function getBusinessId()
    {
        $user = auth()->user();
        
        if ($user->hasRole('superadmin')) {
            abort(403);
        }

        return $user->business_id;
    }
}

<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\ProfitRealization;

class SalesmanController extends Controller
{
    public function dashboard()
    {
        $mySales = Sale::where('user_id', auth()->id())->with('product')->latest()->take(10)->get();
        $todaySales = Sale::where('user_id', auth()->id())->whereDate('created_at', today())->sum('total_amount');
        $todayProfit = Sale::where('user_id', auth()->id())->whereDate('created_at', today())->sum('profit');
        $totalSales = Sale::where('user_id', auth()->id())->count();
        
        // Calculate today's realized profit (profit from paid sales + profit realizations from due payments)
        $todayRealizedProfit = ProfitRealization::whereIn('sale_id', function($query) {
            $query->select('id')->from('sales')->where('user_id', auth()->id());
        })->whereDate('created_at', today())->sum('realized_profit')
        + Sale::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->where('due_amount', 0)
            ->sum('profit');

        return view('salesman.dashboard', compact('mySales', 'todaySales', 'todayProfit', 'todayRealizedProfit', 'totalSales'));
    }
}

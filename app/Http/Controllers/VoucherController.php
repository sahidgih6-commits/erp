<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\VoucherTemplate;
use App\Models\ProfitRealization;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function print($saleId)
    {
        $sale = Sale::with(['product', 'user.business'])->findOrFail($saleId);
        
        // Get all sales with the same voucher number (for multiple product sales)
        $allSales = Sale::with(['product'])
            ->where('voucher_number', $sale->voucher_number)
            ->get();
        
        // Get business from the sale user
        $business = $sale->user->business;
        
        // Get voucher template for the business
        $template = $business ? $business->voucherTemplate : null;
        
        return view('voucher.print', compact('sale', 'allSales', 'template'));
    }

    public function paymentVoucher($profitRealizationId)
    {
        $profitRealization = ProfitRealization::with(['sale.product', 'sale.user.business', 'recordedBy'])
            ->findOrFail($profitRealizationId);
        
        $sale = $profitRealization->sale;
        
        // Get business from the sale user
        $business = $sale->user->business;
        
        // Get voucher template for the business
        $template = $business ? $business->voucherTemplate : null;
        
        return view('voucher.payment-voucher', compact('profitRealization', 'sale', 'template'));
    }
}

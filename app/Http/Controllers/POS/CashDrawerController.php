<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\CashDrawerSession;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashDrawerController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        
        $sessions = CashDrawerSession::where('business_id', $businessId)
            ->with('user')
            ->latest('opened_at')
            ->paginate(20);

        $activeSession = CashDrawerSession::where('business_id', $businessId)
            ->where('user_id', auth()->id())
            ->open()
            ->first();

        return view('pos.cash-drawer.index', compact('sessions', 'activeSession'));
    }

    public function create()
    {
        $businessId = auth()->user()->business_id;

        // Check if user already has an open session
        $activeSession = CashDrawerSession::where('business_id', $businessId)
            ->where('user_id', auth()->id())
            ->open()
            ->first();

        if ($activeSession) {
            return redirect()->route('pos.billing')
                ->with('info', 'আপনার ইতিমধ্যে একটি সক্রিয় সেশন আছে');
        }

        return view('pos.cash-drawer.create');
    }

    public function store(Request $request)
    {
        $businessId = auth()->user()->business_id;

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if user already has an open session
        $activeSession = CashDrawerSession::where('business_id', $businessId)
            ->where('user_id', auth()->id())
            ->open()
            ->first();

        if ($activeSession) {
            return back()->with('error', 'আপনার ইতিমধ্যে একটি সক্রিয় সেশন আছে');
        }

        CashDrawerSession::create([
            'business_id' => $businessId,
            'user_id' => auth()->id(),
            'opening_balance' => $validated['opening_balance'],
            'opened_at' => now(),
            'is_open' => true,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('pos.billing')
            ->with('success', 'ক্যাশ ড্রয়ার সফলভাবে খোলা হয়েছে');
    }

    public function close($id)
    {
        $session = CashDrawerSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->open()
            ->firstOrFail();

        return view('pos.cash-drawer.close', compact('session'));
    }

    public function update(Request $request, $id)
    {
        $session = CashDrawerSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->open()
            ->firstOrFail();

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string',
        ]);

        // Calculate expected balance
        $expectedBalance = $session->opening_balance + $session->total_cash;
        $difference = $validated['closing_balance'] - $expectedBalance;

        $session->update([
            'closing_balance' => $validated['closing_balance'],
            'expected_balance' => $expectedBalance,
            'difference' => $difference,
            'closed_at' => now(),
            'is_open' => false,
            'closing_notes' => $validated['closing_notes'] ?? null,
        ]);

        return redirect()->route('pos.cash-drawer.index')
            ->with('success', 'ক্যাশ ড্রয়ার সফলভাবে বন্ধ করা হয়েছে');
    }

    public function show($id)
    {
        $session = CashDrawerSession::where('id', $id)
            ->where('business_id', auth()->user()->business_id)
            ->with('user')
            ->firstOrFail();

        // Get sales during this session
        $sales = Sale::where('user_id', $session->user_id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $session->opened_at,
                $session->closed_at ?? now()
            ])
            ->with('product', 'customer')
            ->get();

        // Group by payment method
        $salesByPayment = $sales->groupBy('payment_method');

        return view('pos.cash-drawer.show', compact('session', 'sales', 'salesByPayment'));
    }
}

<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $this->getBusinessId();
        
        $query = Customer::where('business_id', $businessId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('with_due')) {
            $query->withDue();
        }

        $customers = $query->latest()->paginate(20);

        return view('manager.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('manager.customers.create');
    }

    public function store(Request $request)
    {
        $businessId = $this->getBusinessId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['business_id'] = $businessId;

        Customer::create($validated);

        return redirect()->route(
            auth()->user()->hasRole('owner') ? 'owner.customers.index' : 'manager.customers.index'
        )->with('success', 'গ্রাহক সফলভাবে যোগ করা হয়েছে');
    }

    public function show(Customer $customer)
    {
        // Ensure customer belongs to the same business
        if ($customer->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to customer from different business.');
        }

        $sales = $customer->sales()
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('manager.customers.show', compact('customer', 'sales'));
    }

    public function edit(Customer $customer)
    {
        // Ensure customer belongs to the same business
        if ($customer->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to customer from different business.');
        }
        
        return view('manager.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Ensure customer belongs to the same business
        if ($customer->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to customer from different business.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $customer->update($validated);

        return redirect()->route(
            auth()->user()->hasRole('owner') ? 'owner.customers.index' : 'manager.customers.index'
        )->with('success', 'গ্রাহক তথ্য সফলভাবে আপডেট করা হয়েছে');
    }

    public function destroy(Customer $customer)
    {
        // Ensure customer belongs to the same business
        if ($customer->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to customer from different business.');
        }

        if ($customer->current_due > 0) {
            return back()->with('error', 'এই গ্রাহকের বকেয়া আছে। প্রথমে বকেয়া পরিশোধ করুন।');
        }

        $customer->delete();

        return back()->with('success', 'গ্রাহক সফলভাবে মুছে ফেলা হয়েছে');
    }

    public function search(Request $request)
    {
        $businessId = $this->getBusinessId();
        $search = $request->get('q', '');

        $customers = Customer::where('business_id', $businessId)
            ->active()
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'phone', 'credit_limit', 'current_due']);

        return response()->json([
            'success' => true,
            'customers' => $customers,
            'count' => $customers->count(),
        ]);
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

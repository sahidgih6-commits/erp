<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SalesmanController extends Controller
{
    public function index()
    {
        $salesmen = User::role('salesman')
            ->where('business_id', auth()->user()->business_id)
            ->with('creator')
            ->latest()
            ->get();
            
        return view('owner.salesmen.index', compact('salesmen'));
    }

    public function create()
    {
        return view('owner.salesmen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]);

        $user->assignRole('salesman');

        return redirect()->route('owner.salesmen.index')->with('success', 'সেলসম্যান সফলভাবে যুক্ত হয়েছে।');
    }

    public function edit(User $salesman)
    {
        // Ensure the salesman belongs to the same business
        if ($salesman->business_id !== auth()->user()->business_id) {
            abort(403);
        }

        return view('owner.salesmen.edit', compact('salesman'));
    }

    public function update(Request $request, User $salesman)
    {
        // Ensure the salesman belongs to the same business
        if ($salesman->business_id !== auth()->user()->business_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $salesman->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
        ]);

        $salesman->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $salesman->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('owner.salesmen.index')->with('success', 'সেলসম্যান সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(User $salesman)
    {
        // Ensure the salesman belongs to the same business
        if ($salesman->business_id !== auth()->user()->business_id) {
            abort(403);
        }

        $salesman->delete();

        return redirect()->route('owner.salesmen.index')->with('success', 'সেলসম্যান সফলভাবে মুছে ফেলা হয়েছে।');
    }
}

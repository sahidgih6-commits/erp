<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\SystemVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = User::role('owner')->with(['creator', 'business'])->latest()->paginate(15);
        return view('superadmin.owners.index', compact('owners'));
    }

    public function create()
    {
        $businesses = Business::where('is_active', true)->get();
        return view('superadmin.owners.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'business_id' => ['required', 'exists:businesses,id'],
        ]);

        $owner = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'created_by' => auth()->id(),
            'business_id' => $validated['business_id'],
        ]);

        $owner->assignRole('owner');

        // Auto-enable POS system for the business if not already enabled
        $business = Business::find($validated['business_id']);
        if ($business && !$business->systemVersion) {
            SystemVersion::create([
                'business_id' => $business->id,
                'version' => 'pro',
                'pos_enabled' => true,
                'barcode_scanner_enabled' => true,
                'thermal_printer_enabled' => true,
                'cash_drawer_enabled' => true,
                'pos_activated_at' => now(),
            ]);
        }

        // Store login credentials in session to show once
        session()->flash('owner_created', [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('superadmin.owners.index')->with('success', 'Owner created successfully.');
    }

    public function edit(User $owner)
    {
        $businesses = Business::where('is_active', true)->get();
        return view('superadmin.owners.edit', compact('owner', 'businesses'));
    }

    public function update(Request $request, User $owner)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$owner->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'business_id' => ['required', 'exists:businesses,id'],
        ]);

        $owner->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $owner->password,
            'business_id' => $validated['business_id'],
        ]);

        return redirect()->route('superadmin.owners.index')->with('success', 'Owner updated successfully.');
    }

    public function destroy(User $owner)
    {
        $owner->delete();
        return redirect()->route('superadmin.owners.index')->with('success', 'Owner deleted successfully.');
    }

    public function toggleDueSystem(User $owner)
    {
        $owner->update([
            'due_system_enabled' => !$owner->due_system_enabled
        ]);

        $status = $owner->due_system_enabled ? 'চালু' : 'বন্ধ';
        return redirect()->route('superadmin.owners.index')->with('success', "বকেয়া সিস্টেম {$status} করা হয়েছে");
    }
}

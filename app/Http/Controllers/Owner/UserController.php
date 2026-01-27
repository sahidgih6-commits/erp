<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of all users (managers, salesmen, cashiers).
     */
    public function index()
    {
        $businessId = auth()->user()->business_id;
        
        // Get all users under this owner
        $managers = User::role('manager')
            ->where('business_id', $businessId)
            ->with('roles')
            ->latest()
            ->get();
        
        $salesmen = User::role('salesman')
            ->where('business_id', $businessId)
            ->with('roles')
            ->latest()
            ->get();
        
        $cashiers = User::role('cashier')
            ->where('business_id', $businessId)
            ->with('roles')
            ->latest()
            ->get();

        // Check if POS is enabled for this business
        $systemVersion = auth()->user()->business->systemVersion;
        $posEnabled = $systemVersion ? $systemVersion->isPOSEnabled() : false;

        return view('owner.users.index', compact('managers', 'salesmen', 'cashiers', 'posEnabled'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Check if POS is enabled for cashier role
        $systemVersion = auth()->user()->business->systemVersion;
        $posEnabled = $systemVersion ? $systemVersion->isPOSEnabled() : false;

        return view('owner.users.create', compact('posEnabled'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'in:manager,salesman,cashier'],
        ]);

        // Check if cashier role is allowed
        if ($validated['role'] === 'cashier') {
            $systemVersion = auth()->user()->business->systemVersion;
            if (!$systemVersion || !$systemVersion->isPOSEnabled()) {
                return back()->withErrors(['role' => 'Cashier role is only available when POS system is enabled. Contact Super Admin.']);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'created_by' => auth()->id(),
            'business_id' => auth()->user()->business_id,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('owner.users.index')
            ->with('success', ucfirst($validated['role']) . ' created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Ensure owner can only edit users from their business
        if ($user->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if POS is enabled for cashier role
        $systemVersion = auth()->user()->business->systemVersion;
        $posEnabled = $systemVersion ? $systemVersion->isPOSEnabled() : false;

        return view('owner.users.edit', compact('user', 'posEnabled'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Ensure owner can only update users from their business
        if ($user->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$user->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'role' => ['required', 'in:manager,salesman,cashier'],
        ]);

        // Check if cashier role is allowed
        if ($validated['role'] === 'cashier') {
            $systemVersion = auth()->user()->business->systemVersion;
            if (!$systemVersion || !$systemVersion->isPOSEnabled()) {
                return back()->withErrors(['role' => 'Cashier role is only available when POS system is enabled. Contact Super Admin.']);
            }
        }

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        // Update role if changed
        $currentRole = $user->roles->first()?->name;
        if ($currentRole !== $validated['role']) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('owner.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Ensure owner can only delete users from their business
        if ($user->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized action.');
        }

        $role = $user->roles->first()?->name ?? 'User';
        $user->delete();

        return redirect()->route('owner.users.index')
            ->with('success', ucfirst($role) . ' deleted successfully.');
    }
}

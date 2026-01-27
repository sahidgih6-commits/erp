<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\SystemVersion;
use App\Models\VoucherTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businesses = Business::with(['owners', 'voucherTemplate'])->paginate(10);
        return view('superadmin.businesses.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.businesses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $business = Business::create($validated);

        // Auto-enable POS system for every new business
        SystemVersion::create([
            'business_id' => $business->id,
            'version' => 'pro',
            'pos_enabled' => true,
            'barcode_scanner_enabled' => true,
            'thermal_printer_enabled' => true,
            'cash_drawer_enabled' => true,
            'pos_activated_at' => now(),
        ]);

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Company created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Business $business)
    {
        return view('superadmin.businesses.edit', compact('business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $business->update($validated);

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Company updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Business $business)
    {
        $business->delete();
        
        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Company deleted successfully!');
    }

    public function editTemplate(Business $business)
    {
        $template = $business->voucherTemplate;
        
        return view('superadmin.businesses.edit-template', compact('business', 'template'));
    }

    public function updateTemplate(Request $request, Business $business)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'header_text' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_size' => 'nullable|string|max:10',
            'page_margin' => 'nullable|string|max:10',
            'logo_url' => 'nullable|url|max:500',
            'show_watermark' => 'boolean',
            'watermark_text' => 'nullable|string|max:50',
        ]);

        // Ensure watermark_text has a default value if not provided
        $validated['watermark_text'] = $validated['watermark_text'] ?? '';
        $validated['business_id'] = $business->id;

        if ($business->voucherTemplate) {
            $business->voucherTemplate->update($validated);
        } else {
            VoucherTemplate::create($validated);
        }

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Voucher template updated successfully!');
    }

    public function addOwner(Business $business)
    {
        return view('superadmin.businesses.add-owner', compact('business'));
    }

    public function storeOwner(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'due_system_enabled' => 'boolean',
        ]);

        $owner = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'created_by' => auth()->id(),
            'business_id' => $business->id,
            'due_system_enabled' => $request->has('due_system_enabled'),
        ]);

        $owner->assignRole('owner');

        // Auto-enable POS system if not already enabled
        if (!$business->systemVersion) {
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

        return redirect()->route('superadmin.businesses.edit', $business)
            ->with('success', 'Owner added successfully!');
    }
}

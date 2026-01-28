<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Business;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $businessId = $this->getBusinessId();
        $products = Product::where('business_id', $businessId)->latest()->get();
        return view('manager.products.index', compact('products'));
    }

    public function create()
    {
        return view('manager.products.create');
    }

    public function store(Request $request)
    {
        $businessId = $this->getBusinessId();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })
            ],
            'image' => ['nullable', 'image', 'max:2048'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'min_stock_level' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Auto-generate barcode if not provided
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $this->generateUniqueBarcode($businessId);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->store($request->file('image'), 'products');
        }

        $validated['business_id'] = $businessId;
        Product::create($validated);

        $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        return redirect()->route($routePrefix . '.products.index')->with('success', 'পণ্য সফলভাবে তৈরি হয়েছে। এখন স্টক পেজ থেকে স্টক যোগ করুন।');
    }

    public function edit(Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        return view('manager.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        $businessId = $this->getBusinessId();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })->ignore($product->id)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })->ignore($product->id)
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'adjust_type' => ['nullable', 'in:increase,decrease'],
            'adjust_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Auto-generate barcode if empty
        if (empty($validated['barcode']) && empty($product->barcode)) {
            $validated['barcode'] = $this->generateUniqueBarcode($businessId);
        }

        // Update product details
        $product->update([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'] ?? $product->barcode,
            'purchase_price' => $validated['purchase_price'],
            'sell_price' => $validated['sell_price'],
        ]);

        // If owner adjusts stock (not counted as sale)
        if (auth()->user()->hasRole('owner') && !empty($validated['adjust_type']) && !empty($validated['adjust_quantity']) && $validated['adjust_quantity'] > 0) {
            if ($validated['adjust_type'] === 'increase') {
                // Increase stock
                $product->increment('current_stock', $validated['adjust_quantity']);
                
                // Create stock entry
                \App\Models\StockEntry::create([
                    'product_id' => $product->id,
                    'quantity' => $validated['adjust_quantity'],
                    'purchase_price' => $validated['purchase_price'],
                    'added_by' => auth()->id(),
                    'business_id' => $businessId,
                ]);
            } elseif ($validated['adjust_type'] === 'decrease') {
                // Decrease stock (correction, not a sale)
                $newStock = $product->current_stock - $validated['adjust_quantity'];
                
                if ($newStock < 0) {
                    return redirect()->back()->withErrors(['adjust_quantity' => 'বর্তমান স্টকের চেয়ে বেশি কমানো সম্ভব নয়।'])->withInput();
                }
                
                $product->decrement('current_stock', $validated['adjust_quantity']);
                
                // Create negative stock entry for record
                \App\Models\StockEntry::create([
                    'product_id' => $product->id,
                    'quantity' => -$validated['adjust_quantity'],
                    'purchase_price' => $validated['purchase_price'],
                    'added_by' => auth()->id(),
                    'business_id' => $businessId,
                ]);
            }
        }

        $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        return redirect()->route($routePrefix . '.products.index')->with('success', 'পণ্য সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        // Check if product has sales or stock
        if ($product->sales()->exists()) {
            return redirect()->back()->with('error', 'এই পণ্যের বিক্রয় রেকর্ড আছে। ডিলিট করা যাবে না।');
        }
        
        if ($product->current_stock > 0) {
            return redirect()->back()->with('error', 'পণ্যে স্টক আছে। প্রথমে স্টক শূন্য করুন।');
        }
        
        $product->delete();
        $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        return redirect()->route($routePrefix . '.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Resolve or provision a business id for the authenticated user so owners can create stock/products.
     */
    private function getBusinessId(): int
    {
        $user = auth()->user();

        if ($user->business_id) {
            return $user->business_id;
        }

        $business = Business::first() ?: Business::create([
            'name' => 'Default Business',
            'owner_name' => $user->name ?? 'Owner',
            'phone' => $user->phone ?? null,
            'address' => 'N/A',
        ]);

        $user->business_id = $business->id;
        $user->save();

        return $business->id;
    }

    /**
     * Generate a unique barcode for a product.
     */
    private function generateUniqueBarcode($businessId): string
    {
        do {
            // Generate 13-digit EAN-13 compatible barcode
            // Format: 2 (custom prefix) + 11 random digits + 1 check digit
            $barcode = '2' . str_pad(mt_rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
            
            // Calculate EAN-13 check digit
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int)$barcode[$i] * (($i % 2 === 0) ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $barcode .= $checkDigit;
            
            // Check if barcode already exists in this business
            $exists = Product::where('business_id', $businessId)
                ->where('barcode', $barcode)
                ->exists();
                
        } while ($exists);
        
        return $barcode;
    }
}

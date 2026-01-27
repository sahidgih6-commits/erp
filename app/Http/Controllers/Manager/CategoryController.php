<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $businessId = $this->getBusinessId();
        $categories = Category::where('business_id', $businessId)
            ->withCount('products')
            ->ordered()
            ->paginate(20);

        return view('manager.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('manager.categories.create');
    }

    public function store(Request $request)
    {
        $businessId = $this->getBusinessId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['business_id'] = $businessId;

        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->store($request->file('image'), 'categories');
        }

        Category::create($validated);

        return redirect()->route(
            auth()->user()->hasRole('owner') ? 'owner.categories.index' : 'manager.categories.index'
        )->with('success', 'ক্যাটাগরি সফলভাবে যোগ করা হয়েছে');
    }

    public function edit(Category $category)
    {
        // Ensure category belongs to the same business
        if ($category->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to category from different business.');
        }
        
        return view('manager.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // Ensure category belongs to the same business
        if ($category->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to category from different business.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                $this->imageService->delete($category->image);
            }
            $validated['image'] = $this->imageService->store($request->file('image'), 'categories');
        }

        $category->update($validated);

        return redirect()->route(
            auth()->user()->hasRole('owner') ? 'owner.categories.index' : 'manager.categories.index'
        )->with('success', 'ক্যাটাগরি সফলভাবে আপডেট করা হয়েছে');
    }

    public function destroy(Category $category)
    {
        // Ensure category belongs to the same business
        if ($category->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to category from different business.');
        }

        if ($category->products()->count() > 0) {
            return back()->with('error', 'এই ক্যাটাগরিতে পণ্য আছে। প্রথমে পণ্যগুলো মুছুন বা অন্য ক্যাটাগরিতে স্থানান্তর করুন।');
        }

        if ($category->image) {
            $this->imageService->delete($category->image);
        }

        $category->delete();

        return back()->with('success', 'ক্যাটাগরি সফলভাবে মুছে ফেলা হয়েছে');
    }

    protected function getBusinessId()
    {
        $user = auth()->user();
        
        if ($user->hasRole('superadmin')) {
            abort(403);
        }

        if ($user->hasRole('owner')) {
            return $user->business_id;
        }

        return $user->business_id;
    }
}

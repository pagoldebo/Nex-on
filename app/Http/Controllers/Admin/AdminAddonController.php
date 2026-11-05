<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAddonController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $tab    = $request->input('tab', 'brands');

        $addonQuery = Addon::latest();
        $brandQuery = Brand::withCount('products')->orderBy('sort_order')->orderBy('name');

        if ($search !== '') {
            $addonQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });

            $brandQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $addons = $addonQuery->paginate(15, ['*'], 'addons_page')->withQueryString();
        $brands = $brandQuery->get();

        return view('admin.addons.index', compact('addons', 'brands', 'search', 'tab'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Addon::create($validated);

        return redirect()->route('admin.addons.index')->with('success', 'Add-on created successfully.');
    }

    public function update(Request $request, Addon $addon): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $addon->update($validated);

        return redirect()->route('admin.addons.index')->with('success', 'Add-on updated successfully.');
    }

    public function toggle(Addon $addon): JsonResponse
    {
        $addon->is_active = ! $addon->is_active;
        $addon->save();

        return response()->json([
            'success'   => true,
            'is_active' => $addon->is_active,
            'message'   => $addon->is_active ? 'Add-on activated' : 'Add-on deactivated',
        ]);
    }

    public function destroy(Addon $addon): RedirectResponse
    {
        $addon->delete();
        return redirect()->route('admin.addons.index', ['tab' => 'addons'])->with('success', 'Add-on deleted successfully.');
    }

    public function storeBrand(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'website'     => 'nullable|url|max:255',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Brand::create($validated);

        return redirect()->route('admin.addons.index', ['tab' => 'brands'])->with('success', 'Brand created successfully.');
    }

    public function updateBrand(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'website'     => 'nullable|url|max:255',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $brand->update($validated);

        return redirect()->route('admin.addons.index', ['tab' => 'brands'])->with('success', 'Brand updated successfully.');
    }

    public function destroyBrand(Brand $brand): RedirectResponse
    {
        $brand->delete();
        return redirect()->route('admin.addons.index', ['tab' => 'brands'])->with('success', 'Brand deleted successfully.');
    }

    public function toggleBrand(Brand $brand): JsonResponse
    {
        $brand->is_active = ! $brand->is_active;
        $brand->save();

        return response()->json([
            'success'   => true,
            'is_active' => $brand->is_active,
            'message'   => $brand->is_active ? 'Brand activated' : 'Brand deactivated',
        ]);
    }
}

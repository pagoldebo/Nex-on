<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(): View
    {
        $flatTree = Category::getFlatTree();
        $categories = Category::with(['parent', 'children'])
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories', 'flatTree'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'slug'       => 'nullable|string|max:100|unique:categories,slug',
            'parent_id'  => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'image'      => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|mimetypes:image/jpeg,image/png,image/webp,image/svg+xml,image/gif|max:5120',
        ]);

        $slug = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['name']);

        // Ensure unique slug
        $baseSlug = $slug;
        $counter  = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            if (strtolower($imageFile->getClientOriginalExtension()) === 'svg' || str_contains($imageFile->getMimeType() ?? '', 'svg')) {
                $sanitizedSvg = \App\Services\HtmlSanitizerService::sanitizeSvg(file_get_contents($imageFile->getRealPath()));
                file_put_contents($imageFile->getRealPath(), $sanitizedSvg);
            }
            $imagePath = $imageFile->store('categories', 'public');
        }

        $category = Category::create([
            'name'       => $validated['name'],
            'slug'       => $slug,
            'image'      => $imagePath,
            'parent_id'  => !empty($validated['parent_id']) ? $validated['parent_id'] : null,
            'sort_order' => $validated['sort_order'] ?? (Category::max('sort_order') + 1),
        ]);

        $typeMsg = $category->parent_id ? "Subcategory '{$category->name}' (under '{$category->parent->name}')" : "Category '{$category->name}'";

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "{$typeMsg} created successfully.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'slug'         => 'nullable|string|max:100|unique:categories,slug,' . $category->id,
            'parent_id'    => 'nullable|exists:categories,id',
            'sort_order'   => 'nullable|integer|min:0',
            'image'        => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|mimetypes:image/jpeg,image/png,image/webp,image/svg+xml,image/gif|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        // Prevent cyclical parent assignment
        if (!empty($validated['parent_id'])) {
            $parentId = (int) $validated['parent_id'];
            if ($parentId === (int) $category->id) {
                return redirect()
                    ->route('admin.categories.index')
                    ->withErrors(['parent_id' => 'A category cannot be its own parent.'])
                    ->with('error', 'A category cannot be its own parent.');
            }
            if (in_array($parentId, $category->getAllDescendantIds())) {
                return redirect()
                    ->route('admin.categories.index')
                    ->withErrors(['parent_id' => 'A category cannot have one of its own subcategories as its parent.'])
                    ->with('error', 'A category cannot have one of its own subcategories as its parent.');
            }
        }

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : ($category->slug ?: Str::slug($validated['name']));

        $updateData = [
            'name'       => $validated['name'],
            'slug'       => $slug,
            'parent_id'  => !empty($validated['parent_id']) ? $validated['parent_id'] : null,
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
        ];

        if ($request->boolean('remove_image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $updateData['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $imageFile = $request->file('image');
            if (strtolower($imageFile->getClientOriginalExtension()) === 'svg' || str_contains($imageFile->getMimeType() ?? '', 'svg')) {
                $sanitizedSvg = \App\Services\HtmlSanitizerService::sanitizeSvg(file_get_contents($imageFile->getRealPath()));
                file_put_contents($imageFile->getRealPath(), $sanitizedSvg);
            }
            $updateData['image'] = $imageFile->store('categories', 'public');
        }

        $category->update($updateData);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Cannot delete '{$category->name}' because it contains {$category->products()->count()} products. Reassign or delete the products first.");
        }

        if ($category->children()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Cannot delete '{$category->name}' because it contains {$category->children()->count()} subcategories. Please reassign or delete its subcategories first.");
        }

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$name}' deleted successfully.");
    }
}

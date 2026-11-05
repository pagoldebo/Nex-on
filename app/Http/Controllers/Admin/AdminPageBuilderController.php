<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPageBuilderController extends Controller
{
    /**
     * List all custom visual pages.
     */
    public function index(): View
    {
        $pages = CustomPage::latest()->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the visual block page builder.
     */
    public function create(): View
    {
        $products = Product::where('is_active', true)->select('id', 'name', 'sku')->take(50)->get();
        return view('admin.pages.builder', [
            'page'     => new CustomPage(['is_published' => true]),
            'isEdit'   => false,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created custom page with blocks.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:150',
            'slug'             => 'nullable|string|max:150|unique:custom_pages,slug',
            'meta_title'       => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:300',
            'is_published'     => 'nullable|boolean',
            'content_blocks'   => 'nullable|array',
        ]);

        $validated['slug'] = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['title']) . '-' . Str::random(4);

        $validated['is_published'] = $request->has('is_published');
        $validated['content_blocks'] = $request->input('content_blocks', []);

        $page = CustomPage::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', "Page '{$page->title}' published successfully! Available at /p/{$page->slug}");
    }

    /**
     * Edit an existing visual page.
     */
    public function edit(CustomPage $page): View
    {
        $products = Product::where('is_active', true)->select('id', 'name', 'sku')->take(50)->get();
        return view('admin.pages.builder', [
            'page'     => $page,
            'isEdit'   => true,
            'products' => $products,
        ]);
    }

    /**
     * Update existing visual page.
     */
    public function update(Request $request, CustomPage $page): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:150',
            'slug'             => 'required|string|max:150|unique:custom_pages,slug,' . $page->id,
            'meta_title'       => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:300',
            'is_published'     => 'nullable|boolean',
            'content_blocks'   => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_published'] = $request->has('is_published');
        $validated['content_blocks'] = $request->input('content_blocks', []);

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', "Page '{$page->title}' updated successfully!");
    }

    /**
     * Toggle page published visibility.
     */
    public function togglePublish(CustomPage $page): RedirectResponse
    {
        $page->update(['is_published' => !$page->is_published]);
        $status = $page->is_published ? 'published' : 'drafted';
        return back()->with('success', "Page '{$page->title}' is now {$status}.");
    }

    /**
     * Delete custom page.
     */
    public function destroy(CustomPage $page): RedirectResponse
    {
        $title = $page->title;
        $page->delete();
        return back()->with('success', "Page '{$title}' removed.");
    }
}

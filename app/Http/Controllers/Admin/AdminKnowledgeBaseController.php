<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KbArticle;
use App\Models\KbCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminKnowledgeBaseController extends Controller
{
    /**
     * Job 27: Customer Knowledge Base Article Publisher - Index Desk.
     */
    public function index(Request $request)
    {
        $query = KbArticle::with(['category', 'author'])->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'LIKE', "%{$s}%")
                    ->orWhere('summary', 'LIKE', "%{$s}%")
                    ->orWhere('content', 'LIKE', "%{$s}%");
            });
        }

        $articles = $query->paginate(12)->withQueryString();

        $categories = KbCategory::withCount('allArticles')
            ->orderBy('sort_order')
            ->get();

        $totalArticles   = KbArticle::count();
        $publishedCount  = KbArticle::where('is_published', true)->count();
        $draftCount      = $totalArticles - $publishedCount;
        $totalViews      = (int) KbArticle::sum('views_count');
        $totalCategories = $categories->count();

        return view('admin.support.kb.index', compact(
            'articles',
            'categories',
            'totalArticles',
            'publishedCount',
            'draftCount',
            'totalViews',
            'totalCategories'
        ));
    }

    /**
     * Create article authoring form.
     */
    public function create()
    {
        $categories = KbCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.support.kb.create', compact('categories'));
    }

    /**
     * Store new article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:kb_categories,id',
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:kb_articles,slug',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
            'is_featured'  => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']) . '-' . Str::random(4);

        $article = KbArticle::create([
            'category_id'  => $validated['category_id'],
            'title'        => $validated['title'],
            'slug'         => $slug,
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'is_published' => (bool) ($request->is_published ?? false),
            'is_featured'  => (bool) ($request->is_featured ?? false),
            'author_id'    => Auth::id() ?? 1,
            'views_count'  => 0,
        ]);

        return redirect()->route('admin.support.kb.index')
            ->with('success', "Knowledge base article '{$article->title}' saved successfully.");
    }

    /**
     * Edit article form.
     */
    public function edit(KbArticle $article)
    {
        $categories = KbCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.support.kb.edit', compact('article', 'categories'));
    }

    /**
     * Update article.
     */
    public function update(Request $request, KbArticle $article)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:kb_categories,id',
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:kb_articles,slug,' . $article->id,
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
            'is_featured'  => 'nullable|boolean',
        ]);

        if (!empty($validated['slug'])) {
            $article->slug = Str::slug($validated['slug']);
        }

        $article->update([
            'category_id'  => $validated['category_id'],
            'title'        => $validated['title'],
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'is_published' => (bool) ($request->is_published ?? false),
            'is_featured'  => (bool) ($request->is_featured ?? false),
        ]);

        return redirect()->route('admin.support.kb.index')
            ->with('success', "Article '{$article->title}' updated successfully.");
    }

    /**
     * Delete article.
     */
    public function destroy(KbArticle $article)
    {
        $title = $article->title;
        $article->delete();

        return redirect()->route('admin.support.kb.index')
            ->with('success', "Article '{$title}' removed.");
    }

    /**
     * Toggle publish state.
     */
    public function togglePublish(KbArticle $article)
    {
        $article->is_published = !$article->is_published;
        $article->save();

        $status = $article->is_published ? 'published live' : 'moved to draft';
        return redirect()->back()->with('success', "Article '{$article->title}' is now {$status}.");
    }

    /**
     * Save category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:kb_categories,name',
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
        ]);

        $category = KbCategory::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? 'document-text',
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.support.kb.index')
            ->with('success', "Category '{$category->name}' created.");
    }

    /**
     * Update category.
     */
    public function updateCategory(Request $request, KbCategory $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:kb_categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        $category->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? $category->icon,
            'sort_order'  => $validated['sort_order'] ?? $category->sort_order,
            'is_active'   => (bool) ($request->is_active ?? true),
        ]);

        return redirect()->route('admin.support.kb.index')
            ->with('success', "Category '{$category->name}' updated.");
    }

    /**
     * Delete category.
     */
    public function destroyCategory(KbCategory $category)
    {
        if ($category->allArticles()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category that still contains articles.');
        }

        $category->delete();
        return redirect()->route('admin.support.kb.index')
            ->with('success', "Category removed.");
    }

    /**
     * Storefront Customer Help Center (/help).
     */
    public function storefrontHelp(Request $request)
    {
        $categories = KbCategory::where('is_active', true)
            ->with(['articles' => function ($q) {
                $q->latest();
            }])
            ->orderBy('sort_order')
            ->get();

        $featuredArticles = KbArticle::where('is_published', true)
            ->where('is_featured', true)
            ->with('category')
            ->take(6)
            ->get();

        $searchQuery = trim($request->get('q', ''));
        $searchResults = collect();

        if (!empty($searchQuery)) {
            $searchResults = KbArticle::where('is_published', true)
                ->where(function ($q) use ($searchQuery) {
                    $q->where('title', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('summary', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('content', 'LIKE', "%{$searchQuery}%");
                })
                ->with('category')
                ->take(15)
                ->get();
        }

        return view('storefront.help', compact('categories', 'featuredArticles', 'searchQuery', 'searchResults'));
    }

    /**
     * Storefront Article Reader (/help/{slug}).
     */
    public function storefrontArticle(string $slug)
    {
        $article = KbArticle::where('slug', $slug)
            ->where('is_published', true)
            ->with(['category', 'author'])
            ->firstOrFail();

        // Increment view count
        $article->increment('views_count');

        // Related articles from same category
        $relatedArticles = KbArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->take(4)
            ->get();

        return view('storefront.help-show', compact('article', 'relatedArticles'));
    }
}


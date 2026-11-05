@extends('admin.layout')

@section('content')
<div class="space-y-6" x-data="{
    showCategoryModal: false,
    categoryMode: 'create',
    categoryId: '',
    categoryName: '',
    categoryIcon: 'book-open',
    categoryDesc: '',
    categoryOrder: 0,
    openCreateCategory() {
        this.categoryMode = 'create';
        this.categoryId = '';
        this.categoryName = '';
        this.categoryIcon = 'book-open';
        this.categoryDesc = '';
        this.categoryOrder = 0;
        this.showCategoryModal = true;
    },
    openEditCategory(id, name, icon, desc, order) {
        this.categoryMode = 'edit';
        this.categoryId = id;
        this.categoryName = name;
        this.categoryIcon = icon || 'book-open';
        this.categoryDesc = desc || '';
        this.categoryOrder = order || 0;
        this.showCategoryModal = true;
    }
}">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <span>Organization</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 7: Customer Self-Service &amp; Help</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Knowledge Base Article Publisher</h1>
            <p class="text-sm text-slate-500 mt-0.5">Author customer sizing guides, atelier care instructions, return guidelines, and dispatch FAQs.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('help.index') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Live Help Center
            </a>
            <button @click="openCreateCategory()" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Manage Categories
            </button>
            <a href="{{ route('admin.support.kb.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Publish New Article
            </a>
        </div>
    </div>

    <!-- 4 High-Impact KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Total Articles</span>
                <span class="p-2 bg-slate-50 text-slate-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($totalArticles) }}</div>
            <p class="text-xs text-slate-500 mt-1">Across {{ $totalCategories }} categories</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Live &amp; Published</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($publishedCount) }}</div>
            <p class="text-xs text-slate-500 mt-1">Visible on customer portal</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Draft Articles</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ number_format($draftCount) }}</div>
            <p class="text-xs text-slate-500 mt-1">Unpublished revisions</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Customer Views</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalViews) }}</div>
            <p class="text-xs text-slate-500 mt-1">Total article impressions</p>
        </div>
    </div>

    <!-- Filter & Categories Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.support.kb.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Search Articles</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, summary, keywords..." 
                           class="w-full text-xs rounded-xl border-slate-200 pl-8 focus:border-rose-500 focus:ring-rose-500">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                <select name="category_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->all_articles_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Publish Status</label>
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Articles</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published Live</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('admin.support.kb.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Article Title</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Views</th>
                        <th class="py-3.5 px-4">Featured</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Author</th>
                        <th class="py-3.5 px-4">Last Updated</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $article)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.support.kb.edit', $article) }}" class="font-bold text-slate-900 hover:text-rose-600 block">
                                    {{ $article->title }}
                                </a>
                                @if($article->summary)
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $article->summary }}</p>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">/help/{{ $article->slug }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($article->category)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700">
                                        {{ $article->category->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Uncategorized</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800">{{ number_format($article->views_count) }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($article->is_featured)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        ★ Featured
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <form method="POST" action="{{ route('admin.support.kb.toggle', $article) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-semibold border transition {{ $article->is_published ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                        {{ $article->is_published ? 'Published' : 'Draft' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-700">{{ $article->author->name ?? 'Admin Staff' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-500">{{ $article->updated_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($article->is_published)
                                        <a href="{{ route('help.show', $article->slug) }}" target="_blank" title="View live"
                                           class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-slate-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.support.kb.edit', $article) }}" title="Edit article"
                                       class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.support.kb.destroy', $article) }}" onsubmit="return confirm('Delete this article?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <p class="font-semibold text-slate-600 text-sm">No articles found</p>
                                    <p class="text-xs text-slate-400">Create your first knowledge base article or adjust filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    <!-- Category Modal -->
    <div x-show="showCategoryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="showCategoryModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Knowledge Base Categories</h3>
                    <button @click="showCategoryModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <!-- Existing Categories List -->
                <div class="mt-4 mb-6 space-y-2 max-h-48 overflow-y-auto">
                    @foreach($categories as $cat)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl text-xs">
                            <div>
                                <span class="font-bold text-slate-800">{{ $cat->name }}</span>
                                <span class="text-slate-400 text-[11px] ml-2 font-mono">({{ $cat->all_articles_count }} articles)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="openEditCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->icon }}', '{{ addslashes($cat->description) }}', {{ $cat->sort_order }})" 
                                        class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Edit
                                </button>
                                @if($cat->all_articles_count == 0)
                                    <form method="POST" action="{{ route('admin.support.kb.category.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Create/Update Category Form -->
                <form method="POST" :action="categoryMode === 'create' ? '{{ route('admin.support.kb.category.store') }}' : '/admin/support/kb/categories/' + categoryId" class="space-y-3.5 border-t border-slate-100 pt-4">
                    @csrf
                    <template x-if="categoryMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <span x-text="categoryMode === 'create' ? 'Add New Category' : 'Edit Category'"></span>
                        </label>
                        <input type="text" name="name" x-model="categoryName" required placeholder="Category name (e.g. Atelier Sizing Guide)" 
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Icon Identifier</label>
                            <input type="text" name="icon" x-model="categoryIcon" placeholder="e.g. tag, truck, star" 
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Display Sort Order</label>
                            <input type="number" name="sort_order" x-model="categoryOrder" 
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Description (Optional)</label>
                        <input type="text" name="description" x-model="categoryDesc" placeholder="Brief summary of articles in this section" 
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showCategoryModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                            Close
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition">
                            <span x-text="categoryMode === 'create' ? 'Save Category' : 'Update Category'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


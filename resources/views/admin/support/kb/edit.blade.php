@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.support.kb.index') }}" class="hover:underline">Knowledge Base</a>
                <span>•</span>
                <span class="text-slate-500 font-medium">Article Editor</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Article: {{ $article->title }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Update content, category assignment, slug, or publication status.</p>
        </div>
        <div class="flex items-center gap-2.5">
            @if($article->is_published)
                <a href="{{ route('help.show', $article->slug) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Live
                </a>
            @endif
            <a href="{{ route('admin.support.kb.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Return to Articles
            </a>
        </div>
    </div>

    <!-- Article Editor Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <form method="POST" action="{{ route('admin.support.kb.update', $article) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 cols: Main Content -->
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Article Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $article->title) }}" required 
                               class="w-full text-sm rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">URL Slug</label>
                        <div class="flex rounded-xl shadow-xs">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-xs font-mono">
                                /help/
                            </span>
                            <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" 
                                   class="flex-1 text-xs rounded-none rounded-r-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Executive Summary / Excerpt</label>
                        <textarea name="summary" rows="2" 
                                  class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">{{ old('summary', $article->summary) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Full Article Content <span class="text-rose-500">*</span></label>
                        <textarea name="content" rows="14" required 
                                  class="w-full text-xs font-mono rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">{{ old('content', $article->content) }}</textarea>
                    </div>
                </div>

                <!-- Right col: Publishing Options -->
                <div class="space-y-6">
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-4">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Publishing Settings</h3>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Helpdesk Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 bg-white">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-3 border-t border-slate-200/60 space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }} 
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="text-xs font-semibold text-slate-800">Published Live</span>
                                    <p class="text-[10px] text-slate-500">Visible on public /help portal</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }} 
                                       class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="text-xs font-semibold text-slate-800">Featured Article</span>
                                    <p class="text-[10px] text-slate-500">Highlight in Help Center hero</p>
                                </div>
                            </label>
                        </div>

                        <div class="pt-3 border-t border-slate-200/60 text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Impressions:</span>
                                <span class="font-bold text-slate-800">{{ number_format($article->views_count) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Author:</span>
                                <span class="text-slate-800">{{ $article->author->name ?? 'Admin Staff' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Last Updated:</span>
                                <span class="text-slate-800">{{ $article->updated_at->format('M d, Y · H:i') }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200/60">
                            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                                Update Article
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


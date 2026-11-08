{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT HELP SHOW 
    ======================================================================
    File: resources/views/storefront/help-show.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
<x-storefront-layout :title="$article->title . ' - Help Center'" :metaDesc="$article->summary ?? Str::limit(strip_tags($article->content), 150)">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Home</a>
                <span>/</span>
                <a href="{{ route('help.index') }}" class="hover:text-rose-600 transition">Help Center</a>
                @if($article->category)
                    <span>/</span>
                    <span class="text-slate-600 font-medium">{{ $article->category->name }}</span>
                @endif
                <span>/</span>
                <span class="text-slate-900 font-semibold truncate max-w-xs">{{ $article->title }}</span>
            </nav>

            <!-- Article Container -->
            <article class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-xs space-y-8">
                <!-- Article Header -->
                <header class="border-b border-slate-100 pb-6 space-y-3">
                    @if($article->category)
                        <span class="inline-block text-[11px] font-bold tracking-[0.2em] text-rose-600 uppercase">
                            {{ $article->category->name }}
                        </span>
                    @endif
                    <h1 class="text-2xl sm:text-4xl font-serif text-slate-900 font-normal tracking-tight leading-tight">
                        {{ $article->title }}
                    </h1>
                    @if($article->summary)
                        <p class="text-sm sm:text-base text-slate-600 font-light leading-relaxed">
                            {{ $article->summary }}
                        </p>
                    @endif
                    <div class="flex items-center gap-4 text-[11px] text-slate-400 pt-2 flex-wrap">
                        <span>Published: {{ $article->created_at->format('M d, Y') }}</span>
                        <span>•</span>
                        <span>Last Updated: {{ $article->updated_at->format('M d, Y') }}</span>
                        <span>•</span>
                        <span>{{ number_format($article->views_count) }} views</span>
                    </div>
                </header>

                <!-- Article Body Content -->
                <div class="prose prose-slate max-w-none text-slate-800 text-sm leading-relaxed whitespace-pre-line font-light">
                    {!! nl2br(e($article->content)) !!}
                </div>

                <!-- Helpful Feedback Widget -->
                <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50 p-6 rounded-2xl">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Was this guide helpful?</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Let us know if you need additional assistance with this topic.</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button onclick="this.innerText='Thank you!'; this.disabled=true;" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition shadow-2xs">
                            👍 Yes, helpful
                        </button>
                        <a href="{{ route('contact') }}" class="px-4 py-2 bg-slate-900 hover:bg-rose-950 text-white text-xs font-bold rounded-xl transition shadow-2xs">
                            Contact Support
                        </a>
                    </div>
                </div>
            </article>

            <!-- Related Articles -->
            @if(isset($relatedArticles) && $relatedArticles->isNotEmpty())
                <div class="mt-12 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Related Articles in this Category</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($relatedArticles as $rel)
                            <a href="{{ route('help.show', $rel->slug) }}" 
                               class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:border-rose-300 hover:shadow-md transition group">
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-rose-600 transition">{{ $rel->title }}</h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 font-light">
                                    {{ $rel->summary ?? Str::limit(strip_tags($rel->content), 90) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Back Link -->
            <div class="mt-10 text-center">
                <a href="{{ route('help.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-rose-600 hover:underline">
                    ← Return to Help Center Directory
                </a>
            </div>
        </div>
    </div>
</x-storefront-layout>


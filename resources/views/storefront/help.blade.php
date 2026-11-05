<x-storefront-layout title="Help Center &amp; Client Knowledge Base" metaDesc="Find instant answers to sizing guides, atelier care, international dispatch, and return policies.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">
            <!-- Header & Search Hero -->
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">Self-Service Help Center</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">How Can We Help You?</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-3 leading-relaxed font-light">
                    Browse our atelier guides, bespoke sizing tutorials, dispatch advisories, and care manuals.
                </p>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('help.index') }}" class="mt-8 relative max-w-xl mx-auto">
                    <div class="relative flex items-center shadow-sm rounded-full overflow-hidden border border-slate-200 bg-white focus-within:border-rose-500 focus-within:ring-2 focus-within:ring-rose-200">
                        <span class="pl-5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Search articles, e.g. sizing guide, wash care, returns..." 
                               class="w-full py-3.5 px-4 text-xs sm:text-sm border-0 focus:ring-0 text-slate-800 placeholder-slate-400">
                        <button type="submit" class="m-1.5 px-5 py-2.5 bg-slate-900 hover:bg-rose-950 text-white rounded-full text-xs font-bold uppercase tracking-wider transition">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Results View (If query present) -->
            @if(!empty($searchQuery))
                <div class="mb-14">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-6">
                        <h2 class="text-lg font-bold text-slate-900">Search Results for "{{ $searchQuery }}"</h2>
                        <a href="{{ route('help.index') }}" class="text-xs text-rose-600 font-semibold hover:underline">Clear Search</a>
                    </div>

                    @if($searchResults->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($searchResults as $res)
                                <a href="{{ route('help.show', $res->slug) }}" 
                                   class="block p-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md hover:border-rose-300 transition group">
                                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-rose-600 mb-1.5">
                                        <span>{{ $res->category->name ?? 'General' }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-rose-600 transition">{{ $res->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $res->summary ?? Str::limit(strip_tags($res->content), 120) }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl p-8 text-center border border-slate-200">
                            <p class="text-slate-600 text-sm">No articles matched your search query.</p>
                            <a href="{{ route('faq') }}" class="inline-block mt-3 text-xs font-semibold text-rose-600 hover:underline">Browse FAQ Section →</a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Featured Articles Grid (If available) -->
            @if(isset($featuredArticles) && $featuredArticles->isNotEmpty())
                <div class="mb-14">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-900">Featured Guides &amp; Key Policies</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($featuredArticles as $feat)
                            <a href="{{ route('help.show', $feat->slug) }}" 
                               class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-rose-300 hover:shadow-md transition flex flex-col justify-between group">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block mb-2">{{ $feat->category->name ?? 'Guides' }}</span>
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-rose-600 transition">{{ $feat->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed font-light">
                                        {{ $feat->summary ?? Str::limit(strip_tags($feat->content), 100) }}
                                    </p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-rose-600 font-semibold">
                                    <span>Read Full Guide</span>
                                    <span>→</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Knowledge Categories Directory -->
            <div class="space-y-10">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-3">
                    Knowledge Base Categories
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($categories as $category)
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900">{{ $category->name }}</h3>
                                        @if($category->description)
                                            <p class="text-xs text-slate-500 font-light">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                    {{ $category->articles->count() }} articles
                                </span>
                            </div>

                            <ul class="space-y-2.5 divide-y divide-slate-50">
                                @forelse($category->articles->take(5) as $art)
                                    <li class="pt-2">
                                        <a href="{{ route('help.show', $art->slug) }}" 
                                           class="flex items-center justify-between text-xs text-slate-700 hover:text-rose-600 font-medium group transition">
                                            <span class="group-hover:translate-x-1 transition-transform truncate pr-4">{{ $art->title }}</span>
                                            <span class="text-slate-300 group-hover:text-rose-500">→</span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="pt-2 text-xs text-slate-400 italic">No published articles yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <div class="col-span-2 py-12 text-center bg-white rounded-3xl border border-slate-200">
                            <p class="text-slate-500 text-sm">Knowledge base articles will appear here soon.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Still Need Help Footer -->
            <div class="text-center mt-16 p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <h3 class="font-serif text-lg text-slate-900">Couldn't find the answers you're looking for?</h3>
                <p class="text-xs text-slate-500 mt-1">Our bespoke client care specialists and personal concierges are available 6 days a week.</p>
                <div class="mt-4 flex items-center justify-center gap-4">
                    <a href="{{ route('faq') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-full text-xs font-bold uppercase tracking-wider transition">
                        View FAQs
                    </a>
                    <a href="{{ route('contact') }}" class="px-6 py-2.5 bg-slate-900 hover:bg-rose-950 text-white rounded-full text-xs font-bold uppercase tracking-wider transition">
                        Contact Concierge
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-storefront-layout>


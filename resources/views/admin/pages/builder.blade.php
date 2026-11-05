@extends('admin.layout')

@section('title', $isEdit ? "Edit Page: {$page->title}" : 'Create Custom Landing Page')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-16" x-data="{
    blocks: {{ json_encode($page->content_blocks ?? []) }},
    addBlock(type) {
        if (type === 'hero') {
            this.blocks.push({
                type: 'hero',
                headline: 'Elegance Redefined',
                subheadline: 'Discover the new limited-edition bridal silk collection handcrafted in Varanasi.',
                cta_text: 'Explore Collection',
                cta_link: '/category/bridal-sets',
                theme_style: 'dark'
            });
        } else if (type === 'featured_grid') {
            this.blocks.push({
                type: 'featured_grid',
                title: 'Curated Designer Edit',
                subtitle: 'Handpicked luxury pieces trending this week',
                limit: 4
            });
        } else if (type === 'countdown') {
            this.blocks.push({
                type: 'countdown',
                title: 'Exclusive Midnight Flash Drop',
                subtitle: 'Use code MIDNIGHT20 for 20% off all silk chemises before timer expires',
                target_date: '2026-12-31T23:59',
                coupon_code: 'MIDNIGHT20'
            });
        } else if (type === 'testimonials') {
            this.blocks.push({
                type: 'testimonials',
                title: 'What Haute Couture Connoisseurs Say',
                quote: 'The finest mulberry silk lingerie I have ever experienced. Unrivaled craftsmanship.',
                author: 'Sanjana M.',
                location: 'Vogue India Reader',
                rating: 5
            });
        } else if (type === 'faq') {
            this.blocks.push({
                type: 'faq',
                title: 'Frequently Asked Questions',
                items: [
                    { q: 'What is your discreet packaging policy?', a: 'All shipments arrive in unbranded, unmarked luxury matte black gift boxes.' },
                    { q: 'How do I choose the correct cup and band size?', a: 'Refer to our comprehensive interactive Size Guide or book a complimentary virtual consultation.' }
                ]
            });
        } else if (type === 'newsletter') {
            this.blocks.push({
                type: 'newsletter',
                title: 'Join the Private VIP Salon',
                subtitle: 'Receive first-access to limited drops, seasonal lookbooks, and invitation-only trunk shows.'
            });
        }
    },
    removeBlock(idx) {
        this.blocks.splice(idx, 1);
    },
    moveUp(idx) {
        if (idx > 0) {
            const temp = this.blocks[idx];
            this.blocks.splice(idx, 1);
            this.blocks.splice(idx - 1, 0, temp);
        }
    },
    moveDown(idx) {
        if (idx < this.blocks.length - 1) {
            const temp = this.blocks[idx];
            this.blocks.splice(idx, 1);
            this.blocks.splice(idx + 1, 0, temp);
        }
    }
}">

    <!-- Header & Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Back to Pages</span>
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">{{ $isEdit ? 'Editing Page' : 'New Page Builder' }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">{{ $isEdit ? "Edit: {$page->title}" : 'Visual Block Page Builder' }}</h1>
        </div>

        @if($isEdit && $page->is_published)
            <a href="{{ route('custom-page.show', $page->slug) }}" target="_blank"
               class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>View Live /p/{{ $page->slug }}</span>
            </a>
        @endif
    </div>

    <!-- Main Builder Form -->
    <form method="POST" action="{{ $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="space-y-6 text-xs">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Page Settings Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Page Identity &amp; SEO Metadata</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Page Title *</label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}" required placeholder="e.g. Bridal Trousseau 2026 Collection"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 font-bold focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">URL Slug</label>
                    <div class="flex items-center">
                        <span class="px-3.5 py-2.5 rounded-l-xl bg-slate-50 border border-r-0 border-slate-200 text-slate-400 font-mono">/p/</span>
                        <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" placeholder="bridal-trousseau"
                               class="w-full px-3.5 py-2.5 rounded-r-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">SEO Meta Title (Optional)</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" placeholder="Luxury Bridal Lingerie & Silk Sets"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">SEO Meta Description (Optional)</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" placeholder="Handcrafted mulberry silk lingerie sets designed for wedding evenings."
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                    <span class="font-bold text-slate-800">Publish immediately (visible on storefront)</span>
                </label>
            </div>
        </div>

        <!-- Add Block Palette Bar -->
        <div class="bg-indigo-900 rounded-3xl p-6 text-white shadow-xl space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white">Modular Block Palette</h3>
                    <p class="text-xs text-indigo-200">Click any block component to add it to your page layout.</p>
                </div>
                <span class="text-xs font-mono px-2.5 py-1 rounded-full bg-indigo-800 text-indigo-300 font-bold" x-text="blocks.length + ' Blocks in Page'"></span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2.5 pt-1">
                <button type="button" @click="addBlock('hero')"
                        class="p-3 rounded-2xl bg-indigo-800/80 hover:bg-indigo-700 text-center transition flex flex-col items-center gap-1.5 border border-indigo-700/50">
                    <span class="text-lg">👑</span>
                    <span class="font-bold text-[11px]">Hero Banner</span>
                </button>

                <button type="button" @click="addBlock('featured_grid')"
                        class="p-3 rounded-2xl bg-indigo-800/80 hover:bg-indigo-700 text-center transition flex flex-col items-center gap-1.5 border border-indigo-700/50">
                    <span class="text-lg">🛍️</span>
                    <span class="font-bold text-[11px]">Product Grid</span>
                </button>

                <button type="button" @click="addBlock('countdown')"
                        class="p-3 rounded-2xl bg-indigo-800/80 hover:bg-indigo-700 text-center transition flex flex-col items-center gap-1.5 border border-indigo-700/50">
                    <span class="text-lg">⏳</span>
                    <span class="font-bold text-[11px]">Drop Timer</span>
                </button>

                <button type="button" @click="addBlock('testimonials')"
                        class="p-3 rounded-2xl bg-indigo-800/80 hover:bg-indigo-700 text-center transition flex flex-col items-center gap-1.5 border border-indigo-700/50">
                    <span class="text-lg">✨</span>
                    <span class="font-bold text-[11px]">Testimonials</span>
                </button>

                <button type="button" @click="addBlock('faq')"
                        class="p-3 rounded-2xl bg-indigo-800/80 hover:bg-indigo-700 text-center transition flex flex-col items-center gap-1.5 border border-indigo-700/50">
                    <span class="text-lg">❓</span>
                    <span class="font-bold text-[11px]">FAQ Accordion</span>
                </button>

                <button type="button" @click="addBlock('newsletter')"
                        class="p-3 rounded-2xl bg-indigo-800/80 hover:bg-indigo-700 text-center transition flex flex-col items-center gap-1.5 border border-indigo-700/50">
                    <span class="text-lg">💌</span>
                    <span class="font-bold text-[11px]">VIP Newsletter</span>
                </button>
            </div>
        </div>

        <!-- Rendered Blocks Container -->
        <div class="space-y-4">
            <template x-for="(block, idx) in blocks" :key="idx">
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4 relative transition hover:border-slate-300">

                    <!-- Block Header Bar -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 font-mono font-bold text-xs flex items-center justify-center" x-text="idx + 1"></span>
                            <span class="font-bold text-slate-900 uppercase tracking-wider text-xs" x-text="block.type.replace('_', ' ')"></span>
                            <input type="hidden" :name="'content_blocks[' + idx + '][type]'" :value="block.type">
                        </div>

                        <div class="flex items-center gap-1">
                            <button type="button" @click="moveUp(idx)" :disabled="idx === 0"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 disabled:opacity-30 transition" title="Move Up">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button type="button" @click="moveDown(idx)" :disabled="idx === blocks.length - 1"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 disabled:opacity-30 transition" title="Move Down">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <button type="button" @click="removeBlock(idx)"
                                    class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition ml-2" title="Delete Block">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Hero Block Fields -->
                    <template x-if="block.type === 'hero'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Headline</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][headline]'" x-model="block.headline"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Call to Action (CTA) Text</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][cta_text]'" x-model="block.cta_text"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-medium">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Subheadline</label>
                                    <textarea :name="'content_blocks[' + idx + '][subheadline]'" x-model="block.subheadline" rows="2"
                                              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">CTA Destination Link</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][cta_link]'" x-model="block.cta_link"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Featured Grid Fields -->
                    <template x-if="block.type === 'featured_grid'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Section Title</label>
                                <input type="text" :name="'content_blocks[' + idx + '][title]'" x-model="block.title"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 font-bold focus:outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Subtitle</label>
                                <input type="text" :name="'content_blocks[' + idx + '][subtitle]'" x-model="block.subtitle"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>
                    </template>

                    <!-- Countdown Block Fields -->
                    <template x-if="block.type === 'countdown'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Event / Drop Title</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][title]'" x-model="block.title"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-bold focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Target End Datetime</label>
                                    <input type="datetime-local" :name="'content_blocks[' + idx + '][target_date]'" x-model="block.target_date"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Subtitle / Urgency Text</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][subtitle]'" x-model="block.subtitle"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Promo Coupon Code</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][coupon_code]'" x-model="block.coupon_code"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono font-bold focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Testimonials Block Fields -->
                    <template x-if="block.type === 'testimonials'">
                        <div class="space-y-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Quote</label>
                                <textarea :name="'content_blocks[' + idx + '][quote]'" x-model="block.quote" rows="2"
                                          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Author Name</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][author]'" x-model="block.author"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Role / Magazine</label>
                                    <input type="text" :name="'content_blocks[' + idx + '][location]'" x-model="block.location"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- FAQ Block Fields -->
                    <template x-if="block.type === 'faq'">
                        <div class="space-y-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Section Title</label>
                                <input type="text" :name="'content_blocks[' + idx + '][title]'" x-model="block.title"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 font-bold focus:outline-none focus:border-indigo-500">
                            </div>
                            <div class="space-y-2">
                                <template x-for="(item, itemIdx) in block.items" :key="itemIdx">
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 space-y-2">
                                        <input type="text" :name="'content_blocks[' + idx + '][items][' + itemIdx + '][q]'" x-model="item.q" placeholder="Question..."
                                               class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white font-bold">
                                        <textarea :name="'content_blocks[' + idx + '][items][' + itemIdx + '][a]'" x-model="item.a" rows="2" placeholder="Answer..."
                                                  class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white"></textarea>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Newsletter Block Fields -->
                    <template x-if="block.type === 'newsletter'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Callout Title</label>
                                <input type="text" :name="'content_blocks[' + idx + '][title]'" x-model="block.title"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 font-bold focus:outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Description / Perks</label>
                                <input type="text" :name="'content_blocks[' + idx + '][subtitle]'" x-model="block.subtitle"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>
                    </template>

                </div>
            </template>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-md shadow-indigo-600/20 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save Page Blocks</span>
            </button>
        </div>

    </form>

</div>
@endsection

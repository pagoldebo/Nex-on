{{-- resources/views/storefront/lumiere/components/header.blade.php --}}
@php
    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Lumière');
    $lumiereAnnouncement = \App\Models\Setting::get('announcement_bar');
@endphp
@if(!empty($lumiereAnnouncement))
<div class="bg-gray-900 text-white text-[10px] sm:text-xs uppercase font-bold tracking-wider sm:tracking-widest text-center py-2 px-3 sm:px-4 select-none break-words">
    {{ $lumiereAnnouncement }}
</div>
@endif
<header id="siteHeader" class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-pink-50 transition-shadow">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 md:px-12 py-3 sm:py-5">
        <div class="flex items-center justify-between gap-2">

            <a href="{{ route('home') }}" class="select-none flex items-center py-1 shrink-0">
                @if(!empty($siteLogo))
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-8 sm:h-9 md:h-11 w-auto max-w-[125px] sm:max-w-[200px] object-contain">
                @else
                    <span class="font-display italic text-xl sm:text-2xl font-black tracking-tighter text-pink-600">Lumière.</span>
                @endif
            </a>

            <nav class="hidden lg:flex items-center gap-10 text-[11px] uppercase tracking-widest font-bold text-gray-400" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-pink-600' : 'hover:text-pink-600' }} transition-colors">Home</a>

                <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button class="flex items-center gap-1.5 uppercase hover:text-pink-600 transition-colors" :aria-expanded="open">
                        Categories
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute left-1/2 -translate-x-1/2 top-full pt-4 w-[620px] z-50">
                        <div class="bg-white rounded-[28px] shadow-2xl shadow-pink-100/50 border border-pink-50 grid grid-cols-3 gap-8 p-8">
                            @foreach ($categories->chunk(max(1, ceil($categories->count() / 3))) as $chunk)
                                <div>
                                     @foreach ($chunk as $category)
                                         <p class="font-display italic text-lg text-pink-500 mb-2 normal-case font-normal">
                                             <a href="{{ route('category.show', $category->slug) }}" class="hover:text-pink-600 transition-colors">{{ $category->name }}</a>
                                         </p>
                                         <ul class="space-y-1.5 text-gray-500 text-xs normal-case font-medium mb-4">
                                             @if($category->children->count() > 0)
                                                @foreach ($category->children->take(5) as $sub)
                                                    <li>
                                                        <a href="{{ route('category.show', $sub->slug) }}" class="hover:text-pink-600 transition-colors flex items-center gap-1.5">
                                                            <span class="text-pink-300">↳</span> {{ $sub->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                @foreach ($category->products->take(4) as $product)
                                                    <li><a href="{{ route('product.show', $product->slug) }}" class="hover:text-pink-500 transition-colors">{{ $product->name }}</a></li>
                                                @endforeach
                                            @endif
                                            <li><a href="{{ route('category.show', $category->slug) }}" class="text-pink-400 font-semibold text-[10px] uppercase">View All →</a></li>
                                        </ul>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="col-span-3 pt-4 border-t border-pink-50 flex items-center justify-between">
                                <span class="text-xs text-gray-400 normal-case font-medium">The Lustre Edit — new for the festive season</span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('featured.products') }}" class="border border-pink-200 text-gray-700 text-[10px] px-4 py-2 rounded-full font-bold uppercase tracking-widest hover:border-pink-500 hover:text-pink-600 transition-colors">Featured</a>
                                    <a href="{{ route('collections.sale') }}" class="bg-pink-600 text-white text-[10px] px-4 py-2 rounded-full font-black uppercase tracking-widest hover:bg-gray-900 transition-colors">Sale ✨</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('featured.products') }}" class="hover:text-pink-600 transition-colors normal-case font-semibold text-gray-500 {{ request()->routeIs('featured.products') ? 'text-pink-600 font-bold' : '' }}">Featured</a>
                <a href="{{ route('new.arrivals') }}" class="hover:text-pink-600 transition-colors normal-case font-semibold text-gray-500 {{ request()->routeIs('new.arrivals') ? 'text-pink-600 font-bold' : '' }}">New In</a>
                <a href="{{ route('collections.sale') }}" class="text-pink-600 font-bold normal-case hover:text-gray-900 transition-colors">Sale ✨</a>
                <a href="{{ route('about') }}" class="hover:text-pink-600 transition-colors normal-case font-semibold text-gray-500 {{ request()->routeIs('about') ? 'text-pink-600 font-bold' : '' }}">About Us</a>
                <a href="{{ route('contact') }}" class="hover:text-pink-600 transition-colors normal-case font-semibold text-gray-500">Contact</a>
            </nav>

            <div class="flex items-center gap-1.5 sm:gap-4 shrink-0">
                <button onclick="openSearch()" class="text-gray-400 hover:text-pink-600 transition-colors p-1.5 sm:p-2" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
                </button>
                @auth
                    <a href="{{ route('account') }}" class="hidden lg:inline-flex text-gray-400 hover:text-pink-600 transition-colors p-2" aria-label="Account">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
                    </a>
                @else
                    <button onclick="openLoginModal()" class="hidden lg:inline-flex text-gray-400 hover:text-pink-600 transition-colors p-2" aria-label="Login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
                    </button>
                @endauth
                <button onclick="openCartDrawer()" class="bg-gray-900 text-white px-2.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-wider sm:tracking-widest hover:bg-pink-600 transition-colors flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span class="hidden sm:inline">Bag</span> (<span class="cart-count">0</span>)
                </button>
            </div>
        </div>
    </div>
</header>

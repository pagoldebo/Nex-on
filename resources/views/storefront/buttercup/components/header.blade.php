{{--
    ======================================================================
    THEME: BUTTERCUP — HEADER COMPONENT
    ======================================================================
    File: resources/views/storefront/buttercup/components/header.blade.php
    Features:
      - Soft pastel announcement banner
      - Rounded pill logo with delicate rose glow
      - Dropdown mega-menu with hierarchical subcategory links
      - Quick actions: Search, Wishlist, Account, Slide-out Bag
--}}
@php
    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Buttercup');
    $buttercupAnnouncement = \App\Models\Setting::get('announcement_bar');
@endphp
@if(!empty($buttercupAnnouncement))
<div class="bg-[#B97680] text-[#FFFAF5] text-[11px] font-medium text-center py-2 px-4 tracking-wide select-none">
    {{ $buttercupAnnouncement }}
</div>
@endif
<header id="siteHeader" class="sticky top-0 z-50 bg-[#FFFAF5]/95 backdrop-blur border-b border-[#D89AA0]/20 transition-shadow">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="flex items-center justify-between h-20">

            <a href="{{ route('home') }}" class="select-none flex items-center gap-1.5 py-1">
                @if(!empty($siteLogo))
                    <img src="{{ \App\Models\Setting::imageUrl($siteLogo) }}" alt="{{ $storeName }}" class="h-10 md:h-12 w-auto max-w-[200px] object-contain">
                @else
                    <span class="font-display text-3xl text-[#B97680] flex items-center gap-1">
                        Buttercup <span class="text-base">🌼</span>
                    </span>
                @endif
            </a>

            <nav class="hidden lg:flex items-center gap-9 font-body font-medium text-[15px]" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="text-[#4A3B3A] {{ request()->routeIs('home') ? 'text-[#B97680]' : 'hover:text-[#B97680]' }} transition-colors">Home</a>

                <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button class="flex items-center gap-1.5 text-[#4A3B3A] hover:text-[#B97680] transition-colors" :aria-expanded="open">
                        Categories
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute left-1/2 -translate-x-1/2 top-full pt-4 w-[620px] z-50">
                        <div class="bg-[#FFFAF5] rounded-[28px] shadow-2xl shadow-[#D89AA0]/20 grid grid-cols-3 gap-8 p-8 border border-[#F3D2CD]">
                            @foreach ($categories->chunk(max(1, ceil($categories->count() / 3))) as $chunk)
                                <div>
                                    @foreach ($chunk as $category)
                                        <p class="font-display text-xl text-[#B97680] mb-2">
                                            <a href="{{ route('category.show', $category->slug) }}" class="hover:underline">{{ $category->name }}</a> 🌸
                                        </p>
                                        <ul class="space-y-1.5 text-sm text-[#4A3B3A]/70 mb-4">
                                            @if($category->children->count() > 0)
                                                @foreach ($category->children as $sub)
                                                    <li>
                                                        <a href="{{ route('category.show', $sub->slug) }}" class="hover:text-[#B97680] transition-colors font-medium">
                                                            {{ $sub->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                @foreach ($category->products->take(4) as $product)
                                                    <li><a href="{{ route('product.show', $product->slug) }}" class="hover:text-[#B97680] transition-colors">{{ $product->name }}</a></li>
                                                @endforeach
                                            @endif
                                            <li><a href="{{ route('category.show', $category->slug) }}" class="text-[#D89AA0] font-semibold text-xs">View All {{ $category->name }} →</a></li>
                                        </ul>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="col-span-3 pt-4 border-t border-[#F3D2CD] flex items-center justify-between">
                                <span class="text-sm text-[#4A3B3A]/50">Comfiest sets for everyday wear 🌼</span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('featured.products') }}" class="border border-[#D89AA0] text-[#B97680] text-xs px-4 py-2 rounded-full font-semibold hover:bg-[#D89AA0] hover:text-[#FFFAF5] transition-colors">FEATURED</a>
                                    <a href="{{ route('collections.sale') }}" class="bg-[#D89AA0] text-[#FFFAF5] text-xs px-4 py-2 rounded-full font-semibold hover:bg-[#B97680] transition-colors">SALE 🌸</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('featured.products') }}" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors {{ request()->routeIs('featured.products') ? 'font-bold text-[#B97680]' : '' }}">Featured</a>
                <a href="{{ route('new.arrivals') }}" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors {{ request()->routeIs('new.arrivals') ? 'font-bold text-[#B97680]' : '' }}">New In</a>
                <a href="{{ route('collections.sale') }}" class="text-[#B97680] font-bold hover:text-[#4A3B3A] transition-colors">Sale 🌸</a>
                <a href="{{ route('about') }}" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors {{ request()->routeIs('about') ? 'font-bold text-[#B97680]' : '' }}">About Us</a>
                <a href="{{ route('contact') }}" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors">Contact</a>
            </nav>

            <div class="hidden lg:flex items-center gap-5">
                <button onclick="openSearch()" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
                </button>
                <a href="{{ route('wishlist') }}" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors p-1" aria-label="Wishlist">
                    <i class="fa-regular fa-heart text-lg"></i>
                </a>
                @auth
                    <a href="{{ route('account') }}" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors" aria-label="Account">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
                    </a>
                @else
                    <button onclick="openLoginModal()" class="text-[#4A3B3A] hover:text-[#B97680] transition-colors" aria-label="Login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
                    </button>
                @endauth
                <button onclick="openCartDrawer()" class="relative text-[#4A3B3A] hover:text-[#B97680] transition-colors" aria-label="Cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" d="M6 7L4 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                    <span class="cart-count absolute -top-2 -right-2 bg-[#A8B89A] text-[#FFFAF5] text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">0</span>
                </button>
            </div>

            <button onclick="openSearch()" class="lg:hidden text-[#4A3B3A] p-2 -mr-2" aria-label="Search">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
            </button>
        </div>
    </div>
</header>

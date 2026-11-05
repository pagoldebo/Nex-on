{{-- resources/views/storefront/vyra/components/header.blade.php --}}
@php
    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'VYRA');
    $vyraAnnouncement = \App\Models\Setting::get('announcement_bar');
@endphp
@if(!empty($vyraAnnouncement))
<div class="bg-[#1A1A1D] border-b border-[#FF3D5A]/40 text-[#D4FF3D] text-[10px] uppercase font-mono font-bold tracking-widest text-center py-2 px-4 select-none">
    ⚡ {{ $vyraAnnouncement }}
</div>
@endif
<header id="siteHeader" class="sticky top-0 z-50 bg-[#0E0E10]/95 backdrop-blur border-b border-[#FF3D5A]/30 transition-shadow">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="flex items-center justify-between h-20">

            <a href="{{ route('home') }}" class="select-none flex items-center py-1">
                @if(!empty($siteLogo))
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-10 md:h-12 w-auto max-w-[200px] object-contain">
                @else
                    <span class="font-display text-2xl text-[#F2F0EC]">
                        VY<span class="text-[#FF3D5A]">RA</span>
                    </span>
                @endif
            </a>

            <nav class="hidden lg:flex items-center gap-9 font-body font-medium text-sm uppercase tracking-wide" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#FF3D5A]' : 'text-[#F2F0EC]/60 hover:text-[#F2F0EC]' }} transition-colors">Home</a>

                <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button class="flex items-center gap-1.5 uppercase text-[#F2F0EC]/60 hover:text-[#F2F0EC] transition-colors" :aria-expanded="open">
                        Categories
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="absolute left-1/2 -translate-x-1/2 top-full pt-4 w-[640px] z-50">
                        <div class="bg-[#1A1A1D] border border-[#FF3D5A]/30 shadow-2xl shadow-black/60 grid grid-cols-3 gap-8 p-8">
                            @foreach ($categories->chunk(max(1, ceil($categories->count() / 3))) as $chunk)
                                <div>
                                    @foreach ($chunk as $category)
                                        <p class="font-display text-sm text-[#FF3D5A] uppercase mb-2 tracking-widest">
                                            <a href="{{ route('category.show', $category->slug) }}" class="hover:text-[#D4FF3D] transition-colors">{{ $category->name }}</a>
                                        </p>
                                        <ul class="space-y-1.5 text-xs normal-case text-[#F2F0EC]/60 mb-4">
                                            @if($category->children->count() > 0)
                                                @foreach ($category->children->take(5) as $sub)
                                                    <li>
                                                        <a href="{{ route('category.show', $sub->slug) }}" class="hover:text-[#D4FF3D] transition-colors flex items-center gap-1.5">
                                                            <span class="text-[#FF3D5A]/50 text-[10px]">↳</span> {{ $sub->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                @foreach ($category->products->take(4) as $product)
                                                    <li><a href="{{ route('product.show', $product->slug) }}" class="hover:text-[#D4FF3D] transition-colors">{{ $product->name }}</a></li>
                                                @endforeach
                                            @endif
                                            <li><a href="{{ route('category.show', $category->slug) }}" class="text-[#D4FF3D] font-bold text-[10px] uppercase">View All →</a></li>
                                        </ul>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="col-span-3 pt-4 border-t border-[#FF3D5A]/20 flex items-center justify-between">
                                <span class="text-xs text-[#F2F0EC]/40 normal-case">Drop 03 — bold cuts, zero compromise</span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('featured.products') }}" class="border border-[#FF3D5A]/50 text-[#F2F0EC] text-[10px] px-4 py-2 font-bold uppercase tracking-widest hover:border-[#D4FF3D] hover:text-[#D4FF3D] transition-colors">FEATURED</a>
                                    <a href="{{ route('collections.sale') }}" class="bg-[#FF3D5A] text-[#0E0E10] text-[10px] px-5 py-2 font-bold uppercase tracking-widest hover:bg-[#D4FF3D] transition-colors"
                                       style="clip-path:polygon(6% 0,100% 0,94% 100%,0 100%)">SALE ⚡</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('featured.products') }}" class="text-[#F2F0EC]/60 hover:text-[#F2F0EC] transition-colors {{ request()->routeIs('featured.products') ? 'text-[#D4FF3D] font-bold' : '' }}">Featured</a>
                <a href="{{ route('new.arrivals') }}" class="text-[#F2F0EC]/60 hover:text-[#F2F0EC] transition-colors {{ request()->routeIs('new.arrivals') ? 'text-[#D4FF3D] font-bold' : '' }}">New</a>
                <a href="{{ route('collections.sale') }}" class="text-[#FF3D5A] font-bold hover:text-[#D4FF3D] transition-colors">Sale ⚡</a>
                <a href="{{ route('about') }}" class="text-[#F2F0EC]/60 hover:text-[#F2F0EC] transition-colors {{ request()->routeIs('about') ? 'text-[#D4FF3D] font-bold' : '' }}">About Us</a>
                <a href="{{ route('contact') }}" class="text-[#F2F0EC]/60 hover:text-[#F2F0EC] transition-colors">Contact</a>
            </nav>

            <div class="hidden lg:flex items-center gap-5">
                @include('storefront.common.language-switcher')
                <button onclick="openSearch()" class="text-[#F2F0EC]/60 hover:text-[#FF3D5A] transition-colors" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
                </button>
                <a href="{{ route('wishlist') }}" class="text-[#F2F0EC]/60 hover:text-[#FF3D5A] transition-colors p-1" aria-label="Wishlist">
                    <i class="fa-regular fa-heart text-lg"></i>
                </a>
                @auth
                    <a href="{{ route('account') }}" class="text-[#F2F0EC]/60 hover:text-[#FF3D5A] transition-colors" aria-label="Account">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
                    </a>
                @else
                    <button onclick="openLoginModal()" class="text-[#F2F0EC]/60 hover:text-[#FF3D5A] transition-colors" aria-label="Login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
                    </button>
                @endauth
                <button onclick="openCartDrawer()" class="relative text-[#F2F0EC]/60 hover:text-[#FF3D5A] transition-colors" aria-label="Cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" d="M6 7L4 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                    <span class="cart-count absolute -top-2 -right-2 bg-[#D4FF3D] text-[#0E0E10] text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">0</span>
                </button>
            </div>

            <button onclick="openSearch()" class="lg:hidden text-[#F2F0EC] p-2 -mr-2" aria-label="Search">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
            </button>
        </div>
    </div>
</header>

{{-- resources/views/storefront/vyra/components/cart-drawer.blade.php --}}
<div id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-[400px] z-[200] translate-x-full transition-transform duration-400 flex flex-col shadow-2xl border-l-2" style="background:#1A1A1D;border-color:#FF3D5A;">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:#FF3D5A33;">
        <h3 class="font-semibold text-lg uppercase" style="color:#F2F0EC">Your Bag</h3>
        <button onclick="closeCartDrawer()" style="color:#F2F0EC" class="text-2xl font-light hover:opacity-70 transition-opacity" aria-label="Close cart">&times;</button>
    </div>
    <div id="cartItems" class="flex-1 overflow-y-auto px-6 py-4" style="color:#F2F0EC">
        <p class="text-center text-sm opacity-40 mt-10">Your bag is empty.</p>
    </div>
    <div class="px-6 py-5 border-t" style="border-color:#FF3D5A33;">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs uppercase tracking-widest font-medium opacity-50" style="color:#F2F0EC">Total</span>
            <span id="cartTotal" class="text-lg font-bold" style="color:#FF3D5A">₹0</span>
        </div>
        <a href="{{ route('checkout') }}" class="block w-full py-3.5 text-center text-xs font-bold uppercase tracking-widest transition-colors hover:opacity-90" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">Checkout</a>
        <a href="{{ route('cart') }}" class="block w-full py-3 text-center text-xs font-medium mt-2 opacity-50 hover:opacity-80 transition-opacity uppercase" style="color:#F2F0EC">View Full Cart</a>
    </div>
</div>
<div id="cartOverlay" onclick="closeCartDrawer()" class="hidden fixed inset-0 bg-black/60 z-[190]"></div>

{{-- resources/views/storefront/lumiere/components/cart-drawer.blade.php --}}
<div id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-[400px] z-[200] translate-x-full transition-transform duration-400 flex flex-col shadow-2xl bg-white">
    <div class="flex items-center justify-between px-6 py-5 border-b border-pink-50">
        <h3 class="font-bold text-lg text-gray-900">Your Bag</h3>
        <button onclick="closeCartDrawer()" class="text-2xl font-light text-gray-400 hover:text-gray-700 transition-colors" aria-label="Close cart">&times;</button>
    </div>
    <div id="cartItems" class="flex-1 overflow-y-auto px-6 py-4 text-gray-800">
        <p class="text-center text-sm opacity-40 mt-10">Your bag is empty.</p>
    </div>
    <div class="px-6 py-5 border-t border-pink-50">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs uppercase tracking-widest font-semibold text-gray-400">Total</span>
            <span id="cartTotal" class="text-lg font-bold text-pink-600">₹0</span>
        </div>
        <a href="{{ route('checkout') }}" class="block w-full py-3.5 text-center text-xs font-bold uppercase tracking-widest rounded-2xl transition-colors bg-gray-900 text-white hover:bg-pink-600">Checkout</a>
        <a href="{{ route('cart') }}" class="block w-full py-3 text-center text-xs font-medium mt-2 text-gray-400 hover:text-gray-700 transition-colors">View Full Cart</a>
    </div>
</div>
<div id="cartOverlay" onclick="closeCartDrawer()" class="hidden fixed inset-0 bg-black/40 z-[190]"></div>

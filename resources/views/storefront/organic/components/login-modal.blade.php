{{-- resources/views/storefront/organic/components/login-modal.blade.php
     Accessible quick login modal conforming to modern web standards. --}}
<dialog id="loginModal" 
        closedby="any" 
        aria-labelledby="loginModalTitle"
        class="rounded-3xl p-0 backdrop:bg-stone-900/60 backdrop:backdrop-blur-xs shadow-2xl max-w-md w-full m-auto overflow-hidden border border-stone-200">
  
  <div class="bg-white p-6 sm:p-8">
    
    {{-- Header --}}
    <div class="flex items-center justify-between pb-4 border-b border-stone-100">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-organic-500 text-white flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
        </div>
        <h3 id="loginModalTitle" class="text-lg font-black text-stone-900 font-display">Patron Access</h3>
      </div>
      <form method="dialog">
        <button type="submit" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-full" aria-label="Close dialog">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </form>
    </div>

    {{-- Form --}}
    <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-4">
      @csrf

      <div>
        <label for="modalEmail" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Email Address</label>
        <input type="email" 
               id="modalEmail" 
               name="email" 
               required 
               autofocus
               placeholder="name@example.com" 
               class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:ring-2 focus:ring-organic-500/30 focus:border-organic-500 transition">
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="modalPassword" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Password</label>
        </div>
        <input type="password" 
               id="modalPassword" 
               name="password" 
               required 
               placeholder="••••••••" 
               class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:ring-2 focus:ring-organic-500/30 focus:border-organic-500 transition">
      </div>

      <div class="flex items-center justify-between text-xs pt-1">
        <label class="flex items-center gap-2 cursor-pointer text-stone-600">
          <input type="checkbox" name="remember" class="rounded text-organic-600 focus:ring-organic-500 border-stone-300">
          <span>Remember me</span>
        </label>
        <a href="{{ route('login') }}" class="text-organic-600 hover:underline font-semibold">Forgot password?</a>
      </div>

      <div class="pt-2">
        <button type="submit" class="w-full py-3 bg-organic-500 hover:bg-organic-600 text-white font-bold text-xs rounded-xl transition shadow-md shadow-organic-500/20">
          Sign In to Account
        </button>
      </div>

      <div class="text-center pt-2 text-xs text-stone-500">
        <span>Don't have an account yet? </span>
        <a href="{{ route('register') }}" class="text-organic-600 font-bold hover:underline">Register now</a>
      </div>

    </form>

  </div>
</dialog>

<script>
  function openLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal && typeof modal.showModal === 'function') {
      modal.showModal();
    }
  }

  // Light dismiss fallback for browsers without native closedby="any"
  if (!('closedBy' in HTMLDialogElement.prototype)) {
    const dialog = document.getElementById('loginModal');
    if (dialog) {
      const isOutside = (e, dlg) => {
        const r = dlg.getBoundingClientRect();
        return e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom;
      };
      let pressedOutside = false;
      dialog.addEventListener('pointerdown', (e) => { pressedOutside = isOutside(e, dialog); });
      dialog.addEventListener('pointerup', (e) => { if (pressedOutside && isOutside(e, dialog)) dialog.close(); });
    }
  }
</script>


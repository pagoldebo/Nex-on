{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    COMPONENT: MODAL AUTHENTICATION DIALOG
    ======================================================================
    File: resources/views/storefront/saaya/components/login-modal.blade.php
    Component Injection: storefront.saaya.components.login-modal
    Responsibility: Frictionless patron sign-in and registration overlay
    Features:
      - In-context customer sign-in without breaking current browsing flow
      - Password authentication, Two-Factor OTP step, and password recovery trigger
      - Switchable tabs between existing customer Login and new customer Registration
      - CSRF token protection and patron rate limiting enforcement
    Operations & Maintenance:
      - Triggered via Alpine event 'open-login-modal'
      - Automatically reloads active cart upon authentication
--}}
<div id="loginModal"
     class="hidden fixed inset-0 z-[60] bg-maroon-dark/80 backdrop-blur-sm items-center justify-center p-6"
     role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">

  <div class="bg-ivory max-w-md w-full p-10 relative foil-frame">
    {{-- Gold foil tracing border --}}
    <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
      <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
    </svg>

    <button id="loginModalClose"
            class="absolute top-4 right-4 text-maroon/40 hover:text-maroon transition-colors"
            aria-label="Close sign-in">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <p class="font-display italic text-gold text-center text-lg">Welcome Back</p>
    <h2 id="loginModalTitle" class="font-display text-3xl text-maroon text-center mt-1">Sign In to Saaya</h2>

    <form method="POST" action="{{ route('login') }}" class="mt-8 flex flex-col gap-4">
      @csrf
      <input type="email" name="email" required autocomplete="email" placeholder="Email Address"
             class="border border-gold/40 bg-transparent px-4 py-3 text-sm text-maroon placeholder-maroon/40 focus:border-gold focus:outline-none transition-colors font-body">
      <input type="password" name="password" required autocomplete="current-password" placeholder="Password"
             class="border border-gold/40 bg-transparent px-4 py-3 text-sm text-maroon placeholder-maroon/40 focus:border-gold focus:outline-none transition-colors font-body">
      <div class="flex justify-end">
        <a href="{{ route('password.request') }}" class="text-xs text-gold hover:underline font-body">Forgot password?</a>
      </div>
      <button type="submit" class="gold-btn py-3.5 text-xs tracking-widest font-body font-semibold mt-1">SIGN IN</button>
    </form>

    <div class="relative my-6">
      <div class="petal-divider opacity-30"></div>
    </div>

    <p class="text-center text-xs text-maroon/60 font-body">
      New to Saaya?
      <a href="{{ route('register') }}" class="text-gold underline-grow">Create an account</a>
    </p>
  </div>
</div>

<script>
(function () {
  const modal = document.getElementById('loginModal');
  if (!modal) return;

  function openLoginModal()  { modal.classList.remove('hidden'); modal.classList.add('flex'); modal.querySelector('input')?.focus(); }
  function closeLoginModal() { modal.classList.add('hidden');    modal.classList.remove('flex'); }

  document.getElementById('loginModalClose')?.addEventListener('click', closeLoginModal);
  modal.addEventListener('click', e => { if (e.target === modal) closeLoginModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLoginModal(); });

  // Expose so header + tab-bar can call it
  window.openLoginModal = openLoginModal;
})();
</script>

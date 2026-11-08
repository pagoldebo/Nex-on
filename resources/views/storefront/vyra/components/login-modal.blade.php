{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    COMPONENT: MODAL AUTHENTICATION DIALOG
    ======================================================================
    File: resources/views/storefront/vyra/components/login-modal.blade.php
    Component Injection: storefront.vyra.components.login-modal
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
<div id="loginModal" class="hidden fixed inset-0 z-[210] bg-black/70 backdrop-blur-sm items-center justify-center p-4">
    <div class="w-full max-w-md p-10 relative shadow-2xl border-2" style="background:#1A1A1D;border-color:#FF3D5A;">
        <button onclick="closeLoginModal()" class="absolute top-5 right-5 text-2xl font-light opacity-40 hover:opacity-80 transition-opacity" style="color:#F2F0EC" aria-label="Close">&times;</button>
        <h3 class="text-2xl font-semibold text-center mb-1 uppercase" style="color:#F2F0EC">Welcome Back</h3>
        <p class="text-center text-sm opacity-50 mb-8 uppercase tracking-wide" style="color:#F2F0EC">Sign in to VYRA</p>
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf
            <input type="email" name="email" placeholder="Email Address" required
                   class="border px-5 py-3.5 text-sm outline-none transition-colors"
                   style="background:transparent;border-color:#FF3D5A55;color:#F2F0EC;">
            <input type="password" name="password" placeholder="Password" required
                   class="border px-5 py-3.5 text-sm outline-none transition-colors"
                   style="background:transparent;border-color:#FF3D5A55;color:#F2F0EC;">
            <button type="submit" class="py-3.5 text-xs font-bold uppercase tracking-widest mt-1 transition-opacity hover:opacity-90" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">Sign In</button>
        </form>
        <p class="text-center text-xs mt-6 opacity-50 uppercase" style="color:#F2F0EC">
            New to VYRA? <a href="{{ route('register') }}" class="font-semibold opacity-100" style="color:#D4FF3D">Create an account</a>
        </p>
    </div>
</div>

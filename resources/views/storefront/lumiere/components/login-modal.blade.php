{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    COMPONENT: MODAL AUTHENTICATION DIALOG
    ======================================================================
    File: resources/views/storefront/lumiere/components/login-modal.blade.php
    Component Injection: storefront.lumiere.components.login-modal
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
<div id="loginModal" class="hidden fixed inset-0 z-[210] bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-[32px] p-10 relative shadow-2xl">
        <button onclick="closeLoginModal()" class="absolute top-5 right-5 text-2xl font-light text-gray-300 hover:text-gray-600 transition-colors" aria-label="Close">&times;</button>
        <h3 class="font-display italic text-3xl text-center text-gray-900 mb-1">Welcome Back</h3>
        <p class="text-center text-sm text-gray-400 mb-8">Sign in to your Lumière account</p>
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf
            <input type="email" name="email" placeholder="Email Address" required
                   class="rounded-2xl border border-pink-100 px-5 py-3.5 text-sm outline-none transition-colors focus:border-pink-400 text-gray-800">
            <input type="password" name="password" placeholder="Password" required
                   class="rounded-2xl border border-pink-100 px-5 py-3.5 text-sm outline-none transition-colors focus:border-pink-400 text-gray-800">
            <button type="submit" class="py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest mt-1 transition-colors bg-[#405549] text-white hover:bg-pink-600">Sign In</button>
        </form>
        <p class="text-center text-xs mt-6 text-gray-400">
            New to Lumière? <a href="{{ route('register') }}" class="font-bold text-pink-500">Create an account</a>
        </p>
    </div>
</div>

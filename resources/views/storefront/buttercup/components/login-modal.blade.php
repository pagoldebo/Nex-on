{{-- resources/views/storefront/buttercup/components/login-modal.blade.php --}}
<div id="loginModal" class="hidden fixed inset-0 z-[210] bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="w-full max-w-md rounded-[32px] p-10 relative shadow-2xl" style="background:#FFFAF5;">
        <button onclick="closeLoginModal()" class="absolute top-5 right-5 text-2xl font-light opacity-40 hover:opacity-80 transition-opacity" style="color:#4A3B3A" aria-label="Close">&times;</button>
        <h3 class="text-2xl font-semibold text-center mb-1" style="color:#4A3B3A">Welcome Back</h3>
        <p class="text-center text-sm opacity-50 mb-8" style="color:#4A3B3A">Sign in to your Buttercup account</p>
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf
            <input type="email" name="email" placeholder="Email Address" required
                   class="rounded-2xl border px-5 py-3.5 text-sm outline-none transition-colors"
                   style="background:transparent;border-color:#D89AA055;color:#4A3B3A;">
            <input type="password" name="password" placeholder="Password" required
                   class="rounded-2xl border px-5 py-3.5 text-sm outline-none transition-colors"
                   style="background:transparent;border-color:#D89AA055;color:#4A3B3A;">
            <button type="submit" class="py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest mt-1 transition-opacity hover:opacity-90 bg-[#D89AA0] text-[#FFFAF5]">Sign In</button>
        </form>
        <p class="text-center text-xs mt-6 opacity-50" style="color:#4A3B3A">
            New here? <a href="{{ route('register') }}" class="font-semibold opacity-100" style="color:#D89AA0">Create an account</a>
        </p>
    </div>
</div>

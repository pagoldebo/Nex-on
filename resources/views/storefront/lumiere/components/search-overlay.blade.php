{{-- resources/views/storefront/lumiere/components/search-overlay.blade.php --}}
<div id="searchOverlay" class="hidden fixed inset-0 z-[205] backdrop-blur-md flex-col p-6 md:p-12 bg-white/95">
    <div class="max-w-2xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <span class="font-bold text-xl text-gray-900">Search</span>
            <button onclick="closeSearch()" class="text-2xl font-light text-gray-300 hover:text-gray-600 transition-colors" aria-label="Close search">&times;</button>
        </div>
        <form method="GET" action="{{ route('search') }}">
            <input id="searchInput" name="q" type="text" placeholder="Search for silk sets, bralettes, sleepwear..."
                   value="{{ request('q') }}"
                   class="w-full border-b-2 border-pink-100 text-xl py-4 outline-none transition-colors focus:border-pink-400 bg-transparent text-gray-800">
        </form>
        <div id="liveResults" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8 hidden"></div>
        <div class="mt-10">
            <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-4">Popular</p>
            <div class="flex flex-wrap gap-2">
                @foreach (['Silk Slip', 'Lustre Bralette', 'Lace Set', 'Sleepwear', 'Bridal Edit'] as $term)
                    <a href="{{ route('search') }}?q={{ $term }}" class="text-xs px-4 py-2 rounded-full border border-pink-100 text-gray-700 transition-colors hover:border-pink-400">{{ $term }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    document.getElementById('searchInput')?.addEventListener('input', function() {
        const q = this.value.trim();
        const res = document.getElementById('liveResults');
        if (q.length < 2) { res.classList.add('hidden'); return; }
        fetch(`{{ route('search') }}?q=${encodeURIComponent(q)}&ajax=1`)
            .then(r => r.json())
            .then(data => {
                res.classList.remove('hidden');
                res.innerHTML = data.length
                    ? data.map(p => `<a href="${esc(p.url)}" class="p-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-pink-50 transition-colors bg-pink-50/50">${esc(p.name)}</a>`).join('')
                    : `<p class="text-xs text-gray-400 col-span-2 text-center py-4">No results found.</p>`;
            });
    });
</script>

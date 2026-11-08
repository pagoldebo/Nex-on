{{-- resources/views/storefront/vyra/components/search-overlay.blade.php --}}
<div id="searchOverlay" class="hidden fixed inset-0 z-[205] backdrop-blur-md flex-col p-6 md:p-12" style="background:#0E0E10f5;">
    <div class="max-w-2xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <span class="font-semibold text-xl uppercase" style="color:#F2F0EC">Search</span>
            <button onclick="closeSearch()" class="text-2xl font-light opacity-40 hover:opacity-80 transition-opacity" style="color:#F2F0EC" aria-label="Close search">&times;</button>
        </div>
        <form method="GET" action="{{ route('search') }}">
            <input id="searchInput" name="q" type="text" placeholder="Search for bralettes, thongs, sets..."
                   value="{{ request('q') }}"
                   class="w-full border-b-2 text-xl py-4 outline-none transition-colors bg-transparent"
                   style="border-color:#FF3D5A55;color:#F2F0EC;">
        </form>
        <div id="liveResults" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8 hidden"></div>
        <div class="mt-10">
            <p class="text-xs uppercase tracking-widest font-medium opacity-40 mb-4" style="color:#F2F0EC">Popular</p>
            <div class="flex flex-wrap gap-2">
                @foreach (['Mesh Bralette','High-Waist Thong','Slip Cami','Strappy Set','Sports Bra'] as $term)
                    <a href="{{ route('search') }}?q={{ $term }}" class="text-xs px-4 py-2 border transition-colors hover:opacity-80 uppercase"
                       style="border-color:#FF3D5A55;color:#F2F0EC">{{ $term }}</a>
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
                    ? data.map(p => `<a href="${esc(p.url)}" class="p-3 text-sm font-medium hover:opacity-80 transition-opacity uppercase" style="background:#FF3D5A22;color:#F2F0EC">${esc(p.name)}</a>`).join('')
                    : `<p class="text-xs opacity-40 col-span-2 text-center py-4" style="color:#F2F0EC">No results found.</p>`;
            });
    });
</script>

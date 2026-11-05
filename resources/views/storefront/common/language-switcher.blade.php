@php
    $currentLocale = app()->getLocale();
    $supportedLocales = \App\Http\Middleware\SetLocale::SUPPORTED_LOCALES;
    $activeMeta = $supportedLocales[$currentLocale] ?? $supportedLocales['en'];
@endphp

<div x-data="{ open: false }" class="relative inline-block text-left z-30" @keydown.escape.window="open = false">
    <button @click="open = !open" 
            type="button" 
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border border-current/20 hover:border-current/40 transition-colors opacity-90 hover:opacity-100"
            aria-haspopup="true"
            :aria-expanded="open.toString()">
        <span class="text-sm leading-none">{{ $activeMeta['flag'] }}</span>
        <span class="font-mono text-[11px] font-bold tracking-wider uppercase">{{ $currentLocale }}</span>
        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-44 rounded-2xl bg-white text-slate-800 shadow-xl border border-slate-100 py-1.5 z-50 text-xs focus:outline-none"
         style="display: none;">
        
        <div class="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-100">
            {{ __('Select Language') }}
        </div>

        @foreach($supportedLocales as $code => $meta)
            <a href="{{ route('locale.switch', $code) }}"
               class="flex items-center justify-between px-3 py-2 hover:bg-slate-50 transition {{ $code === $currentLocale ? 'bg-indigo-50/70 text-indigo-700 font-bold' : 'text-slate-700' }}">
                <span class="flex items-center gap-2">
                    <span class="text-sm">{{ $meta['flag'] }}</span>
                    <span>{{ $meta['native'] }}</span>
                </span>
                @if($code === $currentLocale)
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>

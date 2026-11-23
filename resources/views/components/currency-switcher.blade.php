@props([
    'variant' => 'dropdown',
    'dark' => false,
])

@php
    $currencyService = $currencyService ?? app(\App\Services\CurrencyService::class);
    $isMulti = $isMultiCurrency ?? $currencyService->isMultiCurrencyEnabled();
    $activeList = $activeCurrencies ?? $currencyService->getEnabledCurrencies();
    $current = $currentCurrency ?? $currencyService->getUserCurrencyData();
@endphp

@if($isMulti && count($activeList) > 1)
    @if($variant === 'select')
        {{-- Native Select Variant for dense forms or mobile drawers --}}
        <div class="relative inline-block text-xs">
            <select onchange="window.switchCurrency(this.value)" 
                    class="appearance-none bg-transparent py-1.5 pl-2.5 pr-6 rounded-lg font-medium border cursor-pointer focus:outline-none transition-colors {{ $dark ? 'border-white/20 text-white bg-black/40 hover:border-white/40' : 'border-slate-200 text-slate-800 bg-white hover:border-slate-300' }}">
                @foreach($activeList as $code => $cur)
                    <option value="{{ $code }}" {{ $code === $current['code'] ? 'selected' : '' }} class="bg-white text-slate-900">
                        {{ $cur['flag'] }} {{ $code }} ({{ $cur['symbol'] }})
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-1.5 flex items-center {{ $dark ? 'text-white/60' : 'text-slate-400' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
    @elseif($variant === 'mobile')
        {{-- Mobile Full-Width List Variant --}}
        <div class="space-y-1 py-2">
            <p class="text-[10px] uppercase font-bold tracking-wider opacity-60 mb-2">Select Currency</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach($activeList as $code => $cur)
                    <button type="button" 
                            onclick="window.switchCurrency('{{ $code }}')"
                            class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold border transition-all cursor-pointer {{ $code === $current['code'] ? 'border-amber-600 bg-amber-500/10 text-amber-700 font-bold' : ($dark ? 'border-white/10 text-white/80 hover:bg-white/5' : 'border-slate-200 text-slate-700 hover:bg-slate-50') }}">
                        <span class="flex items-center gap-1.5">
                            <span>{{ $cur['flag'] }}</span>
                            <span>{{ $code }}</span>
                        </span>
                        <span class="opacity-60 text-[11px]">{{ $cur['symbol'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @else
        {{-- Default Desktop Dropdown Variant --}}
        <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" 
                    @click="open = !open" 
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-all duration-200 cursor-pointer {{ $dark ? 'text-white/90 hover:text-white bg-white/10 hover:bg-white/15 border border-white/20' : 'text-slate-700 hover:text-slate-900 bg-slate-100/80 hover:bg-slate-200/80 border border-slate-200/60' }}"
                    aria-expanded="false" 
                    aria-haspopup="true"
                    title="Change Currency">
                <span class="text-sm leading-none">{{ $current['flag'] }}</span>
                <span class="font-semibold tracking-wide">{{ $current['code'] }}</span>
                <span class="opacity-60 text-[11px]">({{ $current['symbol'] }})</span>
                <svg class="w-3 h-3 opacity-60 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="open" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-xl border border-slate-200/90 py-1.5 z-[150] text-slate-800 divide-y divide-slate-100 focus:outline-none"
                 style="display: none;">
                
                <div class="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                    Switch Currency
                </div>

                <div class="max-h-64 overflow-y-auto py-1">
                    @foreach($activeList as $code => $cur)
                        <button type="button" 
                                onclick="window.switchCurrency('{{ $code }}')"
                                class="w-full flex items-center justify-between px-3 py-2 text-xs text-left hover:bg-slate-50 transition-colors {{ $code === $current['code'] ? 'bg-amber-50/70 text-amber-900 font-bold' : 'text-slate-700' }}">
                            <span class="flex items-center gap-2">
                                <span class="text-sm">{{ $cur['flag'] }}</span>
                                <span class="font-semibold">{{ $code }}</span>
                                <span class="text-[11px] text-slate-400 font-normal truncate max-w-[80px]">{{ $cur['name'] }}</span>
                            </span>
                            <span class="font-mono text-slate-600 text-xs">{{ $cur['symbol'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif

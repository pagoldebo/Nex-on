{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT COMMON ABOUT 
    ======================================================================
    File: resources/views/storefront/common/about.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
@php
  $tKey = $theme->key ?? 'saaya';
  $storyImg = \App\Models\Setting::get("{$tKey}_story_image") ?: \App\Models\Setting::get('saaya_story_image');
  $storyBadge = \App\Models\Setting::get("{$tKey}_story_badge", 'Our Story');
  $storyTitle = \App\Models\Setting::get("{$tKey}_story_title", "Crafted for Her,\nRooted in Grace");
  $storyP1 = \App\Models\Setting::get("{$tKey}_story_p1", 'Founded on the belief that what lies beneath should be as exquisite as what meets the eye, our atelier crafts intimate silhouettes designed to empower, indulge, and celebrate femininity.');
  $storyP2 = \App\Models\Setting::get("{$tKey}_story_p2", 'Every piece begins in our design studio, where timeless silhouettes meet artisanal fabric innovations and precision wire framing.');
  $stat1Num = \App\Models\Setting::get("{$tKey}_stat1_num", '100%');
  $stat1Lbl = \App\Models\Setting::get("{$tKey}_stat1_label", 'Pure Comfort');
  $stat2Num = \App\Models\Setting::get("{$tKey}_stat2_num", '50K+');
  $stat2Lbl = \App\Models\Setting::get("{$tKey}_stat2_label", 'Happy Clients');
  $stat3Num = \App\Models\Setting::get("{$tKey}_stat3_num", '100%');
  $stat3Lbl = \App\Models\Setting::get("{$tKey}_stat3_label", 'Discreet Delivery');
@endphp
<x-storefront-layout :theme="$theme" title="About Us — The Atelier Story" metaDesc="Discover the heritage of our intimate atelier, handcrafted with premium fabrics and silhouettes.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">{{ $storyBadge }}</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">{!! nl2br(e($storyTitle)) !!}</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    {{ $storyP1 }}
                </p>
            </div>

            <!-- Story Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
                <div class="rounded-3xl overflow-hidden shadow-lg border border-slate-200/60 aspect-4/5 bg-slate-100">
                    @if($storyImg)
                        <img src="{{ asset('storage/' . $storyImg) }}" alt="Atelier Craftsmanship" class="w-full h-full object-cover">
                    @else
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1000&q=80" 
                             alt="Atelier Craftsmanship" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="space-y-6">
                    <span class="text-xs font-mono text-slate-400 uppercase tracking-widest">{{ \App\Models\Setting::get("{$tKey}_about_philosophy_badge", 'Philosophy 01') }}</span>
                    <h2 class="text-2xl lg:text-3xl font-serif text-slate-900">{{ \App\Models\Setting::get("{$tKey}_about_philosophy_heading", 'Uncompromising Quality & Comfort') }}</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $storyP1 }}
                    </p>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $storyP2 }}
                    </p>
                    <div class="pt-4 border-t border-slate-200 grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-serif font-bold text-slate-900">{{ $stat1Num }}</div>
                            <div class="text-[11px] text-slate-500 uppercase tracking-wider mt-1">{{ $stat1Lbl }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-serif font-bold text-slate-900">{{ $stat2Num }}</div>
                            <div class="text-[11px] text-slate-500 uppercase tracking-wider mt-1">{{ $stat2Lbl }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-serif font-bold text-slate-900">{{ $stat3Num }}</div>
                            <div class="text-[11px] text-slate-500 uppercase tracking-wider mt-1">{{ $stat3Lbl }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pillars -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-12 border-t border-b border-slate-200">
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-serif text-lg font-bold">1</div>
                    <h3 class="font-serif text-lg text-slate-900">{{ \App\Models\Setting::get("{$tKey}_pillar1_title", 'Private & Confidential') }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ \App\Models\Setting::get("{$tKey}_pillar1_desc", 'Your privacy is sacred. All orders arrive in unmarked, tamper-evident matte boxes with no brand indicators on the exterior shipping label.') }}</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-serif text-lg font-bold">2</div>
                    <h3 class="font-serif text-lg text-slate-900">{{ \App\Models\Setting::get("{$tKey}_pillar2_title", 'Inclusive Architecture') }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ \App\Models\Setting::get("{$tKey}_pillar2_desc", 'Engineered across petite, standard, and full-bust contours to deliver unparalleled support without digging, binding, or red marks.') }}</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-serif text-lg font-bold">3</div>
                    <h3 class="font-serif text-lg text-slate-900">{{ \App\Models\Setting::get("{$tKey}_pillar3_title", 'Artisanal Finishes') }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ \App\Models\Setting::get("{$tKey}_pillar3_desc", 'Double-stitched seams, gold-plated hardware clasps, and pure silk trim details that withstand gentle machine or hand washing.') }}</p>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mt-16">
                <a href="{{ route('search') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 hover:bg-rose-950 text-white text-xs font-bold uppercase tracking-widest rounded-full transition shadow-md">
                    <span>Explore Our Collection</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>
</x-storefront-layout>

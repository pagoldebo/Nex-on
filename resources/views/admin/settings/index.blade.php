{{--
    ======================================================================
    ADMIN MODULE: SETTINGS INDEX
    ======================================================================
    File: resources/views/admin/settings/index.blade.php
    Responsibility: Back-office administration for SETTINGS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Store Settings',
    'breadcrumb' => 'Settings',
])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Store Configuration & Policy Settings</h1>
            <p class="text-sm text-slate-500 mt-1">Configure live shipping rates, tax rules, helpline details, and store operations.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Project Logo & Brand Identity -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow-sm shadow-rose-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Project Logo & Brand Identity</h2>
                    <p class="text-xs text-slate-500">Universal store logo applied automatically across all 4 themes (Buttercup, Saaya, Vyra, Lumière), invoices, and customer receipts</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-8 items-start">
                <!-- Current Logo Preview -->
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Current Active Logo</label>
                    
                    @if(!empty($settings['site_logo']))
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3 relative group">
                            <div class="p-4 bg-white rounded-lg border border-slate-200/70 shadow-xs flex items-center justify-center min-h-[90px] w-full">
                                <img id="currentLogoPreview" src="{{ \App\Models\Setting::imageUrl($settings['site_logo']) }}" alt="Store Logo" class="max-h-16 max-w-full object-contain">
                            </div>
                            <div class="flex items-center justify-between w-full pt-1">
                                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Custom Logo Active
                                </span>
                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-700 font-medium cursor-pointer">
                                    <input type="checkbox" name="remove_logo" value="1" class="w-3.5 h-3.5 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <span>Remove Logo</span>
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="p-6 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center text-center space-y-2">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">No Custom Logo Uploaded</p>
                            <p class="text-[11px] text-slate-400 max-w-[220px]">Themes are displaying their respective default stylized typography branding.</p>
                        </div>
                    @endif

                    <!-- Dynamic Live Preview on new file selection -->
                    <div id="newLogoPreviewWrapper" class="hidden p-4 rounded-xl border border-indigo-200 bg-indigo-50/40 space-y-2">
                        <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">New Selection Preview:</span>
                        <div class="p-3 bg-white rounded-lg border border-indigo-100 flex items-center justify-center min-h-[80px]">
                            <img id="newLogoPreview" src="" alt="New Logo Preview" class="max-h-14 max-w-full object-contain">
                        </div>
                    </div>
                </div>

                <!-- Logo Upload & Brand Name Inputs -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Upload New Project Logo</label>
                        <div class="relative">
                            <input type="file" name="site_logo" id="siteLogoInput" data-no-webp="true" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl cursor-pointer bg-white transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Saved directly as PNG (<code>branding/logo.png</code>) to preserve transparent raster quality and crisp branding without WebP conversion.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Store / Brand Name</label>
                        <input type="text" name="store_name" 
                               value="{{ $settings['store_name'] ?? config('app.name', 'Nex-on') }}" 
                               placeholder="e.g. {{ config('app.name', 'Nex-on') }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                        <p class="text-[11px] text-slate-400 mt-1.5">Used as logo alt text, fallback brand typography, invoice titles, and SMS/email headers.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Footer Store Bio / Brand Description</label>
                        <textarea name="footer_bio" rows="3" 
                                  placeholder="Short 1-2 sentence brand summary displayed in the footer beneath the logo across storefront themes..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">{{ $settings['footer_bio'] ?? '' }}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1.5">Universal summary displayed in the footer column below the logo across all themes. Leave empty to use theme defaults.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Footer Trust Badge 1</label>
                            <input type="text" name="footer_badge1" 
                                   value="{{ $settings['footer_badge1'] ?? '' }}" 
                                   placeholder="e.g. FSSAI Certified"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">First badge below footer bio.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Footer Trust Badge 2</label>
                            <input type="text" name="footer_badge2" 
                                   value="{{ $settings['footer_badge2'] ?? '' }}" 
                                   placeholder="e.g. 100% Non-GMO"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Second badge below footer bio.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Homepage Hero Banners & Visual Identity -->        <div id="hero-banners" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6" 
             x-data="{ 
                 activeTab: '{{ $activeTheme->key ?? 'vyra' }}',
                 previewVyra: null,
                 previewVyraIsVideo: false,
                 previewButtercup: null,
                 previewButtercupSlide1: null,
                 previewButtercupSlide2: null,
                 previewButtercupSlide3: null,
                 previewButtercupSlide4: null,
                 previewButtercupSlide5: null,
                 activeButtercupSlideTab: 1,
                 previewLumiere: null,
                 previewSaaya: null,
                 previewSaayaStory: null,
                 previewVyraStory: null,
                 previewButtercupStory: null,
                 previewLumiereStory: null,
                 previewOrganicHero: null,
                 previewOrganicStory: null,
                 previewOrganicAd1: null,
                 previewOrganicAd2: null,
                 previewOrganicAd3: null
             }">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-600 text-white flex items-center justify-center font-bold shadow-sm shadow-rose-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">Homepage Hero Banners &amp; Visuals</h2>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Live Theme: {{ ucfirst($activeTheme->key ?? 'vyra') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Upload custom hero banner graphics (replaces the default 3D/SVG illustrations) and customize headlines per theme</p>
                    </div>
                </div>

                <!-- Theme Tabs (Dynamically populated from Template model for current & future themes) -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold flex-wrap gap-1">
                    @foreach($templates ?? [] as $tmpl)
                        <button type="button" @click="activeTab = '{{ $tmpl->key }}'" 
                                :class="activeTab === '{{ $tmpl->key }}' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                            <span>{{ $tmpl->name }}</span>
                            @if($tmpl->is_active)
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Active Theme"></span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- VYRA HERO BANNER TAB -->
            <div x-show="activeTab === 'vyra'" class="space-y-8">
                <!-- Section 1: Hero Banner -->
                <div class="space-y-6">
                    <div class="p-3 bg-[#0E0E10] text-[#F2F0EC] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#FF3D5A]"></span>
                            <span class="font-bold">Vyra Hero Banner Section</span>
                            <span class="text-[#D4FF3D] font-mono text-[10px]">("Wear Your Power" / Drop 03)</span>
                        </div>
                        <a href="{{ route('home') }}" target="_blank" class="text-[#D4FF3D] hover:underline flex items-center gap-1 text-[11px]">
                            <span>View Live Storefront</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>

                    @php
                        $vyraCustomMedia = $settings['vyra_hero_image'] ?? ($settings['vyra_hero_video'] ?? '');
                        $vyraActiveUrl = \App\Models\Setting::imageUrl($vyraCustomMedia);
                        $vyraDefaultVideoUrl = file_exists(public_path('images/slider/banner.mp4')) ? asset('images/slider/banner.mp4') : null;
                        if (!$vyraActiveUrl && $vyraDefaultVideoUrl) {
                            $vyraActiveUrl = $vyraDefaultVideoUrl;
                        }
                        $vyraActiveExt = strtolower(pathinfo(parse_url($vyraActiveUrl ?? '', PHP_URL_PATH), PATHINFO_EXTENSION));
                        $vyraActiveIsVideo = in_array($vyraActiveExt, ['mp4', 'webm', 'mov', 'ogg'], true);
                    @endphp

                    <div class="grid lg:grid-cols-2 gap-8 items-start">
                        <!-- Background Media Upload & Preview (Image or Video) -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Vyra Hero Image &amp; Video Background
                                </label>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">Full Width</span>
                            </div>

                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-900 text-white flex flex-col items-center justify-center gap-3">
                                {{-- Preview when a new file has just been chosen --}}
                                <template x-if="previewVyra">
                                    <div class="relative w-full aspect-[16/9] rounded-xl overflow-hidden shadow-lg border border-slate-700 bg-black flex items-center justify-center">
                                        <template x-if="previewVyraIsVideo">
                                            <video :src="previewVyra" class="w-full h-full object-cover" autoplay muted loop playsinline></video>
                                        </template>
                                        <template x-if="!previewVyraIsVideo">
                                            <img :src="previewVyra" class="w-full h-full object-cover" alt="New Vyra Preview">
                                        </template>
                                        <span class="absolute bottom-2 left-2 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">
                                            New <span x-text="previewVyraIsVideo ? 'Video' : 'Image'"></span> Selected (Unsaved Preview)
                                        </span>
                                    </div>
                                </template>

                                {{-- Live Media when no new file is chosen --}}
                                <template x-if="!previewVyra">
                                    <div class="w-full">
                                        @if(!empty($vyraActiveUrl))
                                            <div class="relative w-full aspect-[16/9] rounded-xl overflow-hidden shadow-lg border border-slate-700 bg-black">
                                                @if($vyraActiveIsVideo)
                                                    <video class="w-full h-full object-cover" autoplay muted loop playsinline poster="{{ asset('images/slider/banner-1.jpg') }}">
                                                        <source src="{{ $vyraActiveUrl }}" type="video/{{ $vyraActiveExt === 'mov' ? 'quicktime' : ($vyraActiveExt === 'ogg' ? 'ogg' : ($vyraActiveExt === 'webm' ? 'webm' : 'mp4')) }}">
                                                    </video>
                                                    <span class="absolute bottom-2 left-2 {{ !empty($vyraCustomMedia) ? 'bg-emerald-600' : 'bg-slate-800' }} text-white text-[10px] font-bold px-2 py-0.5 rounded shadow backdrop-blur-xs">
                                                        {{ !empty($vyraCustomMedia) ? 'Active Custom Video' : 'Default Video (banner.mp4)' }}
                                                    </span>
                                                @else
                                                    <img src="{{ $vyraActiveUrl }}" class="w-full h-full object-cover" alt="Current Vyra Hero">
                                                    <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">
                                                        Active Custom Image
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="w-full aspect-[16/9] bg-[#1A1A1D] rounded-xl flex flex-col items-center justify-center p-6 text-center border border-[#FF3D5A]/30">
                                                <p class="text-xs text-[#F2F0EC]/80 font-bold">No Media Configured</p>
                                                <p class="text-[11px] text-[#F2F0EC]/40 mt-1">Upload a background image or video below</p>
                                            </div>
                                        @endif
                                    </div>
                                </template>

                                @if(!empty($vyraCustomMedia))
                                    <div class="flex items-center justify-between w-full pt-2 border-t border-slate-800">
                                        <span class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>Custom {{ $vyraActiveIsVideo ? 'Video' : 'Image' }} Running</span>
                                        </span>
                                        <label class="inline-flex items-center gap-1.5 text-xs text-rose-400 hover:text-rose-300 font-medium cursor-pointer">
                                            <input type="checkbox" name="remove_vyra_hero_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded border-slate-700 bg-slate-800">
                                            <span>Reset to Default Video</span>
                                        </label>
                                    </div>
                                @else
                                    <div class="flex items-center justify-between w-full pt-2 border-t border-slate-800">
                                        <span class="text-[11px] text-slate-400 font-mono">public/images/slider/banner.mp4</span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#0E0E10] bg-[#D4FF3D] px-2 py-0.5 rounded-full">Default Active</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <input type="file" name="vyra_hero_image" 
                                       accept="image/png,image/jpeg,image/webp,image/avif,image/svg+xml,video/mp4,video/webm,video/ogg,video/quicktime"
                                       @change="if ($event.target.files && $event.target.files[0]) {
                                           const f = $event.target.files[0];
                                           previewVyra = URL.createObjectURL(f);
                                           previewVyraIsVideo = f.type.startsWith('video/') || /\.(mp4|webm|mov|ogg)$/i.test(f.name);
                                       }"
                                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl cursor-pointer bg-white transition">
                                <p class="text-[11px] text-slate-400 mt-1.5">Upload a full-width background video (MP4/WEBM up to 50MB) or photo (JPG/PNG/WEBP/AVIF up to 10MB). Runs full-width in the hero slider.</p>
                            </div>
                        </div>

                        <!-- Hero Text Settings -->
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Vyra Hero Copy &amp; Call To Action
                            </label>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Top Tagline / Badge</label>
                                <input type="text" name="vyra_hero_badge" 
                                       value="{{ $settings['vyra_hero_badge'] ?? 'Drop 03 / Out Now' }}" 
                                       placeholder="Drop 03 / Out Now"
                                       class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Main Headline (Line 2 will highlight in Red/Neon)</label>
                                <textarea name="vyra_hero_title" rows="2" 
                                          placeholder="Wear Your&#10;Power"
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">{{ $settings['vyra_hero_title'] ?? "Wear Your\nPower" }}</textarea>
                                <p class="text-[11px] text-slate-400">Put each line on a new row (e.g. "Wear Your" then "Power").</p>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Hero Description / Subtitle</label>
                                <textarea name="vyra_hero_subtitle" rows="3" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">{{ $settings['vyra_hero_subtitle'] ?? "Bold cuts. Mesh, lace, and second-skin fabrics built for a generation that doesn't ask permission." }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Primary Button Text</label>
                                    <input type="text" name="vyra_hero_btn_text" 
                                           value="{{ $settings['vyra_hero_btn_text'] ?? 'Shop Drop 03' }}" 
                                           class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Primary Button Link URL</label>
                                    <input type="text" name="vyra_hero_btn_url" 
                                           value="{{ $settings['vyra_hero_btn_url'] ?? '#products' }}" 
                                           class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-mono text-xs focus:border-indigo-500 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Explore (Secondary) Button Text</label>
                                    <input type="text" name="vyra_hero_btn2_text" 
                                           value="{{ $settings['vyra_hero_btn2_text'] ?? 'Explore' }}" 
                                           placeholder="Explore"
                                           class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Explore Button Link URL</label>
                                    <input type="text" name="vyra_hero_btn2_url" 
                                           value="{{ $settings['vyra_hero_btn2_url'] ?? route('search') }}" 
                                           placeholder="/search"
                                           class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-mono text-xs focus:border-indigo-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Vyra Curated Featured Picks Section -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#1A1A1D] text-[#F2F0EC] border border-[#FF3D5A]/30 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D4FF3D]"></span>
                            <span class="font-bold">Vyra "Curated Featured Picks" Section</span>
                            <span class="text-xs text-[#D4FF3D] font-mono">("Raw Power &amp; Neon Aesthetics")</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Badge</label>
                            <input type="text" name="vyra_curated_badge" 
                                   value="{{ $settings['vyra_curated_badge'] ?? 'Curated' }}" 
                                   placeholder="Curated"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Main Section Title</label>
                            <input type="text" name="vyra_curated_title" 
                                   value="{{ $settings['vyra_curated_title'] ?? 'Curated Featured Picks' }}" 
                                   placeholder="Curated Featured Picks"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Subtitle / Description</label>
                            <input type="text" name="vyra_curated_subtitle" 
                                   value="{{ $settings['vyra_curated_subtitle'] ?? 'Second-skin fabrics, fearless cuts, and midnight noir lingerie engineered for an unfiltered presence.' }}" 
                                   placeholder="Second-skin fabrics..."
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section: Vyra Sale Section -->
                <div class="space-y-6 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#1A1A1D] border border-[#FF3D5A]/40 text-[#F2F0EC] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#FF3D5A]"></span>
                            <span class="font-bold">Vyra "Flash Sale" Section (Category Tabs &amp; Sale Cards)</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Kicker / Badge</label>
                            <input type="text" name="vyra_sale_kicker" 
                                   value="{{ $settings['vyra_sale_kicker'] ?? '⚡ Limited Drops' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Title</label>
                            <input type="text" name="vyra_sale_title" 
                                   value="{{ $settings['vyra_sale_title'] ?? 'Special Sale Offers' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Subtitle</label>
                            <input type="text" name="vyra_sale_subtitle" 
                                   value="{{ $settings['vyra_sale_subtitle'] ?? 'High-impact power silhouettes, sculpting mesh, and daring cuts at exclusive promotional drops.' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Vyra Brand Story & About Us Page Link -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="p-4 bg-gradient-to-r from-slate-900 to-[#1A1A1D] text-[#F2F0EC] rounded-2xl border border-[#FF3D5A]/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#FF3D5A]/20 border border-[#FF3D5A]/40 text-[#FF3D5A] flex items-center justify-center shrink-0 font-bold">✦</div>
                            <div>
                                <p class="text-xs font-bold text-white">Vyra "Our Story" &amp; About Us Page Content</p>
                                <p class="text-[11px] text-[#F2F0EC]/60">Customize Vyra story image, philosophy, 3 live counters, and 3 brand pillars in the dedicated About Us editor.</p>
                            </div>
                        </div>
                        <a href="#about-settings" onclick="window.dispatchEvent(new CustomEvent('switch-about-tab', { detail: 'vyra' }))" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-[#FF3D5A] hover:bg-[#ff5770] text-[#0E0E10] font-bold rounded-xl text-xs shrink-0 transition">
                            <span>Manage in About Us Settings</span>
                            <span>↓</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- BUTTERCUP HERO BANNER TAB -->
            <div x-show="activeTab === 'buttercup'" class="space-y-8" x-cloak>
                <!-- Section 1: Hero Slider (5 Full Background Slides with 3-Second Autoplay) -->
                <div class="space-y-6">
                    <div class="p-3 bg-[#FBE7E4] text-[#4A3B3A] rounded-xl flex items-center justify-between text-xs border border-[#D89AA0]/30 shadow-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D89AA0]"></span>
                            <span class="font-bold">Buttercup Hero Slider Section</span>
                            <span class="font-mono text-[10px] bg-white/70 text-[#B97680] font-semibold px-2 py-0.5 rounded-full">5 Full Background Slides • 3s Auto-Scroll</span>
                        </div>
                        <span class="text-[11px] text-[#4A3B3A]/70 hidden sm:inline">Active Theme: Buttercup</span>
                    </div>

                    {{-- Slide Selector Pills (Slide 1 to 5) --}}
                    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
                        <span class="text-xs font-semibold text-slate-500 mr-2">Configure Slide:</span>
                        @for($s = 1; $s <= 5; $s++)
                            <button type="button" 
                                    @click="activeButtercupSlideTab = {{ $s }}" 
                                    :class="activeButtercupSlideTab === {{ $s }} ? 'bg-[#D89AA0] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="px-4 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5">
                                <span>Slide {{ $s }}</span>
                                @if(!empty($settings["buttercup_slide_{$s}_image"]) || ($s === 1 && !empty($settings['buttercup_hero_image'])))
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                @endif
                            </button>
                        @endfor
                    </div>

                    @php
                        $buttercupDefaultSlides = [
                            1 => [
                                'badge' => 'New Season Edit',
                                'title' => "Comfort that\nFeels Like Home",
                                'desc'  => 'Buttery-soft everyday lingerie, made for real bodies and real days — because comfort should never be a compromise.',
                                'btn1'  => 'Shop Now',
                                'url1'  => '#products',
                                'btn2'  => 'Explore',
                                'url2'  => route('search'),
                            ],
                            2 => [
                                'badge' => 'Cloud-Soft Intimates',
                                'title' => "Second-Skin Ease,\nAll Day Long",
                                'desc'  => 'Breathable modal and organic cotton with zero pinch. Gentle, weightless support made to move with you.',
                                'btn1'  => 'Shop Intimates',
                                'url1'  => '#products',
                                'btn2'  => 'View Collection',
                                'url2'  => route('featured.products'),
                            ],
                            3 => [
                                'badge' => 'Pure Indulgence',
                                'title' => "Soft as a Whisper,\nCrafted for You",
                                'desc'  => 'Delicate lace accents meet featherlight silhouettes. Experience the sublime touch of effortless elegance every day.',
                                'btn1'  => 'Explore Bestsellers',
                                'url1'  => '#products',
                                'btn2'  => 'Discover More',
                                'url2'  => route('search'),
                            ],
                            4 => [
                                'badge' => 'Wire-Free Freedom',
                                'title' => "Zero Pinching,\n100% Pure Comfort",
                                'desc'  => 'Say goodbye to painful underwires forever. Our contour bands adapt harmoniously to your natural curves.',
                                'btn1'  => 'Shop Wire-Free',
                                'url1'  => '#products',
                                'btn2'  => 'Our Fit Guide',
                                'url2'  => route('search'),
                            ],
                            5 => [
                                'badge' => 'Lounge & Sleep',
                                'title' => "Dreamy Loungewear\nfor Cozy Evenings",
                                'desc'  => 'From quiet morning coffee to tranquil midnight slumber — wrap yourself in the soothing bliss of buttery loungewear.',
                                'btn1'  => 'Shop Loungewear',
                                'url1'  => '#products',
                                'btn2'  => 'Explore All',
                                'url2'  => route('featured.products'),
                            ],
                        ];
                    @endphp

                    @for($s = 1; $s <= 5; $s++)
                        @php
                            $slideDef = $buttercupDefaultSlides[$s];
                            $slideImg = $settings["buttercup_slide_{$s}_image"] ?? ($s === 1 ? ($settings['buttercup_hero_image'] ?? '') : '');
                            $previewVar = "previewButtercupSlide{$s}";
                            $removeVar = "remove_buttercup_slide_{$s}_image";
                            $badgeVal = $settings["buttercup_slide_{$s}_badge"] ?? ($s === 1 ? ($settings['buttercup_hero_badge'] ?? $slideDef['badge']) : $slideDef['badge']);
                            $titleVal = $settings["buttercup_slide_{$s}_title"] ?? ($s === 1 ? ($settings['buttercup_hero_title'] ?? $slideDef['title']) : $slideDef['title']);
                            $descVal  = $settings["buttercup_slide_{$s}_subtitle"] ?? ($s === 1 ? ($settings['buttercup_hero_subtitle'] ?? $slideDef['desc']) : $slideDef['desc']);
                            $btn1Val  = $settings["buttercup_slide_{$s}_btn1_text"] ?? $slideDef['btn1'];
                            $url1Val  = $settings["buttercup_slide_{$s}_btn1_url"] ?? $slideDef['url1'];
                            $btn2Val  = $settings["buttercup_slide_{$s}_btn2_text"] ?? $slideDef['btn2'];
                            $url2Val  = $settings["buttercup_slide_{$s}_btn2_url"] ?? $slideDef['url2'];
                        @endphp
                        <div x-show="activeButtercupSlideTab === {{ $s }}" class="space-y-6">
                            <div class="grid lg:grid-cols-2 gap-8 items-start">
                                {{-- Slide Image --}}
                                <div class="space-y-4">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                        Slide {{ $s }} Full Background Image
                                    </label>
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                                        <template x-if="{{ $previewVar }}">
                                            <div class="relative w-full aspect-[16/9] max-w-md rounded-xl overflow-hidden shadow-md border border-slate-300">
                                                <img :src="{{ $previewVar }}" class="w-full h-full object-cover">
                                            </div>
                                        </template>
                                        <template x-if="!{{ $previewVar }}">
                                            <div>
                                                @if(!empty($slideImg))
                                                    <div class="relative w-full aspect-[16/9] max-w-md rounded-xl overflow-hidden shadow-md border border-slate-300">
                                                        <img src="{{ \App\Models\Setting::imageUrl($slideImg) }}" class="w-full h-full object-cover">
                                                    </div>
                                                @else
                                                    <div class="relative w-full aspect-[16/9] max-w-md rounded-xl overflow-hidden shadow-md border border-[#D89AA0]/30 bg-cover bg-center"
                                                         style="background-image: url('{{ asset("images/slider/buttercup/slide-{$s}.jpg") }}');">
                                                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center p-4">
                                                            <span class="text-xs text-white font-bold bg-black/60 px-3 py-1 rounded-full backdrop-blur-xs">Default Slide {{ $s }} Image</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </template>
                                        @if(!empty($slideImg))
                                            <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                                <span class="text-[11px] text-emerald-600 font-semibold">Custom Uploaded Active</span>
                                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
                                                    <input type="checkbox" name="{{ $removeVar }}" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                                    <span>Revert to Default</span>
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="buttercup_slide_{{ $s }}_image" accept="image/*"
                                           @change="{{ $previewVar }} = URL.createObjectURL($event.target.files[0])"
                                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 border border-slate-200 rounded-xl cursor-pointer bg-white">
                                </div>

                                {{-- Slide Copy & CTAs --}}
                                <div class="space-y-4">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                        Slide {{ $s }} Copy &amp; Buttons
                                    </label>

                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Badge / Tagline</label>
                                        <input type="text" name="buttercup_slide_{{ $s }}_badge" 
                                               value="{{ $badgeVal }}" 
                                               class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">
                                        @if($s === 1)
                                            <input type="hidden" name="buttercup_hero_badge" value="{{ $badgeVal }}">
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Headline</label>
                                        <textarea name="buttercup_slide_{{ $s }}_title" rows="2" 
                                                  class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-rose-400 outline-none">{{ $titleVal }}</textarea>
                                        @if($s === 1)
                                            <input type="hidden" name="buttercup_hero_title" value="{{ $titleVal }}">
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Subtitle / Description</label>
                                        <textarea name="buttercup_slide_{{ $s }}_subtitle" rows="2" 
                                                  class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">{{ $descVal }}</textarea>
                                        @if($s === 1)
                                            <input type="hidden" name="buttercup_hero_subtitle" value="{{ $descVal }}">
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 pt-2">
                                        <div>
                                            <label class="block text-xs text-slate-600 mb-1">Primary Button Text</label>
                                            <input type="text" name="buttercup_slide_{{ $s }}_btn1_text" 
                                                   value="{{ $btn1Val }}" 
                                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:border-rose-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-600 mb-1">Primary Button Link</label>
                                            <input type="text" name="buttercup_slide_{{ $s }}_btn1_url" 
                                                   value="{{ $url1Val }}" 
                                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:border-rose-400 outline-none">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-slate-600 mb-1">Secondary Button Text</label>
                                            <input type="text" name="buttercup_slide_{{ $s }}_btn2_text" 
                                                   value="{{ $btn2Val }}" 
                                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:border-rose-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-600 mb-1">Secondary Button Link</label>
                                            <input type="text" name="buttercup_slide_{{ $s }}_btn2_url" 
                                                   value="{{ $url2Val }}" 
                                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:border-rose-400 outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- Section 2: Buttercup Curated Featured Picks Section -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#FFF5F6] border border-[#D89AA0]/40 text-[#4A3B3A] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D89AA0]"></span>
                            <span class="font-bold">Buttercup "Curated Featured Picks" Section</span>
                            <span class="text-xs text-[#B97680] font-medium">("Everyday Soft Intimates")</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Badge</label>
                            <input type="text" name="buttercup_curated_badge" 
                                   value="{{ $settings['buttercup_curated_badge'] ?? 'Curated' }}" 
                                   placeholder="Curated"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Main Section Title</label>
                            <input type="text" name="buttercup_curated_title" 
                                   value="{{ $settings['buttercup_curated_title'] ?? 'Curated Featured Picks' }}" 
                                   placeholder="Curated Featured Picks"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-rose-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Subtitle / Description</label>
                            <input type="text" name="buttercup_curated_subtitle" 
                                   value="{{ $settings['buttercup_curated_subtitle'] ?? 'Everyday cloud-soft intimates, wire-free comfort, and breathable cotton modal sets made for real bodies.' }}" 
                                   placeholder="Everyday cloud-soft..."
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section: Buttercup Sale Section -->
                <div class="space-y-6 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#FFF5F6] border border-[#D89AA0]/40 text-[#4A3B3A] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D89AA0]"></span>
                            <span class="font-bold">Buttercup "Flash Sale" Section (Category Tabs &amp; Sale Cards)</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Kicker / Badge</label>
                            <input type="text" name="buttercup_sale_kicker" 
                                   value="{{ $settings['buttercup_sale_kicker'] ?? 'Limited Time Deals' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Title</label>
                            <input type="text" name="buttercup_sale_title" 
                                   value="{{ $settings['buttercup_sale_title'] ?? 'Special Sale Offers' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-rose-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Subtitle</label>
                            <input type="text" name="buttercup_sale_subtitle" 
                                   value="{{ $settings['buttercup_sale_subtitle'] ?? 'Explore cloud-soft comfort sets and everyday silhouettes at celebratory sale prices.' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Buttercup Brand Story & About Us Page Link -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="p-4 bg-[#FFF5F6] text-[#4A3B3A] rounded-2xl border border-[#D89AA0]/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#D89AA0]/20 border border-[#D89AA0]/50 text-[#B97680] flex items-center justify-center shrink-0 font-bold">🌸</div>
                            <div>
                                <p class="text-xs font-bold text-[#4A3B3A]">Buttercup "Our Story" &amp; About Us Page Content</p>
                                <p class="text-[11px] text-[#4A3B3A]/70">Customize studio image, promise heading, 3 comfort metrics, and 3 brand pillars in the dedicated About Us editor.</p>
                            </div>
                        </div>
                        <a href="#about-settings" onclick="window.dispatchEvent(new CustomEvent('switch-about-tab', { detail: 'buttercup' }))" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-[#D89AA0] hover:bg-[#B97680] text-white font-bold rounded-xl text-xs shrink-0 transition shadow-xs">
                            <span>Manage in About Us Settings</span>
                            <span>↓</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- LUMIÈRE HERO BANNER TAB -->
            <div x-show="activeTab === 'lumiere'" class="space-y-8" x-cloak>
                <!-- Section 1: Hero Banner -->
                <div class="space-y-6">
                    <div class="p-3 bg-[#FFE6A5] text-gray-900 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                            <span class="font-bold">Lumière Hero Banner Section</span>
                            <span class="font-mono text-[10px]">("Silk that feels like light")</span>
                        </div>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-8 items-start">
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Lumière Hero Image</label>
                            @php $lumImg = $settings['lumiere_hero_image'] ?? ''; @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                                <template x-if="previewLumiere">
                                    <div class="relative w-full aspect-[4/5] max-w-[260px] rounded-[32px] overflow-hidden shadow-md">
                                        <img :src="previewLumiere" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <template x-if="!previewLumiere">
                                    <div>
                                        @if(!empty($lumImg))
                                            <div class="relative w-full aspect-[4/5] max-w-[260px] rounded-[32px] overflow-hidden shadow-md">
                                                <img src="{{ \App\Models\Setting::imageUrl($lumImg) }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-full aspect-[4/5] w-56 bg-white/60 rounded-[32px] flex flex-col items-center justify-center p-6 text-center">
                                                <p class="text-xs text-gray-800 font-bold">Default Silk Graphic</p>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                                @if(!empty($lumImg))
                                    <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                        <span class="text-[11px] text-emerald-600 font-semibold">Active</span>
                                        <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
                                            <input type="checkbox" name="remove_lumiere_hero_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                            <span>Remove Image</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="lumiere_hero_image" accept="image/*"
                                   @change="previewLumiere = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-pink-50 file:text-pink-700 border border-slate-200 rounded-xl cursor-pointer bg-white">
                        </div>

                        <!-- Hero Text Settings -->
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Lumière Hero Copy
                            </label>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Tagline / Badge</label>
                                <input type="text" name="lumiere_hero_badge" 
                                       value="{{ $settings['lumiere_hero_badge'] ?? 'The Festive Edit' }}" 
                                       class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Main Headline</label>
                                <textarea name="lumiere_hero_title" rows="2" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-pink-400 outline-none">{{ $settings['lumiere_hero_title'] ?? "Silk that\nfeels like light." }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Hero Subtitle</label>
                                <textarea name="lumiere_hero_subtitle" rows="3" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">{{ $settings['lumiere_hero_subtitle'] ?? 'Sustainably made silk lingerie, finished by hand in India. Everyday elegance, no compromise.' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Lumière Curated Featured Picks Section -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#FFF5F8] border border-pink-200 text-gray-900 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                            <span class="font-bold">Lumière "Curated Featured Picks" Section</span>
                            <span class="text-xs text-pink-600 font-serif italic">("Hand-finished Pure Mulberry Silk")</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Badge</label>
                            <input type="text" name="lumiere_curated_badge" 
                                   value="{{ $settings['lumiere_curated_badge'] ?? 'Curated' }}" 
                                   placeholder="Curated"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Main Section Title</label>
                            <input type="text" name="lumiere_curated_title" 
                                   value="{{ $settings['lumiere_curated_title'] ?? 'Curated Featured Picks' }}" 
                                   placeholder="Curated Featured Picks"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-pink-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Subtitle / Description</label>
                            <input type="text" name="lumiere_curated_subtitle" 
                                   value="{{ $settings['lumiere_curated_subtitle'] ?? 'Hand-finished pure mulberry silk, delicate French lace, and heirloom silhouettes curated for effortless elegance.' }}" 
                                   placeholder="Hand-finished pure mulberry silk..."
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section: Lumière Sale Section -->
                <div class="space-y-6 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#FFF5F8] border border-pink-200 text-gray-900 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                            <span class="font-bold">Lumière "Flash Sale" Section (Category Tabs &amp; Sale Cards)</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Kicker / Badge</label>
                            <input type="text" name="lumiere_sale_kicker" 
                                   value="{{ $settings['lumiere_sale_kicker'] ?? 'Limited Time Deals' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Title</label>
                            <input type="text" name="lumiere_sale_title" 
                                   value="{{ $settings['lumiere_sale_title'] ?? 'Special Sale Offers' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-pink-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Subtitle</label>
                            <input type="text" name="lumiere_sale_subtitle" 
                                   value="{{ $settings['lumiere_sale_subtitle'] ?? 'Hand-finished French lace, fine silk, and fine nightwear at celebratory promotional prices.' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Lumière Brand Story & About Us Page Link -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="p-4 bg-[#FFF0F3] text-gray-900 rounded-2xl border border-pink-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-pink-100 border border-pink-300 text-pink-600 flex items-center justify-center shrink-0 font-bold">✨</div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">Lumière "Our Story" &amp; About Us Page Content</p>
                                <p class="text-[11px] text-gray-600">Customize silk atelier image, craft roots heading, 3 live counters, and 3 brand pillars in the dedicated About Us editor.</p>
                            </div>
                        </div>
                        <a href="#about-settings" onclick="window.dispatchEvent(new CustomEvent('switch-about-tab', { detail: 'lumiere' }))" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-bold rounded-xl text-xs shrink-0 transition shadow-xs">
                            <span>Manage in About Us Settings</span>
                            <span>↓</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SAAYA THEME TAB (HERO + OUR STORY REF SECTION) -->
            <div x-show="activeTab === 'saaya'" class="space-y-8" x-cloak>
                <!-- Section 1: Hero Banner -->
                <div class="space-y-6">
                    <div class="p-3 bg-[#3D0C11] text-[#F9F7F2] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#C9A24B]"></span>
                            <span class="font-bold text-[#C9A24B]">Saaya Hero Banner Section</span>
                            <span class="font-mono text-[10px] text-ivory/70">("Draped in Gold &amp; Grace")</span>
                        </div>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-8 items-start">
                        <!-- Hero Image Upload -->
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Saaya Hero Image (Right Foil Frame)</label>
                            @php $saayaImg = $settings['saaya_hero_image'] ?? ''; @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                                <template x-if="previewSaaya">
                                    <div class="relative w-full aspect-[3/4] max-w-[260px] rounded-xl overflow-hidden shadow-md">
                                        <img :src="previewSaaya" class="w-full h-full object-cover">
                                        <span class="absolute bottom-2 left-2 bg-[#C9A24B] text-[#3D0C11] text-[10px] font-bold px-2 py-0.5 rounded shadow">New Preview</span>
                                    </div>
                                </template>
                                <template x-if="!previewSaaya">
                                    <div>
                                        @if(!empty($saayaImg))
                                            <div class="relative w-full aspect-[3/4] max-w-[260px] rounded-xl overflow-hidden shadow-md">
                                                <img src="{{ \App\Models\Setting::imageUrl($saayaImg) }}" class="w-full h-full object-cover">
                                                <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                            </div>
                                        @else
                                            <div class="w-full aspect-[3/4] w-56 bg-[#3D0C11] rounded-xl flex flex-col items-center justify-center p-6 text-center border border-[#C9A24B]/30">
                                                <svg class="w-20 h-24 text-[#C9A24B]/40 mb-2" fill="currentColor" viewBox="0 0 100 130">
                                                    <ellipse cx="50" cy="30" rx="18" ry="22"/>
                                                    <path d="M20 55 Q35 45 50 48 Q65 45 80 55 L85 115 Q65 125 50 122 Q35 125 15 115 Z"/>
                                                </svg>
                                                <p class="text-xs text-[#C9A24B] font-bold">Default Foil Silhouette</p>
                                                <p class="text-[11px] text-[#F9F7F2]/60 mt-1">Upload a model/product image to replace this placeholder</p>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                                @if(!empty($saayaImg))
                                    <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                        <span class="text-[11px] text-emerald-600 font-semibold">Active</span>
                                        <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
                                            <input type="checkbox" name="remove_saaya_hero_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                            <span>Remove Image</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="saaya_hero_image" accept="image/*"
                                   @change="previewSaaya = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#3D0C11] file:text-[#C9A24B] border border-slate-200 rounded-xl cursor-pointer bg-white">
                        </div>

                        <!-- Hero Text Settings -->
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Saaya Hero Copy</label>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Tagline / Badge</label>
                                <input type="text" name="saaya_hero_badge" 
                                       value="{{ $settings['saaya_hero_badge'] ?? 'Crafted for Her' }}" 
                                       class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Main Headline</label>
                                <textarea name="saaya_hero_title" rows="2" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-amber-600 outline-none">{{ $settings['saaya_hero_title'] ?? "Draped in\nGold & Grace" }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Hero Subtitle</label>
                                <textarea name="saaya_hero_subtitle" rows="3" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">{{ $settings['saaya_hero_subtitle'] ?? 'Handcrafted Indian lingerie rooted in tradition, elevated by craft. Each piece is an heirloom waiting to be worn.' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Saaya Curated Featured Picks Section -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#FAF8F5] border border-[#C9A24B]/30 text-[#3D0C11] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#C9A24B]"></span>
                            <span class="font-bold">Saaya "Curated Featured Picks" Section</span>
                            <span class="text-xs text-amber-800 font-serif italic">("Heirlooms &amp; Artisanal Silhouettes")</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Badge / Tag</label>
                            <input type="text" name="saaya_curated_badge" 
                                   value="{{ $settings['saaya_curated_badge'] ?? 'Curated' }}" 
                                   placeholder="Curated"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Main Section Title</label>
                            <input type="text" name="saaya_curated_title" 
                                   value="{{ $settings['saaya_curated_title'] ?? 'Curated Featured Picks' }}" 
                                   placeholder="Curated Featured Picks"
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-amber-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Section Subtitle / Description</label>
                            <input type="text" name="saaya_curated_subtitle" 
                                   value="{{ $settings['saaya_curated_subtitle'] ?? 'Handcrafted heirlooms shaped with pure mulberry silk, delicate jaali embroidery, and timeless silhouettes.' }}" 
                                   placeholder="Handcrafted heirlooms..."
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section: Saaya Sale Section -->
                <div class="space-y-6 pt-6 border-t border-slate-200">
                    <div class="p-3 bg-[#FAF8F5] border border-[#C9A24B]/40 text-[#3D0C11] rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#C9A24B]"></span>
                            <span class="font-bold">Saaya "Flash Sale" Section (Category Tabs &amp; Sale Cards)</span>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Kicker / Badge</label>
                            <input type="text" name="saaya_sale_kicker" 
                                   value="{{ $settings['saaya_sale_kicker'] ?? 'Limited Time Deals' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Title</label>
                            <input type="text" name="saaya_sale_title" 
                                   value="{{ $settings['saaya_sale_title'] ?? 'Special Sale Offers' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-amber-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Sale Section Subtitle</label>
                            <input type="text" name="saaya_sale_subtitle" 
                                   value="{{ $settings['saaya_sale_subtitle'] ?? 'Exclusive heirlooms and seasonal lingerie edits at celebratory promotional prices.' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Saaya Brand Story & About Us Page Link -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="p-4 bg-[#FAF8F5] text-[#3D0C11] rounded-2xl border border-[#C9A24B]/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#C9A24B]/20 border border-[#C9A24B]/50 text-[#C9A24B] flex items-center justify-center shrink-0 font-bold">⚜️</div>
                            <div>
                                <p class="text-xs font-bold text-[#3D0C11]">Saaya "Our Story" &amp; About Us Page Content</p>
                                <p class="text-[11px] text-[#3D0C11]/70">Customize foil frame image, heritage mastery heading, 3 atelier counters, and 3 brand pillars in the dedicated About Us editor.</p>
                            </div>
                        </div>
                        <a href="#about-settings" onclick="window.dispatchEvent(new CustomEvent('switch-about-tab', { detail: 'saaya' }))" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-[#C9A24B] hover:bg-[#b38e3a] text-[#3D0C11] font-bold rounded-xl text-xs shrink-0 transition shadow-xs">
                            <span>Manage in About Us Settings</span>
                            <span>↓</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ORGANIC THEME CONTENT & HERO BANNER TAB -->
            <div x-show="activeTab === 'organic'" class="space-y-8" x-cloak>

                <!-- Top Bar -->
                <div class="p-3 bg-emerald-950 text-white rounded-xl flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <span class="font-bold text-emerald-300">Organic Market Theme Content &amp; Visuals</span>
                        <span class="text-emerald-400 font-mono text-[10px]">("100% Certified Farm-to-Table")</span>
                    </div>
                    <a href="{{ route('home') }}" target="_blank" class="text-emerald-300 hover:underline flex items-center gap-1 text-[11px]">
                        <span>View Live Storefront</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <!-- Section 1: Hero Banner & Farm Metrics -->
                <div class="space-y-6 p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h3 class="text-sm font-bold text-slate-800">1. Hero Banner Headline, Background &amp; Purity Counters</h3>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-8 items-start">
                        <!-- Hero Image Upload -->
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Organic Hero Background Graphic</label>
                            @php $organicImg = $settings['organic_hero_image'] ?? ''; @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                                <template x-if="previewOrganicHero">
                                    <div class="relative w-full aspect-video max-w-[340px] rounded-xl overflow-hidden shadow-md">
                                        <img :src="previewOrganicHero" class="w-full h-full object-cover">
                                        <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">New Preview</span>
                                    </div>
                                </template>
                                <template x-if="!previewOrganicHero">
                                    <div>
                                        @if(!empty($organicImg))
                                            <div class="relative w-full aspect-video max-w-[340px] rounded-xl overflow-hidden shadow-md">
                                                <img src="{{ \App\Models\Setting::imageUrl($organicImg) }}" class="w-full h-full object-cover">
                                                <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                            </div>
                                        @else
                                            <div class="w-full aspect-video w-64 bg-emerald-900 rounded-xl flex flex-col items-center justify-center p-6 text-center border border-emerald-500/30 text-white">
                                                <p class="text-xs font-bold text-emerald-300">Default Organic Gradient</p>
                                                <p class="text-[11px] text-white/60 mt-1">Upload a farm harvest image or photo</p>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                                @if(!empty($organicImg))
                                    <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                        <span class="text-[11px] text-emerald-600 font-semibold">Active</span>
                                        <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
                                            <input type="checkbox" name="remove_organic_hero_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                            <span>Remove Image</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="organic_hero_image" accept="image/*"
                                   @change="previewOrganicHero = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-700 file:text-white border border-slate-200 rounded-xl cursor-pointer bg-white">
                        </div>

                        <!-- Hero Text Inputs -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Hero Badge</label>
                                <input type="text" name="organic_hero_badge" 
                                       value="{{ $settings['organic_hero_badge'] ?? '🌱 100% Certified Farm-to-Table' }}" 
                                       class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Main Headline</label>
                                <textarea name="organic_hero_title" rows="2" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-emerald-600 outline-none">{{ $settings['organic_hero_title'] ?? "Organic Foods at your Doorsteps" }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Subtitle / Farm Promise</label>
                                <textarea name="organic_hero_subtitle" rows="2" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none">{{ $settings['organic_hero_subtitle'] ?? "Pure, wholesome produce cultivated by trusted Indian organic farmers. Zero harmful pesticides, zero synthetic chemicals—just real nutrition harvested daily." }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1 font-medium">Primary CTA Button</label>
                                    <input type="text" name="organic_hero_btn1" 
                                           value="{{ $settings['organic_hero_btn1'] ?? 'Start Shopping' }}" 
                                           class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1 font-medium">Secondary CTA Button</label>
                                    <input type="text" name="organic_hero_btn2" 
                                           value="{{ $settings['organic_hero_btn2'] ?? 'Explore Featured' }}" 
                                           class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Metric 1</label>
                                    <input type="text" name="organic_metric1_num" value="{{ $settings['organic_metric1_num'] ?? '1,200+' }}" class="w-full px-2 py-1 text-xs font-bold border border-slate-200 rounded-lg mb-1">
                                    <input type="text" name="organic_metric1_label" value="{{ $settings['organic_metric1_label'] ?? 'Organic Varieties' }}" class="w-full px-2 py-1 text-[11px] border border-slate-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Metric 2</label>
                                    <input type="text" name="organic_metric2_num" value="{{ $settings['organic_metric2_num'] ?? '50k+' }}" class="w-full px-2 py-1 text-xs font-bold border border-slate-200 rounded-lg mb-1">
                                    <input type="text" name="organic_metric2_label" value="{{ $settings['organic_metric2_label'] ?? 'Happy Patrons' }}" class="w-full px-2 py-1 text-[11px] border border-slate-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Metric 3</label>
                                    <input type="text" name="organic_metric3_num" value="{{ $settings['organic_metric3_num'] ?? '100%' }}" class="w-full px-2 py-1 text-xs font-bold border border-slate-200 rounded-lg mb-1">
                                    <input type="text" name="organic_metric3_label" value="{{ $settings['organic_metric3_label'] ?? 'Purity Assured' }}" class="w-full px-2 py-1 text-[11px] border border-slate-200 rounded-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Three Value Proposition Cards (Ref Image 1) -->
                <div class="space-y-6 p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <h3 class="text-sm font-bold text-slate-800">2. Three Highlight Value Proposition Cards (Directly Below Hero)</h3>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Ref: Image 1</span>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- Card 1: Fresh from Farm -->
                        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-bold text-emerald-900 uppercase">Card 1 (Green)</span>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Card Title</label>
                                <input type="text" name="organic_prop1_title" 
                                       value="{{ $settings['organic_prop1_title'] ?? 'Fresh from the Farm' }}" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold bg-white focus:border-emerald-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Card Description</label>
                                <textarea name="organic_prop1_desc" rows="3" 
                                          class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white focus:border-emerald-600 outline-none">{{ $settings['organic_prop1_desc'] ?? 'Harvested and dispatched within 24 hours for peak nutritional potency.' }}</textarea>
                            </div>
                        </div>

                        <!-- Card 2: 100% Certified Organic -->
                        <div class="p-4 rounded-xl border border-emerald-900/20 bg-emerald-900/5 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-900"></span>
                                <span class="text-xs font-bold text-emerald-950 uppercase">Card 2 (Dark Green)</span>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Card Title</label>
                                <input type="text" name="organic_prop2_title" 
                                       value="{{ $settings['organic_prop2_title'] ?? '100% Certified Organic' }}" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold bg-white focus:border-emerald-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Card Description</label>
                                <textarea name="organic_prop2_desc" rows="3" 
                                          class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white focus:border-emerald-600 outline-none">{{ $settings['organic_prop2_desc'] ?? 'Verified non-GMO, chemical-free crops certified under NPOP & FSSAI norms.' }}</textarea>
                            </div>
                        </div>

                        <!-- Card 3: Free Express Delivery -->
                        <div class="p-4 rounded-xl border border-orange-200 bg-orange-50/40 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                <span class="text-xs font-bold text-orange-900 uppercase">Card 3 (Orange)</span>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Card Title</label>
                                <input type="text" name="organic_prop3_title" 
                                       value="{{ $settings['organic_prop3_title'] ?? 'Free Express Delivery' }}" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold bg-white focus:border-emerald-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1 font-medium">Card Description</label>
                                <textarea name="organic_prop3_desc" rows="3" 
                                          class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white focus:border-emerald-600 outline-none">{{ $settings['organic_prop3_desc'] ?? 'Complimentary door delivery on orders over ₹1,499 with temperature control.' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Triple Promotional Ad Banners (Ref Image 2) -->
                <div class="space-y-6 p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <h3 class="text-sm font-bold text-slate-800">3. Triple Promotional Ad Banners (Category Special Highlights)</h3>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Ref: Image 2</span>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- Banner 1: Wild Honey -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                            <span class="text-xs font-bold text-slate-800 uppercase block">Banner 1 (Wild Honey)</span>
                            
                            {{-- Background Image Upload --}}
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Background Image (Optional)</label>
                                @php
                                    $ad1Raw = $settings['organic_ad1_image'] ?? '';
                                    $ad1Src = \App\Models\Setting::imageUrl($ad1Raw);
                                @endphp
                                <div class="p-2.5 rounded-lg border border-slate-200 bg-white space-y-2">
                                    <template x-if="previewOrganicAd1">
                                        <div class="relative w-full aspect-[2/1] rounded-lg overflow-hidden shadow-xs border border-slate-200">
                                            <img :src="previewOrganicAd1" class="w-full h-full object-cover">
                                            <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">New Upload Preview</span>
                                        </div>
                                    </template>
                                    <template x-if="!previewOrganicAd1">
                                        <div>
                                            @if($ad1Src)
                                                <div class="relative w-full aspect-[2/1] rounded-lg overflow-hidden shadow-xs border border-slate-200 bg-slate-100">
                                                    <img src="{{ $ad1Src }}" class="w-full h-full object-cover" alt="Banner 1 Background">
                                                    <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">
                                                        Active Image
                                                    </span>
                                                </div>
                                            @else
                                                <div class="w-full aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-100 flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                                    <svg class="w-6 h-6 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[10px] font-medium text-slate-500">No Image Set</span>
                                                    <span class="text-[9px] text-slate-400">Uses solid gradient background</span>
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                    @if(!empty($ad1Src))
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-[10px] text-emerald-600 font-semibold">Active</span>
                                            <label class="inline-flex items-center gap-1 text-[11px] text-rose-600 cursor-pointer hover:text-rose-700">
                                                <input type="checkbox" name="remove_organic_ad1_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                                <span>Remove Image</span>
                                            </label>
                                        </div>
                                    @endif
                                    <input type="file" name="organic_ad1_image" accept="image/*"
                                           @change="previewOrganicAd1 = URL.createObjectURL($event.target.files[0])"
                                           class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-700 file:text-white border border-slate-200 rounded cursor-pointer bg-slate-50">
                                    <span class="text-[10px] text-slate-400 block">Recommended: 600×360px JPG, PNG, WEBP</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Tag / Kicker</label>
                                <input type="text" name="organic_ad1_kicker" value="{{ $settings['organic_ad1_kicker'] ?? 'Limited Batch' }}" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Banner Title</label>
                                <input type="text" name="organic_ad1_title" value="{{ $settings['organic_ad1_title'] ?? 'Raw Wild Forest Honey' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Subtitle / Details</label>
                                <textarea name="organic_ad1_desc" rows="2" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">{{ $settings['organic_ad1_desc'] ?? 'Unprocessed, pure nectar directly from Nilgiri biosphere reserves.' }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Button Text</label>
                                    <input type="text" name="organic_ad1_btn" value="{{ $settings['organic_ad1_btn'] ?? 'Shop Wild Honey' }}" class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Link URL (optional)</label>
                                    <input type="text" name="organic_ad1_url" value="{{ $settings['organic_ad1_url'] ?? '' }}" placeholder="/search?q=honey" class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- Banner 2: Wood Oils -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                            <span class="text-xs font-bold text-slate-800 uppercase block">Banner 2 (Cold-Pressed Oils)</span>
                            
                            {{-- Background Image Upload --}}
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Background Image</label>
                                @php
                                    $ad2Raw = $settings['organic_ad2_image'] ?? '';
                                    $ad2Src = \App\Models\Setting::imageUrl($ad2Raw);
                                @endphp
                                <div class="p-2.5 rounded-lg border border-slate-200 bg-white space-y-2">
                                    <template x-if="previewOrganicAd2">
                                        <div class="relative w-full aspect-[2/1] rounded-lg overflow-hidden shadow-xs border border-slate-200">
                                            <img :src="previewOrganicAd2" class="w-full h-full object-cover">
                                            <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">New Upload Preview</span>
                                        </div>
                                    </template>
                                    <template x-if="!previewOrganicAd2">
                                        <div>
                                            @if($ad2Src)
                                                <div class="relative w-full aspect-[2/1] rounded-lg overflow-hidden shadow-xs border border-slate-200 bg-slate-100">
                                                    <img src="{{ $ad2Src }}" class="w-full h-full object-cover" alt="Banner 2 Background">
                                                    <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">
                                                        Active Image
                                                    </span>
                                                </div>
                                            @else
                                                <div class="w-full aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-100 flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                                    <svg class="w-6 h-6 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[10px] font-medium text-slate-500">No Image Set</span>
                                                    <span class="text-[9px] text-slate-400">Uses solid gradient background</span>
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                    @if(!empty($ad2Src))
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-[10px] text-emerald-600 font-semibold">Active</span>
                                            <label class="inline-flex items-center gap-1 text-[11px] text-rose-600 cursor-pointer hover:text-rose-700">
                                                <input type="checkbox" name="remove_organic_ad2_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                                <span>Remove Image</span>
                                            </label>
                                        </div>
                                    @endif
                                    <input type="file" name="organic_ad2_image" accept="image/*"
                                           @change="previewOrganicAd2 = URL.createObjectURL($event.target.files[0])"
                                           class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-700 file:text-white border border-slate-200 rounded cursor-pointer bg-slate-50">
                                    <span class="text-[10px] text-slate-400 block">Recommended: 600×360px JPG, PNG, WEBP</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Tag / Kicker</label>
                                <input type="text" name="organic_ad2_kicker" value="{{ $settings['organic_ad2_kicker'] ?? 'Seasonal Deal' }}" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Banner Title</label>
                                <input type="text" name="organic_ad2_title" value="{{ $settings['organic_ad2_title'] ?? 'Cold-Pressed Wood Oils' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Subtitle / Details</label>
                                <textarea name="organic_ad2_desc" rows="2" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">{{ $settings['organic_ad2_desc'] ?? 'Traditional Kolhu pressed mustard, sesame, and groundnut oils.' }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Button Text</label>
                                    <input type="text" name="organic_ad2_btn" value="{{ $settings['organic_ad2_btn'] ?? 'Explore Cold-Pressed' }}" class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Link URL (optional)</label>
                                    <input type="text" name="organic_ad2_url" value="{{ $settings['organic_ad2_url'] ?? '' }}" placeholder="/search?q=oil" class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- Banner 3: Gir Cow Ghee -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                            <span class="text-xs font-bold text-slate-800 uppercase block">Banner 3 (Gir Cow Ghee)</span>
                            
                            {{-- Background Image Upload --}}
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Background Image</label>
                                @php
                                    $ad3Raw = $settings['organic_ad3_image'] ?? '';
                                    $ad3Src = \App\Models\Setting::imageUrl($ad3Raw);
                                @endphp
                                <div class="p-2.5 rounded-lg border border-slate-200 bg-white space-y-2">
                                    <template x-if="previewOrganicAd3">
                                        <div class="relative w-full aspect-[2/1] rounded-lg overflow-hidden shadow-xs border border-slate-200">
                                            <img :src="previewOrganicAd3" class="w-full h-full object-cover">
                                            <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">New Upload Preview</span>
                                        </div>
                                    </template>
                                    <template x-if="!previewOrganicAd3">
                                        <div>
                                            @if($ad3Src)
                                                <div class="relative w-full aspect-[2/1] rounded-lg overflow-hidden shadow-xs border border-slate-200 bg-slate-100">
                                                    <img src="{{ $ad3Src }}" class="w-full h-full object-cover" alt="Banner 3 Background">
                                                    <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">
                                                        Active Image
                                                    </span>
                                                </div>
                                            @else
                                                <div class="w-full aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-100 flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                                    <svg class="w-6 h-6 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[10px] font-medium text-slate-500">No Image Set</span>
                                                    <span class="text-[9px] text-slate-400">Uses solid gradient background</span>
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                    @if(!empty($ad3Src))
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-[10px] text-emerald-600 font-semibold">Active</span>
                                            <label class="inline-flex items-center gap-1 text-[11px] text-rose-600 cursor-pointer hover:text-rose-700">
                                                <input type="checkbox" name="remove_organic_ad3_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                                <span>Remove Image</span>
                                            </label>
                                        </div>
                                    @endif
                                    <input type="file" name="organic_ad3_image" accept="image/*"
                                           @change="previewOrganicAd3 = URL.createObjectURL($event.target.files[0])"
                                           class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-700 file:text-white border border-slate-200 rounded cursor-pointer bg-slate-50">
                                    <span class="text-[10px] text-slate-400 block">Recommended: 600×360px JPG, PNG, WEBP</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Tag / Kicker</label>
                                <input type="text" name="organic_ad3_kicker" value="{{ $settings['organic_ad3_kicker'] ?? 'Daily Essentials' }}" class="w-full px-3 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Banner Title</label>
                                <input type="text" name="organic_ad3_title" value="{{ $settings['organic_ad3_title'] ?? 'A2 Desi Gir Cow Ghee' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Subtitle / Details</label>
                                <textarea name="organic_ad3_desc" rows="2" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">{{ $settings['organic_ad3_desc'] ?? 'Bilona churned cultured butter from free-grazing indigenous cows.' }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Button Text</label>
                                    <input type="text" name="organic_ad3_btn" value="{{ $settings['organic_ad3_btn'] ?? 'Discover A2 Ghee' }}" class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Link URL (optional)</label>
                                    <input type="text" name="organic_ad3_url" value="{{ $settings['organic_ad3_url'] ?? '' }}" placeholder="/search?q=ghee" class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Sustainable Farming Story / About Us Page Link -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="p-4 bg-emerald-950 text-white rounded-2xl border border-emerald-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-800 text-emerald-400 flex items-center justify-center shrink-0 font-bold">🌱</div>
                            <div>
                                <p class="text-xs font-bold text-white">Organic Farming Manifesto &amp; About Us Page Content</p>
                                <p class="text-[11px] text-emerald-200/70">Customize farm story visual, manifesto narrative, 2 soil metrics, and 4 core pillars in the dedicated About Us editor.</p>
                            </div>
                        </div>
                        <a href="#about-settings" onclick="window.dispatchEvent(new CustomEvent('switch-about-tab', { detail: 'organic' }))" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-bold rounded-xl text-xs shrink-0 transition shadow-xs">
                            <span>Manage in About Us Settings</span>
                            <span>↓</span>
                        </a>
                    </div>
                </div>

                <!-- Section 5: Newsletter Harvest Club Banner (Ref Image 4) -->
                <div class="space-y-6 p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-800"></span>
                            <h3 class="text-sm font-bold text-slate-800">5. Newsletter Harvest Club Card (Footer Banner)</h3>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Ref: Image 4</span>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Newsletter Badge</label>
                            <input type="text" name="organic_news_badge" 
                                   value="{{ $settings['organic_news_badge'] ?? '🌱 Join the Organic Family' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Promo Headline</label>
                            <input type="text" name="organic_news_title" 
                                   value="{{ $settings['organic_news_title'] ?? 'Get ₹200 Off Your First Fresh Harvest' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-emerald-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Subtitle / Perks Note</label>
                            <textarea name="organic_news_subtitle" rows="2" 
                                      class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none">{{ $settings['organic_news_subtitle'] ?? 'Subscribe to our weekly seasonal harvest alerts, farm updates, and exclusive patron discounts. No spam, just pure organic goodness.' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Header Top Announcement Bar & Customer Care Helpline -->
                <div class="space-y-6 p-6 rounded-2xl border border-slate-200 bg-white shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <h3 class="text-sm font-bold text-slate-800">6. Header Top Bar Writeups &amp; Customer Care Helpline</h3>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Header Top Bar &amp; Storefront Contact</span>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Top Bar Announcement Writeup (with ✓ badge)</label>
                            <input type="text" name="organic_top_notice" 
                                   value="{{ $settings['organic_top_notice'] ?? '100% Certified Organic • Direct from Local Farmers & Sustainable Harvests' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-emerald-600 outline-none text-slate-800"
                                   placeholder="100% Certified Organic • Direct from Local Farmers &amp; Sustainable Harvests">
                            <p class="text-[11px] text-slate-500 mt-1.5">Displayed at the top left of the dark green announcement bar on the Organic storefront.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Top Bar Delivery / Shipping Note</label>
                            <input type="text" name="organic_top_shipping_notice" 
                                   value="{{ $settings['organic_top_shipping_notice'] ?? 'Delivery to All Major Indian Pincodes' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none text-slate-800"
                                   placeholder="Delivery to All Major Indian Pincodes">
                            <p class="text-[11px] text-slate-500 mt-1.5">Displayed next to the phone number on the announcement bar.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Customer Care No (Helpline Phone)</label>
                            <input type="text" name="organic_customer_care" 
                                   value="{{ $settings['organic_customer_care'] ?? $settings['support_phone'] ?? '+91 98765 43210' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-emerald-600 outline-none text-slate-800"
                                   placeholder="e.g. +91 98765 43210 or 1800-ORGANIC">
                            <p class="text-[11px] text-slate-500 mt-1.5">Displayed with phone icon in the top header announcement bar, mobile drawer, contact page, and footer helpline in Organic theme.</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Customer Care Email</label>
                            <input type="email" name="organic_support_email" 
                                   value="{{ $settings['organic_support_email'] ?? $settings['support_email'] ?? 'support@nexonorganic.in' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none text-slate-800"
                                   placeholder="support@nexonorganic.in">
                            <p class="text-[11px] text-slate-500 mt-1.5">Displayed on the contact page and footer farm dispatch hub in Organic theme.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- DYNAMIC CARDS FOR ANY FUTURE ADDED THEMES -->
            @foreach($templates ?? [] as $futureTmpl)
                @if(!in_array($futureTmpl->key, ['vyra', 'buttercup', 'lumiere', 'saaya', 'organic']))
                    <div x-show="activeTab === '{{ $futureTmpl->key }}'" class="space-y-6" x-cloak>
                        <div class="p-3 bg-slate-900 text-white rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                <span class="font-bold">{{ $futureTmpl->name }} Theme Hero &amp; Media</span>
                            </div>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-8 items-start">
                            <div class="space-y-4">
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    {{ $futureTmpl->name }} Hero Banner Graphic
                                </label>
                                @php $genericImg = $settings["theme_{$futureTmpl->key}_hero_image"] ?? $settings["{$futureTmpl->key}_hero_image"] ?? ''; @endphp
                                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                                    @if(!empty($genericImg))
                                        <div class="relative w-full aspect-[4/5] max-w-[260px] rounded-xl overflow-hidden shadow-md">
                                            <img src="{{ \App\Models\Setting::imageUrl($genericImg) }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                            <span class="text-[11px] text-emerald-600 font-semibold">Active</span>
                                            <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
                                                <input type="checkbox" name="remove_{{ $futureTmpl->key }}_hero_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                                <span>Remove Image</span>
                                            </label>
                                        </div>
                                    @else
                                        <div class="w-full aspect-[4/5] w-56 bg-white rounded-xl flex flex-col items-center justify-center p-6 text-center border border-slate-200">
                                            <p class="text-xs text-slate-500 font-bold">No Custom Image Uploaded</p>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="{{ $futureTmpl->key }}_hero_image" accept="image/*"
                                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 border border-slate-200 rounded-xl cursor-pointer bg-white">
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Main Slider Bottom Scrolling Announcement Ticker -->
        <div id="ticker-settings" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-600 text-white flex items-center justify-center font-bold shadow-sm shadow-amber-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">Main Slider Bottom Scrolling Ticker</h2>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Global Marquee Band
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Customize the 5 animated scrolling highlights displayed directly beneath the main hero slider on all themes</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ticker_enabled" value="1"
                               {{ ($settings['ticker_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                               class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                        <span class="text-xs font-semibold text-slate-700">Enable Scrolling Band</span>
                    </label>
                </div>
            </div>

            <!-- Preview Notice -->
            <div class="p-3.5 bg-amber-50/70 border border-amber-200/60 rounded-xl flex items-center justify-between text-xs text-amber-900">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>This horizontal marquee infinitely scrolls below the hero slider across Vyra, Buttercup, Saaya, Lumière, and Organic. You may include bold text using <code>&lt;strong&gt;text&lt;/strong&gt;</code>.</span>
                </div>
            </div>

            <!-- Grid of 5 Ticker Items -->
            <div class="space-y-4">
                <!-- Item 1: Free Delivery -->
                <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/40 hover:bg-slate-50/80 transition space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">1</span>
                            <span class="text-xs font-bold text-slate-800">Free Delivery Announcement</span>
                            <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1"><i class="fa-solid fa-truck text-amber-500"></i> Auto truck icon</span>
                        </div>
                    </div>
                    <input type="text" name="ticker_item_1"
                           value="{{ $settings['ticker_item_1'] ?? 'Free Delivery on orders over <strong>₹' . ($settings['free_shipping_threshold'] ?? '1499') . '</strong>' }}"
                           placeholder="Free Delivery on orders over <strong>₹1499</strong>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none bg-white">
                    <p class="text-[11px] text-slate-400">Default: <code>Free Delivery on orders over &lt;strong&gt;₹1499&lt;/strong&gt;</code></p>
                </div>

                <!-- Item 2: Selected Styles Promo -->
                <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/40 hover:bg-slate-50/80 transition space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">2</span>
                            <span class="text-xs font-bold text-slate-800">Special Discount Offer</span>
                        </div>
                    </div>
                    <input type="text" name="ticker_item_2"
                           value="{{ $settings['ticker_item_2'] ?? 'Upto 500/- off for selected styles.' }}"
                           placeholder="Upto 500/- off for selected styles."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none bg-white">
                    <p class="text-[11px] text-slate-400">Default: <code>Upto 500/- off for selected styles.</code></p>
                </div>

                <!-- Item 3: Coupon Code Offer -->
                <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/40 hover:bg-slate-50/80 transition space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">3</span>
                            <span class="text-xs font-bold text-slate-800">Coupon Code / Promo Voucher</span>
                            <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1"><i class="fa-solid fa-tags text-amber-500"></i> Auto coupon tag icon</span>
                        </div>
                    </div>
                    <input type="text" name="ticker_item_3"
                           value="{{ $settings['ticker_item_3'] ?? 'Use code <strong>WELCOME5</strong> for 5% OFF' }}"
                           placeholder="Use code <strong>WELCOME5</strong> for 5% OFF"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none bg-white">
                    <p class="text-[11px] text-slate-400">Default: <code>Use code &lt;strong&gt;WELCOME5&lt;/strong&gt; for 5% OFF</code></p>
                </div>

                <!-- Item 4: Payment Acceptance -->
                <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/40 hover:bg-slate-50/80 transition space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">4</span>
                            <span class="text-xs font-bold text-slate-800">Payment Modes / UPI Highlights</span>
                        </div>
                    </div>
                    <input type="text" name="ticker_item_4"
                           value="{{ $settings['ticker_item_4'] ?? 'Accepting UPI payments' }}"
                           placeholder="Accepting UPI payments"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none bg-white">
                    <p class="text-[11px] text-slate-400">Default: <code>Accepting UPI payments</code></p>
                </div>

                <!-- Item 5: Support Phone Helpline -->
                <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/40 hover:bg-slate-50/80 transition space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">5</span>
                            <span class="text-xs font-bold text-slate-800">Call Us / Concierge Helpline</span>
                            <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1"><i class="fa-solid fa-phone text-pink-500"></i> Auto phone icon</span>
                        </div>
                    </div>
                    <input type="text" name="ticker_item_5"
                           value="{{ $settings['ticker_item_5'] ?? 'Call us: <strong>' . ($settings['support_phone'] ?? '+91 98765 43210') . '</strong>' }}"
                           placeholder="Call us: <strong>+91 98765 43210</strong>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none bg-white">
                    <p class="text-[11px] text-slate-400">Default: <code>Call us: &lt;strong&gt;+91 98765 43210&lt;/strong&gt;</code></p>
                </div>
            </div>
        </div>

        <!-- About Us Page & Brand Philosophy Settings -->
        <div id="about-settings" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6"
             x-data="{
                 activeAboutTab: '{{ $activeTheme->key ?? 'vyra' }}',
                 previewVyraStory: null,
                 previewButtercupStory: null,
                 previewSaayaStory: null,
                 previewLumiereStory: null,
                 previewOrganicStory: null
             }"
             x-on:switch-about-tab.window="activeAboutTab = $event.detail">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow-sm shadow-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">About Us Page &amp; Brand Philosophy</h2>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Live Theme: {{ ucfirst($activeTheme->key ?? 'vyra') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Customize the hero narrative, brand philosophy, story photo, live metrics, and core brand pillars for each theme's About Us page (/about)</p>
                    </div>
                </div>

                <!-- Theme Tabs -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold flex-wrap gap-1">
                    @foreach($templates ?? [] as $tmpl)
                        <button type="button" @click="activeAboutTab = '{{ $tmpl->key }}'" 
                                :class="activeAboutTab === '{{ $tmpl->key }}' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                            <span>{{ $tmpl->name }}</span>
                            @if($tmpl->is_active)
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Active Theme"></span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 1. VYRA ABOUT US TAB                      -->
            <!-- ========================================== -->
            <div x-show="activeAboutTab === 'vyra'" class="space-y-8">
                <div class="p-3 bg-[#0E0E10] text-[#F2F0EC] rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs border border-[#FF3D5A]/40">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#FF3D5A]"></span>
                        <span class="font-bold">Vyra Theme — About Us Page &amp; Atelier Philosophy</span>
                        <span class="font-mono text-[10px] text-[#D4FF3D]">Cyberpunk Noir</span>
                    </div>
                    <a href="{{ route('about') }}?theme=vyra" target="_blank" class="inline-flex items-center gap-1 text-xs text-[#D4FF3D] hover:underline">
                        <span>Preview Live About Page</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                {{-- Sub-section 1: Hero Header --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">1. Hero Header &amp; Main Headline</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Badge / Tag</label>
                            <input type="text" name="vyra_story_badge" value="{{ $settings['vyra_story_badge'] ?? 'The Brand' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Philosophy Lead Badge</label>
                            <input type="text" name="vyra_about_philosophy_badge" value="{{ $settings['vyra_about_philosophy_badge'] ?? 'Philosophy 01' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Main Title (Supports line breaks)</label>
                        <textarea name="vyra_story_title" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold bg-white focus:border-indigo-500 outline-none">{{ $settings['vyra_story_title'] ?? "Not Made to Blend In.\nMade to Rule." }}</textarea>
                    </div>
                </div>

                {{-- Sub-section 2: Story Image Frame & Narrative --}}
                <div class="grid lg:grid-cols-2 gap-8 items-start">
                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Vyra Atelier Photo (Neon Border Frame)
                            </label>
                            <span class="text-[11px] text-slate-500 font-medium">Replaces cyber silhouette</span>
                        </div>
                        @php $vyraStoryImg = $settings['vyra_story_image'] ?? ''; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                            <template x-if="previewVyraStory">
                                <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-xl overflow-hidden shadow-md border-2 border-[#FF3D5A]">
                                    <img :src="previewVyraStory" class="w-full h-full object-cover" alt="New Vyra Story Preview">
                                    <span class="absolute bottom-2 left-2 bg-[#FF3D5A] text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">New Preview</span>
                                </div>
                            </template>
                            <template x-if="!previewVyraStory">
                                <div>
                                    @if(!empty($vyraStoryImg))
                                        <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-xl overflow-hidden shadow-md border-2 border-[#FF3D5A]">
                                            <img src="{{ \App\Models\Setting::imageUrl($vyraStoryImg) }}" class="w-full h-full object-cover" alt="Current Vyra Story">
                                            <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                        </div>
                                    @else
                                        <div class="w-full aspect-[3/4] w-52 bg-[#0E0E10] rounded-xl flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-[#FF3D5A]/40 shadow-inner">
                                            <svg viewBox="0 0 100 120" class="w-20 h-24 text-[#FF3D5A]/40 mb-2" fill="currentColor">
                                                <ellipse cx="50" cy="25" rx="16" ry="20"/>
                                                <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
                                            </svg>
                                            <p class="text-xs text-[#FF3D5A] font-bold">Default Cyber Silhouette</p>
                                            <p class="text-[11px] text-[#F2F0EC]/50 mt-1">Upload a photo to appear in the neon border frame</p>
                                        </div>
                                    @endif
                                </div>
                            </template>
                            @if(!empty($vyraStoryImg))
                                <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                    <span class="text-[11px] text-emerald-600 font-semibold">Custom Image Active</span>
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-700 font-medium cursor-pointer">
                                        <input type="checkbox" name="remove_vyra_story_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                        <span>Remove &amp; Restore Silhouette</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="vyra_story_image" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   @change="previewVyraStory = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-[#FF3D5A] border border-slate-200 rounded-xl cursor-pointer bg-white transition">
                            <p class="text-[11px] text-slate-400 mt-1.5">Recommended ratio: 3:4 (e.g. 768×1024px). JPG, PNG, or WEBP.</p>
                        </div>
                    </div>

                    <!-- Story Copy -->
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Philosophy &amp; Narrative Paragraphs
                        </label>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Philosophy Sub-Heading</label>
                            <input type="text" name="vyra_about_philosophy_heading" 
                                   value="{{ $settings['vyra_about_philosophy_heading'] ?? 'Raw Edge. Zero Compromise.' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 1</label>
                            <textarea name="vyra_story_p1" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['vyra_story_p1'] ?? "Vyra was created for those who treat style as armour. Born in the intersection of high fashion and street energy, every stitch is calibrated for raw confidence." }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 2</label>
                            <textarea name="vyra_story_p2" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['vyra_story_p2'] ?? "Architectural seams, breathable sheer mesh, and unyielding contours. Designed without apologies, crafted for the fearless." }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sub-section 3: 3 Stats Counters --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">3. Live Achievement Counters</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 1</label>
                            <input type="text" name="vyra_stat1_num" value="{{ $settings['vyra_stat1_num'] ?? '0%' }}" placeholder="0%" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg">
                            <input type="text" name="vyra_stat1_label" value="{{ $settings['vyra_stat1_label'] ?? 'Compromise' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 2</label>
                            <input type="text" name="vyra_stat2_num" value="{{ $settings['vyra_stat2_num'] ?? '50K+' }}" placeholder="50K+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg">
                            <input type="text" name="vyra_stat2_label" value="{{ $settings['vyra_stat2_label'] ?? 'Rebels Styled' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 3</label>
                            <input type="text" name="vyra_stat3_num" value="{{ $settings['vyra_stat3_num'] ?? '100%' }}" placeholder="100%" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg">
                            <input type="text" name="vyra_stat3_label" value="{{ $settings['vyra_stat3_label'] ?? 'Raw Edge' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                    </div>
                </div>

                {{-- Sub-section 4: 3 Brand Pillars --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">4. Three Brand Pillars (Value Proposition Cards)</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#FF3D5A]/10 text-[#FF3D5A]">Pillar 01</span>
                            <input type="text" name="vyra_pillar1_title" value="{{ $settings['vyra_pillar1_title'] ?? 'Architectural Seams' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 1 Title">
                            <textarea name="vyra_pillar1_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['vyra_pillar1_desc'] ?? 'Precision-engineered sheer mesh cut lines that sculpt your silhouette with zero bulk and maximum comfort.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#FF3D5A]/10 text-[#FF3D5A]">Pillar 02</span>
                            <input type="text" name="vyra_pillar2_title" value="{{ $settings['vyra_pillar2_title'] ?? 'Midnight Noir Palette' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 2 Title">
                            <textarea name="vyra_pillar2_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['vyra_pillar2_desc'] ?? 'Deep carbon blacks, gunmetal clasps, and electric accents built for nocturnal presence and unfiltered attitude.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#D4FF3D]/20 text-[#6B8500]">Pillar 03</span>
                            <input type="text" name="vyra_pillar3_title" value="{{ $settings['vyra_pillar3_title'] ?? 'Stealth Packaging' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 3 Title">
                            <textarea name="vyra_pillar3_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['vyra_pillar3_desc'] ?? 'All deliveries arrive in sealed, blacked-out matte boxes with zero exterior brand graphics. Your privacy is non-negotiable.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. BUTTERCUP ABOUT US TAB                 -->
            <!-- ========================================== -->
            <div x-show="activeAboutTab === 'buttercup'" class="space-y-8" x-cloak>
                <div class="p-3 bg-[#FBE7E4] text-[#4A3B3A] rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs border border-[#D89AA0]/40">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D89AA0]"></span>
                        <span class="font-bold">Buttercup Theme — About Us Page &amp; Comfort Philosophy</span>
                        <span class="font-body text-[10px] text-[#B97680] font-semibold">Everyday Soft Lingerie</span>
                    </div>
                    <a href="{{ route('about') }}?theme=buttercup" target="_blank" class="inline-flex items-center gap-1 text-xs text-[#B97680] font-semibold hover:underline">
                        <span>Preview Live About Page</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                {{-- Sub-section 1: Hero Header --}}
                <div class="p-5 rounded-2xl bg-[#FFF9F9] border border-[#D89AA0]/30 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-[#D89AA0]/20">
                        <span class="text-xs font-bold text-[#4A3B3A] uppercase tracking-wider">1. Hero Header &amp; Main Headline</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Badge / Tag</label>
                        <input type="text" name="buttercup_story_badge" value="{{ $settings['buttercup_story_badge'] ?? 'Our Story' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-rose-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Main Title (Supports line breaks)</label>
                        <textarea name="buttercup_story_title" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold bg-white focus:border-rose-400 outline-none">{{ $settings['buttercup_story_title'] ?? "Designed for Real Bodies,\nReal Days" }}</textarea>
                    </div>
                </div>

                {{-- Sub-section 2: Story Image Frame & Narrative --}}
                <div class="grid lg:grid-cols-2 gap-8 items-start">
                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Buttercup Studio Photo (Pink Rounded Frame)
                            </label>
                            <span class="text-[11px] text-rose-700 font-medium">Replaces studio graphic</span>
                        </div>
                        @php $bcStoryImg = $settings['buttercup_story_image'] ?? ''; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                            <template x-if="previewButtercupStory">
                                <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-[24px] overflow-hidden shadow-md border-2 border-[#D89AA0]">
                                    <img :src="previewButtercupStory" class="w-full h-full object-cover" alt="New Buttercup Story Preview">
                                    <span class="absolute bottom-2 left-2 bg-[#D89AA0] text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">New Preview</span>
                                </div>
                            </template>
                            <template x-if="!previewButtercupStory">
                                <div>
                                    @if(!empty($bcStoryImg))
                                        <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-[24px] overflow-hidden shadow-md border-2 border-[#D89AA0]">
                                            <img src="{{ \App\Models\Setting::imageUrl($bcStoryImg) }}" class="w-full h-full object-cover" alt="Current Buttercup Story">
                                            <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                        </div>
                                    @else
                                        <div class="w-full aspect-[3/4] w-52 bg-[#FBE7E4] rounded-[24px] flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-[#D89AA0]/50 shadow-inner">
                                            <svg viewBox="0 0 100 120" class="w-20 h-24 text-[#D89AA0]/40 mb-2" fill="currentColor">
                                                <ellipse cx="50" cy="25" rx="16" ry="20"/>
                                                <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
                                            </svg>
                                            <p class="text-xs text-[#B97680] font-bold">Default Studio Graphic</p>
                                            <p class="text-[11px] text-[#4A3B3A]/60 mt-1">Upload a photo to appear in the soft pink frame</p>
                                        </div>
                                    @endif
                                </div>
                            </template>
                            @if(!empty($bcStoryImg))
                                <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                    <span class="text-[11px] text-emerald-600 font-semibold">Custom Story Image Active</span>
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-700 font-medium cursor-pointer">
                                        <input type="checkbox" name="remove_buttercup_story_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                        <span>Remove &amp; Restore Graphic</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="buttercup_story_image" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   @change="previewButtercupStory = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-[#B97680] border border-slate-200 rounded-xl cursor-pointer bg-white transition">
                            <p class="text-[11px] text-slate-400 mt-1.5">Recommended ratio: 3:4. JPG, PNG, or WEBP.</p>
                        </div>
                    </div>

                    <!-- Story Copy -->
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Promise &amp; Narrative Paragraphs
                        </label>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Philosophy Sub-Heading</label>
                            <input type="text" name="buttercup_about_philosophy_heading" 
                                   value="{{ $settings['buttercup_about_philosophy_heading'] ?? 'Why Should Comfort and Style Ever Be a Trade-off?' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-rose-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 1</label>
                            <textarea name="buttercup_story_p1" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">{{ $settings['buttercup_story_p1'] ?? "Buttercup started with one simple question: why should comfort and style be a trade-off? We design every piece with breathable, skin-friendly fabric — soft enough for everyday, pretty enough to feel special." }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 2</label>
                            <textarea name="buttercup_story_p2" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-rose-400 outline-none">{{ $settings['buttercup_story_p2'] ?? "Zero painful underwires, zero pinch. Just butter-soft modal, organic cotton, and gentle stretch that moves with you from dawn to dusk." }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sub-section 3: 3 Stats Counters --}}
                <div class="p-5 rounded-2xl bg-[#FFF9F9] border border-[#D89AA0]/30 space-y-3">
                    <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider">3. Live Comfort Metrics</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-white border border-[#D89AA0]/30 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 1</label>
                            <input type="text" name="buttercup_stat1_num" value="{{ $settings['buttercup_stat1_num'] ?? '5+' }}" placeholder="5+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-[#B97680]">
                            <input type="text" name="buttercup_stat1_label" value="{{ $settings['buttercup_stat1_label'] ?? 'Years of Comfort' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-[#D89AA0]/30 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 2</label>
                            <input type="text" name="buttercup_stat2_num" value="{{ $settings['buttercup_stat2_num'] ?? '2L+' }}" placeholder="2L+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-[#B97680]">
                            <input type="text" name="buttercup_stat2_label" value="{{ $settings['buttercup_stat2_label'] ?? 'Happy Customers' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-[#D89AA0]/30 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 3</label>
                            <input type="text" name="buttercup_stat3_num" value="{{ $settings['buttercup_stat3_num'] ?? '100%' }}" placeholder="100%" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-[#B97680]">
                            <input type="text" name="buttercup_stat3_label" value="{{ $settings['buttercup_stat3_label'] ?? 'Skin-Friendly' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                    </div>
                </div>

                {{-- Sub-section 4: 3 Brand Pillars --}}
                <div class="p-5 rounded-2xl bg-[#FFF9F9] border border-[#D89AA0]/30 space-y-4">
                    <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider">4. Three Brand Pillars (Comfort Innovations)</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white border border-[#D89AA0]/30 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FBE7E4] text-[#B97680]">Pillar 01</span>
                            <input type="text" name="buttercup_pillar1_title" value="{{ $settings['buttercup_pillar1_title'] ?? 'Cloud-Soft Modal' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 1 Title">
                            <textarea name="buttercup_pillar1_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['buttercup_pillar1_desc'] ?? 'Micro-modal knit woven from sustainably harvested beechwood, providing a feather-soft second skin feel.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-[#D89AA0]/30 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FBE7E4] text-[#B97680]">Pillar 02</span>
                            <input type="text" name="buttercup_pillar2_title" value="{{ $settings['buttercup_pillar2_title'] ?? 'Wire-Free Ergonomics' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 2 Title">
                            <textarea name="buttercup_pillar2_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['buttercup_pillar2_desc'] ?? 'Sculpted support panels that gently lift without digging metal bones into your ribs. Wearable 24 hours a day.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-[#D89AA0]/30 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FBE7E4] text-[#B97680]">Pillar 03</span>
                            <input type="text" name="buttercup_pillar3_title" value="{{ $settings['buttercup_pillar3_title'] ?? 'Real-Body Inclusive Sizing' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 3 Title">
                            <textarea name="buttercup_pillar3_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['buttercup_pillar3_desc'] ?? 'Engineered across all bust shapes and cup sizes so you never feel squeezed, pinched, or restricted.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 3. SAAYA ABOUT US TAB                     -->
            <!-- ========================================== -->
            <div x-show="activeAboutTab === 'saaya'" class="space-y-8" x-cloak>
                <div class="p-3 bg-[#3D0C11] text-[#F9F7F2] rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs border border-[#C9A24B]/40">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#C9A24B]"></span>
                        <span class="font-bold text-[#C9A24B]">Saaya Theme — About Us Page &amp; Karigar Heritage</span>
                        <span class="font-serif italic text-[10px] text-ivory/70">Pure Mulberry Silk &amp; Heirloom Zari</span>
                    </div>
                    <a href="{{ route('about') }}?theme=saaya" target="_blank" class="inline-flex items-center gap-1 text-xs text-[#C9A24B] hover:underline">
                        <span>Preview Live About Page</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                {{-- Sub-section 1: Hero Header --}}
                <div class="p-5 rounded-2xl bg-amber-50/40 border border-[#C9A24B]/30 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-[#C9A24B]/20">
                        <span class="text-xs font-bold text-[#3D0C11] uppercase tracking-wider">1. Hero Header &amp; Main Headline</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Badge / Sub-heading</label>
                            <input type="text" name="saaya_story_badge" value="{{ $settings['saaya_story_badge'] ?? 'Our Story' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-amber-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Philosophy Lead Badge</label>
                            <input type="text" name="saaya_about_philosophy_badge" value="{{ $settings['saaya_about_philosophy_badge'] ?? 'Heritage & Mastery' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-amber-600 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Main Title (Supports line breaks)</label>
                        <textarea name="saaya_story_title" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-serif font-bold bg-white focus:border-amber-600 outline-none">{{ $settings['saaya_story_title'] ?? "A Love Letter\nto Indian Femininity" }}</textarea>
                    </div>
                </div>

                {{-- Sub-section 2: Story Image Frame & Narrative --}}
                <div class="grid lg:grid-cols-2 gap-8 items-start">
                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Saaya Heirloom Photo (Gold Foil Frame)
                            </label>
                            <span class="text-[11px] text-amber-700 font-medium">Replaces portrait silhouette</span>
                        </div>
                        @php $saayaStoryImg = $settings['saaya_story_image'] ?? ''; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                            <template x-if="previewSaayaStory">
                                <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-xl overflow-hidden shadow-md border-2 border-[#C9A24B]">
                                    <img :src="previewSaayaStory" class="w-full h-full object-cover" alt="New Story Preview">
                                    <span class="absolute bottom-2 left-2 bg-[#C9A24B] text-[#3D0C11] text-[10px] font-bold px-2 py-0.5 rounded shadow">New Upload Preview</span>
                                </div>
                            </template>
                            <template x-if="!previewSaayaStory">
                                <div>
                                    @if(!empty($saayaStoryImg))
                                        <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-xl overflow-hidden shadow-md border-2 border-[#C9A24B]">
                                            <img src="{{ \App\Models\Setting::imageUrl($saayaStoryImg) }}" class="w-full h-full object-cover" alt="Current Saaya Story">
                                            <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                        </div>
                                    @else
                                        <div class="w-full aspect-[3/4] w-52 bg-[#F9F7F2] rounded-xl flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-[#C9A24B]/50 shadow-inner">
                                            <svg class="w-20 h-28 text-[#3D0C11]/25 mb-2" fill="currentColor" viewBox="0 0 80 110">
                                                <ellipse cx="40" cy="22" rx="14" ry="18"/>
                                                <path d="M10 45 Q25 35 40 38 Q55 35 70 45 L72 95 Q55 105 40 102 Q25 105 8 95 Z"/>
                                            </svg>
                                            <p class="text-xs text-[#3D0C11] font-bold">Default Story Silhouette</p>
                                            <p class="text-[11px] text-[#3D0C11]/50 mt-1">Upload a photo to appear inside the gold foil frame</p>
                                        </div>
                                    @endif
                                </div>
                            </template>
                            @if(!empty($saayaStoryImg))
                                <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                    <span class="text-[11px] text-emerald-600 font-semibold">Custom Story Image Active</span>
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-700 font-medium cursor-pointer">
                                        <input type="checkbox" name="remove_saaya_story_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                        <span>Remove &amp; Restore Silhouette</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="saaya_story_image" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   @change="previewSaayaStory = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#3D0C11] file:text-[#C9A24B] hover:file:bg-[#52131a] border border-slate-200 rounded-xl cursor-pointer bg-white transition">
                            <p class="text-[11px] text-slate-400 mt-1.5">Recommended ratio: 3:4. JPG, PNG, or WEBP.</p>
                        </div>
                    </div>

                    <!-- Story Copy -->
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Heritage &amp; Mastery Narrative Paragraphs
                        </label>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Philosophy Sub-Heading</label>
                            <input type="text" name="saaya_about_philosophy_heading" 
                                   value="{{ $settings['saaya_about_philosophy_heading'] ?? 'Where Ancient Zari Craft Meets Modern Contour' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-serif font-bold focus:border-amber-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 1</label>
                            <textarea name="saaya_story_p1" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">{{ $settings['saaya_story_p1'] ?? 'Saaya was born from a belief that every Indian woman deserves lingerie that honours her form and her heritage. We weave together age-old motifs — jaali embroidery, zari threadwork, lotus prints — with modern fits engineered for real bodies.' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 2</label>
                            <textarea name="saaya_story_p2" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-amber-600 outline-none">{{ $settings['saaya_story_p2'] ?? 'Every piece leaves our atelier only after passing through the hands of our master karigars, who carry forward skills passed down through generations.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sub-section 3: 3 Stats Counters --}}
                <div class="p-5 rounded-2xl bg-amber-50/40 border border-[#C9A24B]/30 space-y-3">
                    <label class="block text-xs font-bold text-[#3D0C11] uppercase tracking-wider">3. Atelier Craft Statistics</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-white border border-[#C9A24B]/30 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 1</label>
                            <input type="text" name="saaya_stat1_num" value="{{ $settings['saaya_stat1_num'] ?? '12+' }}" placeholder="12+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-amber-700">
                            <input type="text" name="saaya_stat1_label" value="{{ $settings['saaya_stat1_label'] ?? 'Years of Craft' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-[#C9A24B]/30 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 2</label>
                            <input type="text" name="saaya_stat2_num" value="{{ $settings['saaya_stat2_num'] ?? '80K+' }}" placeholder="80K+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-amber-700">
                            <input type="text" name="saaya_stat2_label" value="{{ $settings['saaya_stat2_label'] ?? 'Brides Adorned' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-[#C9A24B]/30 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 3</label>
                            <input type="text" name="saaya_stat3_num" value="{{ $settings['saaya_stat3_num'] ?? '450+' }}" placeholder="450+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-amber-700">
                            <input type="text" name="saaya_stat3_label" value="{{ $settings['saaya_stat3_label'] ?? 'Karigars' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                    </div>
                </div>

                {{-- Sub-section 4: 3 Brand Pillars --}}
                <div class="p-5 rounded-2xl bg-amber-50/40 border border-[#C9A24B]/30 space-y-4">
                    <label class="block text-xs font-bold text-[#3D0C11] uppercase tracking-wider">4. Three Brand Pillars (Artisanal Mastery)</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white border border-[#C9A24B]/30 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#C9A24B]/20 text-[#3D0C11]">Pillar 01</span>
                            <input type="text" name="saaya_pillar1_title" value="{{ $settings['saaya_pillar1_title'] ?? 'Pure Mulberry Silk' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 1 Title">
                            <textarea name="saaya_pillar1_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['saaya_pillar1_desc'] ?? 'Sustainably sourced Grade 6A mulberry silk that breathes against the skin, delivering regal drape and featherlight comfort.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-[#C9A24B]/30 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#C9A24B]/20 text-[#3D0C11]">Pillar 02</span>
                            <input type="text" name="saaya_pillar2_title" value="{{ $settings['saaya_pillar2_title'] ?? 'Master Karigar Craft' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 2 Title">
                            <textarea name="saaya_pillar2_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['saaya_pillar2_desc'] ?? 'Each seam is hand-finished by master craftspeople across Rajasthan and Bengal, preserving centuries-old artisanal zardozi techniques.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-[#C9A24B]/30 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#C9A24B]/20 text-[#3D0C11]">Pillar 03</span>
                            <input type="text" name="saaya_pillar3_title" value="{{ $settings['saaya_pillar3_title'] ?? 'Discreet Heirloom Packaging' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 3 Title">
                            <textarea name="saaya_pillar3_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['saaya_pillar3_desc'] ?? 'Your intimacy is sacred. Every order is encased in unmarked, gold-embossed matte keepsakes with zero external brand indicators.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 4. LUMIÈRE ABOUT US TAB                   -->
            <!-- ========================================== -->
            <div x-show="activeAboutTab === 'lumiere'" class="space-y-8" x-cloak>
                <div class="p-3 bg-[#FFF0F3] text-gray-900 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs border border-pink-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                        <span class="font-bold">Lumière Theme — About Us Page &amp; Artisanal Silk Roots</span>
                        <span class="font-serif italic text-[10px] text-pink-600">Everyday Elegance, Made Responsibly</span>
                    </div>
                    <a href="{{ route('about') }}?theme=lumiere" target="_blank" class="inline-flex items-center gap-1 text-xs text-pink-600 hover:underline font-semibold">
                        <span>Preview Live About Page</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                {{-- Sub-section 1: Hero Header --}}
                <div class="p-5 rounded-2xl bg-[#FFF9FA] border border-pink-100 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-pink-100">
                        <span class="text-xs font-bold text-gray-900 uppercase tracking-wider">1. Hero Header &amp; Main Headline</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Badge / Tag</label>
                            <input type="text" name="lumiere_story_badge" value="{{ $settings['lumiere_story_badge'] ?? 'Our Story' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-pink-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Philosophy Lead Badge</label>
                            <input type="text" name="lumiere_about_philosophy_badge" value="{{ $settings['lumiere_about_philosophy_badge'] ?? 'Artisanal Roots' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-pink-400 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Main Title (Supports line breaks)</label>
                        <textarea name="lumiere_story_title" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold bg-white focus:border-pink-400 outline-none">{{ $settings['lumiere_story_title'] ?? "Everyday elegance,\nmade responsibly" }}</textarea>
                    </div>
                </div>

                {{-- Sub-section 2: Story Image Frame & Narrative --}}
                <div class="grid lg:grid-cols-2 gap-8 items-start">
                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Lumière Atelier Photo (Curved Frame)
                            </label>
                            <span class="text-[11px] text-pink-700 font-medium">Replaces silk silhouette</span>
                        </div>
                        @php $lumStoryImg = $settings['lumiere_story_image'] ?? ''; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                            <template x-if="previewLumiereStory">
                                <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-[32px] overflow-hidden shadow-md border-2 border-pink-300">
                                    <img :src="previewLumiereStory" class="w-full h-full object-cover" alt="New Lumiere Story Preview">
                                    <span class="absolute bottom-2 left-2 bg-pink-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">New Preview</span>
                                </div>
                            </template>
                            <template x-if="!previewLumiereStory">
                                <div>
                                    @if(!empty($lumStoryImg))
                                        <div class="relative w-full aspect-[3/4] max-w-[240px] rounded-[32px] overflow-hidden shadow-md border-2 border-pink-300">
                                            <img src="{{ \App\Models\Setting::imageUrl($lumStoryImg) }}" class="w-full h-full object-cover" alt="Current Lumiere Story">
                                            <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                        </div>
                                    @else
                                        <div class="w-full aspect-[3/4] w-52 bg-[#FFF0F3] rounded-[32px] flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-pink-300 shadow-inner">
                                            <svg viewBox="0 0 100 120" class="w-20 h-24 text-pink-400/50 mb-2" fill="currentColor">
                                                <ellipse cx="50" cy="25" rx="16" ry="20"/>
                                                <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
                                            </svg>
                                            <p class="text-xs text-pink-600 font-bold">Default Silk Atelier</p>
                                            <p class="text-[11px] text-gray-500 mt-1">Upload a photo to appear in the soft curved frame</p>
                                        </div>
                                    @endif
                                </div>
                            </template>
                            @if(!empty($lumStoryImg))
                                <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                    <span class="text-[11px] text-emerald-600 font-semibold">Custom Image Active</span>
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-700 font-medium cursor-pointer">
                                        <input type="checkbox" name="remove_lumiere_story_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                        <span>Remove &amp; Restore Silhouette</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="lumiere_story_image" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   @change="previewLumiereStory = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-pink-50 file:text-pink-600 border border-slate-200 rounded-xl cursor-pointer bg-white transition">
                            <p class="text-[11px] text-slate-400 mt-1.5">Recommended ratio: 3:4. JPG, PNG, or WEBP.</p>
                        </div>
                    </div>

                    <!-- Story Copy -->
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Artisanal Roots Narrative Paragraphs
                        </label>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Philosophy Sub-Heading</label>
                            <input type="text" name="lumiere_about_philosophy_heading" 
                                   value="{{ $settings['lumiere_about_philosophy_heading'] ?? 'Hand-Finished Pure Mulberry Silk' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-pink-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 1</label>
                            <textarea name="lumiere_story_p1" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">{{ $settings['lumiere_story_p1'] ?? "Lumière began with a single loom and a simple belief: the things closest to your skin should be the best you own. Every piece is cut from sustainably sourced silk and finished by artisans who have done this for generations." }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 2</label>
                            <textarea name="lumiere_story_p2" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-pink-400 outline-none">{{ $settings['lumiere_story_p2'] ?? "We work directly with master craftspeople in heritage clusters across India, guaranteeing fair wages and keeping age-old textile traditions alive in contemporary silhouettes." }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sub-section 3: 3 Stats Counters --}}
                <div class="p-5 rounded-2xl bg-[#FFF9FA] border border-pink-100 space-y-3">
                    <label class="block text-xs font-bold text-gray-900 uppercase tracking-wider">3. Artisanal Silk Statistics</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-white border border-pink-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 1</label>
                            <input type="text" name="lumiere_stat1_num" value="{{ $settings['lumiere_stat1_num'] ?? '100%' }}" placeholder="100%" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-pink-600">
                            <input type="text" name="lumiere_stat1_label" value="{{ $settings['lumiere_stat1_label'] ?? 'Pure Silk' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-pink-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 2</label>
                            <input type="text" name="lumiere_stat2_num" value="{{ $settings['lumiere_stat2_num'] ?? '300+' }}" placeholder="300+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-pink-600">
                            <input type="text" name="lumiere_stat2_label" value="{{ $settings['lumiere_stat2_label'] ?? 'Artisans' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-pink-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 3</label>
                            <input type="text" name="lumiere_stat3_num" value="{{ $settings['lumiere_stat3_num'] ?? '12' }}" placeholder="12" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-pink-600">
                            <input type="text" name="lumiere_stat3_label" value="{{ $settings['lumiere_stat3_label'] ?? 'Cities' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                    </div>
                </div>

                {{-- Sub-section 4: 3 Brand Pillars --}}
                <div class="p-5 rounded-2xl bg-[#FFF9FA] border border-pink-100 space-y-4">
                    <label class="block text-xs font-bold text-gray-900 uppercase tracking-wider">4. Three Brand Pillars (Responsible Sourcing)</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white border border-pink-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF0F3] text-pink-600">Pillar 01</span>
                            <input type="text" name="lumiere_pillar1_title" value="{{ $settings['lumiere_pillar1_title'] ?? 'Mulberry Silk Quality' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 1 Title">
                            <textarea name="lumiere_pillar1_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['lumiere_pillar1_desc'] ?? 'Featherweight, breathable Grade 6A pure mulberry silk naturally hypoallergenic and temperature-regulating.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-pink-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF0F3] text-pink-600">Pillar 02</span>
                            <input type="text" name="lumiere_pillar2_title" value="{{ $settings['lumiere_pillar2_title'] ?? 'Fair-Wage Handcraft' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 2 Title">
                            <textarea name="lumiere_pillar2_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['lumiere_pillar2_desc'] ?? 'Honouring craftspeople in historic artisan clusters with fair wages, safe ateliers, and sustainable working standards.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-pink-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF0F3] text-pink-600">Pillar 03</span>
                            <input type="text" name="lumiere_pillar3_title" value="{{ $settings['lumiere_pillar3_title'] ?? 'Zero-Plastic Keepsakes' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 3 Title">
                            <textarea name="lumiere_pillar3_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['lumiere_pillar3_desc'] ?? 'Delivered in unmarked, satin-tied reusable presentation boxes. Minimal environmental footprint, absolute discretion.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 5. ORGANIC ABOUT US TAB                   -->
            <!-- ========================================== -->
            <div x-show="activeAboutTab === 'organic'" class="space-y-8" x-cloak>
                <div class="p-3 bg-emerald-950 text-white rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs border border-emerald-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <span class="font-bold">Organic Theme — About Our Farm &amp; Farming Manifesto</span>
                        <span class="text-[10px] text-emerald-300 font-mono">100% Chemical-Free Fresh Produce</span>
                    </div>
                    <a href="{{ route('about') }}?theme=organic" target="_blank" class="inline-flex items-center gap-1 text-xs text-emerald-400 hover:underline font-semibold">
                        <span>Preview Live About Page</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                {{-- Sub-section 1: Organic Hero Section --}}
                <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-emerald-200">
                        <span class="text-xs font-bold text-emerald-950 uppercase tracking-wider">1. About Page Hero Header &amp; Manifesto</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">About Hero Badge</label>
                            <input type="text" name="organic_about_badge" value="{{ $settings['organic_about_badge'] ?? '🌱 Our Farming Manifesto' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-emerald-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Farm Story Badge</label>
                            <input type="text" name="organic_story_badge" value="{{ $settings['organic_story_badge'] ?? 'The Farm-to-Fork Journey' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-emerald-600 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">About Hero Main Headline</label>
                        <input type="text" name="organic_about_title" value="{{ $settings['organic_about_title'] ?? 'Bringing Honest, Pure & Chemical-Free Produce to Indian Homes' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold bg-white focus:border-emerald-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">About Hero Subtitle</label>
                        <textarea name="organic_about_subtitle" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:border-emerald-600 outline-none">{{ $settings['organic_about_subtitle'] ?? 'Nexon Organic was born out of a profound commitment to human wellness and planetary health. We believe food should nourish, heal, and bring us closer to the rhythms of nature.' }}</textarea>
                    </div>
                </div>

                {{-- Sub-section 2: Story Image Frame & Narrative --}}
                <div class="grid lg:grid-cols-2 gap-8 items-start">
                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Organic Farm Visual Photo
                            </label>
                            <span class="text-[11px] text-emerald-700 font-medium">Replaces default farming photo</span>
                        </div>
                        @php $organicStory = $settings['organic_story_image'] ?? ''; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-3">
                            <template x-if="previewOrganicStory">
                                <div class="relative w-full aspect-[4/3] max-w-[320px] rounded-xl overflow-hidden shadow-md">
                                    <img :src="previewOrganicStory" class="w-full h-full object-cover">
                                    <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">New Preview</span>
                                </div>
                            </template>
                            <template x-if="!previewOrganicStory">
                                <div>
                                    @if(!empty($organicStory))
                                        <div class="relative w-full aspect-[4/3] max-w-[320px] rounded-xl overflow-hidden shadow-md">
                                            <img src="{{ \App\Models\Setting::imageUrl($organicStory) }}" class="w-full h-full object-cover">
                                            <span class="absolute bottom-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">Active Live Image</span>
                                        </div>
                                    @else
                                        <div class="w-full aspect-[4/3] w-64 bg-slate-100 rounded-xl flex flex-col items-center justify-center p-6 text-center border border-slate-200">
                                            <span class="text-2xl mb-1">🌱</span>
                                            <p class="text-xs text-slate-600 font-bold">Default Farming Fields Graphic</p>
                                            <p class="text-[10px] text-slate-400 mt-1">Upload a real farm picture or harvest photo</p>
                                        </div>
                                    @endif
                                </div>
                            </template>
                            @if(!empty($organicStory))
                                <div class="flex items-center justify-between w-full pt-2 border-t border-slate-200">
                                    <span class="text-[11px] text-emerald-600 font-semibold">Active Live Image</span>
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
                                        <input type="checkbox" name="remove_organic_story_image" value="1" class="w-3.5 h-3.5 text-rose-600 rounded">
                                        <span>Remove Image</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="organic_story_image" accept="image/*"
                               @change="previewOrganicStory = URL.createObjectURL($event.target.files[0])"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-700 file:text-white border border-slate-200 rounded-xl cursor-pointer bg-white">
                        <p class="text-[11px] text-slate-400 mt-1.5">Recommended ratio: 4:3 (e.g. 800×600px). JPG, PNG, or WEBP.</p>
                    </div>

                    <!-- Story Narrative -->
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Soil Health &amp; Regenerative Ethos Narrative
                        </label>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Farm Story Main Headline</label>
                            <input type="text" name="organic_story_title" 
                                   value="{{ $settings['organic_story_title'] ?? 'Why We Refuse to Compromise on Soil Health' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold focus:border-emerald-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 1 (Soil Reality)</label>
                            <textarea name="organic_story_p1" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none">{{ $settings['organic_story_p1'] ?? "Modern industrial farming relies on synthetic pesticides, chemical sprays, and artificial ripening agents that strip produce of essential micronutrients while poisoning aquifers." }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Story Paragraph 2 (Regenerative Solution)</label>
                            <textarea name="organic_story_p2" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 outline-none">{{ $settings['organic_story_p2'] ?? "At Nexon Organic, we cultivate our fields using regenerative multi-cropping, biodiverse cow-based compost (Jeevamrutha), and solar pest traps. Our harvest is packed in breathable eco-crates and dispatched overnight so you receive farm-fresh vitality within 24 hours of cutting." }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sub-section 3: 2 Stats Counters --}}
                <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-3">
                    <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider">3. Live Farm Statistics</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3 bg-white border border-emerald-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 1</label>
                            <input type="text" name="organic_stat1_num" value="{{ $settings['organic_stat1_num'] ?? '100%' }}" placeholder="100%" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-emerald-700">
                            <input type="text" name="organic_stat1_label" value="{{ $settings['organic_stat1_label'] ?? 'Pesticide & Urea Free' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                        <div class="p-3 bg-white border border-emerald-200 rounded-xl space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase">Stat 2</label>
                            <input type="text" name="organic_stat2_num" value="{{ $settings['organic_stat2_num'] ?? '350+' }}" placeholder="350+" class="w-full px-3 py-1.5 text-xs font-bold text-center border border-slate-200 rounded-lg text-emerald-700">
                            <input type="text" name="organic_stat2_label" value="{{ $settings['organic_stat2_label'] ?? 'Partner Organic Farmers' }}" placeholder="Label" class="w-full px-3 py-1.5 text-[11px] text-center border border-slate-200 rounded-lg text-slate-600">
                        </div>
                    </div>
                </div>

                {{-- Sub-section 4: 4 Core Farm Pillars --}}
                <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-4">
                    <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider">4. Four Core Farming Pillars</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 bg-white border border-emerald-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Pillar 01</span>
                            <input type="text" name="organic_pillar1_title" value="{{ $settings['organic_pillar1_title'] ?? 'Living Soil First' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 1 Title">
                            <textarea name="organic_pillar1_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['organic_pillar1_desc'] ?? 'We nourish the living biome in our soil with cow dung, microbial wash, and mulch instead of petrochemical synthetic urea.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-emerald-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Pillar 02</span>
                            <input type="text" name="organic_pillar2_title" value="{{ $settings['organic_pillar2_title'] ?? '24-Hour Farm-to-Doorstep' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 2 Title">
                            <textarea name="organic_pillar2_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['organic_pillar2_desc'] ?? 'Harvested in pre-dawn hours, chill-packed without artificial wax or ethylene sprays, and delivered before sunset.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-emerald-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Pillar 03</span>
                            <input type="text" name="organic_pillar3_title" value="{{ $settings['organic_pillar3_title'] ?? 'Complete QR Traceability' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 3 Title">
                            <textarea name="organic_pillar3_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['organic_pillar3_desc'] ?? 'Every vegetable box comes with a farm batch code showing you the soil report, harvest timestamp, and the farmer who tended your crop.' }}</textarea>
                        </div>
                        <div class="p-4 bg-white border border-emerald-200 rounded-xl space-y-2">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Pillar 04</span>
                            <input type="text" name="organic_pillar4_title" value="{{ $settings['organic_pillar4_title'] ?? 'Fair Fair-Trade Incomes' }}" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg" placeholder="Pillar 4 Title">
                            <textarea name="organic_pillar4_desc" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg text-slate-600" placeholder="Description">{{ $settings['organic_pillar4_desc'] ?? 'We bypass mandis and middlemen, paying our partner farmers guaranteed minimum premiums 35% above conventional market rates.' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sub-section 5: Homepage Farm Story Extras --}}
                <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-4">
                    <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider">5. Homepage Story Callout Extras (Bullets, CTA &amp; QR Badge)</label>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="p-3 rounded-lg border border-slate-200 bg-white">
                            <label class="block text-[11px] text-slate-500 mb-1 font-bold">Callout 1 Title</label>
                            <input type="text" name="organic_story_f1_title" value="{{ $settings['organic_story_f1_title'] ?? 'Zero Chemical Residue' }}" class="w-full px-2 py-1 text-xs font-bold border border-slate-200 rounded mb-1 bg-white">
                            <label class="block text-[11px] text-slate-500 mb-1 font-bold">Callout 1 Description</label>
                            <textarea name="organic_story_f1_desc" rows="2" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-white">{{ $settings['organic_story_f1_desc'] ?? 'Laboratory tested batches with certified zero chemical residue.' }}</textarea>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-white">
                            <label class="block text-[11px] text-slate-500 mb-1 font-bold">Callout 2 Title</label>
                            <input type="text" name="organic_story_f2_title" value="{{ $settings['organic_story_f2_title'] ?? 'Eco-Safe Packaging' }}" class="w-full px-2 py-1 text-xs font-bold border border-slate-200 rounded mb-1 bg-white">
                            <label class="block text-[11px] text-slate-500 mb-1 font-bold">Callout 2 Description</label>
                            <textarea name="organic_story_f2_desc" rows="2" class="w-full px-2 py-1 text-xs border border-slate-200 rounded bg-white">{{ $settings['organic_story_f2_desc'] ?? '100% biodegradable cornstarch bags and corrugated kraft crates.' }}</textarea>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Manifesto Button Text</label>
                            <input type="text" name="organic_story_btn_text" value="{{ $settings['organic_story_btn_text'] ?? 'Read Our Farming Manifesto' }}" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Manifesto Button Link (optional)</label>
                            <input type="text" name="organic_story_btn_url" value="{{ $settings['organic_story_btn_url'] ?? '' }}" placeholder="/about" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Floating QR Badge Title</label>
                            <input type="text" name="organic_story_badge2" value="{{ $settings['organic_story_badge2'] ?? '100% Traceable Harvest' }}" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1 font-medium">Floating QR Badge Note</label>
                            <input type="text" name="organic_story_badge2_desc" value="{{ $settings['organic_story_badge2_desc'] ?? 'Scan QR on any crate to see farm origins' }}" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 6. GENERIC / FUTURE THEMES ABOUT US TAB    -->
            <!-- ========================================== -->
            @foreach($templates ?? [] as $futureTmpl)
                @if(!in_array($futureTmpl->key, ['vyra', 'buttercup', 'saaya', 'lumiere', 'organic']))
                    <div x-show="activeAboutTab === '{{ $futureTmpl->key }}'" class="space-y-8" x-cloak>
                        <div class="p-3 bg-slate-900 text-white rounded-xl flex items-center justify-between text-xs">
                            <span class="font-bold">{{ $futureTmpl->name }} Theme — About Us Page &amp; Philosophy</span>
                            <a href="{{ route('about') }}?theme={{ $futureTmpl->key }}" target="_blank" class="text-indigo-400 hover:underline">Preview Live About Page ↗</a>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Badge</label>
                                <input type="text" name="{{ $futureTmpl->key }}_story_badge" value="{{ $settings[$futureTmpl->key . '_story_badge'] ?? 'Our Story' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Philosophy Heading</label>
                                <input type="text" name="{{ $futureTmpl->key }}_about_philosophy_heading" value="{{ $settings[$futureTmpl->key . '_about_philosophy_heading'] ?? 'Our Philosophy' }}" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Headline</label>
                            <textarea name="{{ $futureTmpl->key }}_story_title" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold">{{ $settings[$futureTmpl->key . '_story_title'] ?? "Crafted for Her,\nRooted in Grace" }}</textarea>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Paragraph 1</label>
                                <textarea name="{{ $futureTmpl->key }}_story_p1" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm">{{ $settings[$futureTmpl->key . '_story_p1'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Paragraph 2</label>
                                <textarea name="{{ $futureTmpl->key }}_story_p2" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm">{{ $settings[$futureTmpl->key . '_story_p2'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>

        <!-- Store Policies & Size Guide Configuration -->
        <div id="policy-settings" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6"
             x-data="{ activePolicyTab: 'sizeguide' }">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow-sm shadow-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">Policies &amp; Interactive Size Guide</h2>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Global Store Pages
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Customize the live copy, headlines, calculator hints, and policy terms for Size Guide, Shipping Policy, and Return Policy</p>
                    </div>
                </div>

                <!-- Tab switcher -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl">
                    <button type="button" @click="activePolicyTab = 'sizeguide'"
                            :class="activePolicyTab === 'sizeguide' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition">
                        Size Guide
                    </button>
                    <button type="button" @click="activePolicyTab = 'shipping'"
                            :class="activePolicyTab === 'shipping' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition">
                        Shipping Policy
                    </button>
                    <button type="button" @click="activePolicyTab = 'return'"
                            :class="activePolicyTab === 'return' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition">
                        Return Policy
                    </button>
                </div>
            </div>

            <!-- Tab 1: Size Guide -->
            <div x-show="activePolicyTab === 'sizeguide'" class="space-y-6">
                <div class="flex items-center justify-between p-3.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-sky-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Applies to the interactive storefront page <code>/size-guide</code> and size measurement guidance.</span>
                    </div>
                    <a href="{{ route('sizeGuide') }}" target="_blank" class="font-bold underline hover:text-sky-700 shrink-0">View Live Page ↗</a>
                </div>

                <!-- Hero / Top Banner -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Page Header &amp; Introduction</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Top Badge</label>
                            <input type="text" name="sizeguide_badge" value="{{ $settings['sizeguide_badge'] ?? 'Atelier Fit Perfection' }}"
                                   placeholder="Atelier Fit Perfection"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Main Heading</label>
                            <input type="text" name="sizeguide_title" value="{{ $settings['sizeguide_title'] ?? 'The Precision Size Guide' }}"
                                   placeholder="The Precision Size Guide"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Introduction Paragraph / Advisory</label>
                        <textarea name="sizeguide_subtitle" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">{{ $settings['sizeguide_subtitle'] ?? 'Over 80% of women wear the incorrect bra size. Use our interactive measurement calculator to find your exact band, cup, and sister size.' }}</textarea>
                    </div>
                </div>

                <!-- Calculator Section -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Interactive Calculator Card</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Calculator Badge</label>
                            <input type="text" name="sizeguide_calc_badge" value="{{ $settings['sizeguide_calc_badge'] ?? 'Interactive Calculator' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Calculator Title</label>
                            <input type="text" name="sizeguide_calc_title" value="{{ $settings['sizeguide_calc_title'] ?? 'Find Your Bespoke Size' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Calculator Instructions</label>
                        <textarea name="sizeguide_calc_desc" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['sizeguide_calc_desc'] ?? 'Grab a flexible measuring tape while standing relaxed in an unpadded, non-pushup bra.' }}</textarea>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Underbust Measurement Hint</label>
                            <input type="text" name="sizeguide_underbust_hint" value="{{ $settings['sizeguide_underbust_hint'] ?? 'Measure snugly directly underneath your breasts.' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bust Measurement Hint</label>
                            <input type="text" name="sizeguide_bust_hint" value="{{ $settings['sizeguide_bust_hint'] ?? 'Measure loosely across the fullest part of your bust.' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Sister Sizing Matrix & Bottoms -->
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Sister Sizing Matrix</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Section Title</label>
                            <input type="text" name="sizeguide_sister_title" value="{{ $settings['sizeguide_sister_title'] ?? 'Sister Sizing Matrix' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Guidance Description</label>
                            <textarea name="sizeguide_sister_desc" rows="3"
                                      class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['sizeguide_sister_desc'] ?? 'Sister sizes share identical cup volume despite having different band measurements. If your band feels too loose or too snug, navigate horizontally along your sister size group:' }}</textarea>
                        </div>
                    </div>

                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Panties &amp; Sleepwear</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Section Title</label>
                            <input type="text" name="sizeguide_bottoms_title" value="{{ $settings['sizeguide_bottoms_title'] ?? 'Panties, Chemises & Robes' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Guidance Description</label>
                            <textarea name="sizeguide_bottoms_desc" rows="3"
                                      class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['sizeguide_bottoms_desc'] ?? 'For bottoms and sleepwear, measure your waist at the narrowest point and your hips around the fullest curve.' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Custom Sizing Guidelines -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Custom Fitting Notes &amp; Guidelines (Optional)</label>
                    <textarea name="sizeguide_additional_info" rows="3" placeholder="Additional fit tips, measurement precautions, international conversion advice..."
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['sizeguide_additional_info'] ?? '' }}</textarea>
                    <p class="text-[11px] text-slate-400">If filled, an extra custom advice card will be rendered at the bottom of the size guide page.</p>
                </div>
            </div>

            <!-- Tab 2: Shipping Policy -->
            <div x-show="activePolicyTab === 'shipping'" class="space-y-6" style="display: none;">
                <div class="flex items-center justify-between p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-xs text-emerald-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>Applies to the storefront shipping policy page <code>/shipping-policy</code>.</span>
                    </div>
                    <a href="{{ route('shippingPolicy') }}" target="_blank" class="font-bold underline hover:text-emerald-700 shrink-0">View Live Page ↗</a>
                </div>

                <!-- Hero / Top Banner -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Page Header</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Top Badge</label>
                            <input type="text" name="shipping_policy_badge" value="{{ $settings['shipping_policy_badge'] ?? 'Logistics & Privacy' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Main Heading</label>
                            <input type="text" name="shipping_policy_title" value="{{ $settings['shipping_policy_title'] ?? 'Shipping & Discreet Delivery' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle / Summary</label>
                        <textarea name="shipping_policy_subtitle" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_policy_subtitle'] ?? 'Our gold standard delivery experience: fast, trackable, and completely private from our atelier to your doorstep.' }}</textarea>
                    </div>
                </div>

                <!-- Discreet Packaging Banner -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Discreet Packaging Promise Banner</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Banner Badge</label>
                            <input type="text" name="shipping_banner_badge" value="{{ $settings['shipping_banner_badge'] ?? 'Our Privacy Promise' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Banner Title</label>
                            <input type="text" name="shipping_banner_title" value="{{ $settings['shipping_banner_title'] ?? '100% Confidential Outer Packaging' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Banner Description</label>
                        <textarea name="shipping_banner_desc" rows="3"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_banner_desc'] ?? 'Every order is dispatched in an unmarked, tamper-evident exterior box or opaque courier flyer. There are zero mentions of lingerie, brassieres, or intimate apparel anywhere on the exterior mailing label. The sender is listed simply as &quot;LS Logistics&quot;.' }}</textarea>
                    </div>
                </div>

                <!-- Structured Policy Sections -->
                <div class="space-y-4">
                    <!-- Section 1 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 1: Processing &amp; Dispatch</label>
                        <input type="text" name="shipping_sec1_title" value="{{ $settings['shipping_sec1_title'] ?? '1. Processing & Dispatch Timelines' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="shipping_sec1_desc" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_sec1_desc'] ?? 'All in-stock orders are processed within 24 business hours (Monday through Saturday, excluding national holidays). Once dispatched, an SMS and email notification with your live tracking AWB link will be automatically sent.' }}</textarea>
                    </div>

                    <!-- Section 2 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 2: Transit Times</label>
                        <input type="text" name="shipping_sec2_title" value="{{ $settings['shipping_sec2_title'] ?? '2. Estimated Delivery Transit Times' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="shipping_sec2_desc" rows="3" placeholder="Leave blank to use default metro / tier 2 / rest of India breakdown"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_sec2_desc'] ?? '' }}</textarea>
                    </div>

                    <!-- Section 3 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 3: Shipping Rates &amp; Free Delivery</label>
                        <input type="text" name="shipping_sec3_title" value="{{ $settings['shipping_sec3_title'] ?? '3. Shipping Rates & Free Delivery' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="shipping_sec3_desc" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_sec3_desc'] ?? 'We offer Complimentary Express Shipping on all prepaid orders exceeding ₹1,499 across India. For orders below ₹1,499, a nominal flat express shipping fee of ₹99 is applied at checkout.' }}</textarea>
                    </div>

                    <!-- Section 4 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 4: Cash on Delivery (COD)</label>
                        <input type="text" name="shipping_sec4_title" value="{{ $settings['shipping_sec4_title'] ?? '4. Cash on Delivery (COD)' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="shipping_sec4_desc" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_sec4_desc'] ?? 'Cash on Delivery is available for serviceable pincodes with a flat ₹50 handling charge. An OTP or telephonic confirmation may be requested prior to dispatch for high-value COD shipments.' }}</textarea>
                    </div>

                    <!-- Section 5 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 5: Courier Networks</label>
                        <input type="text" name="shipping_sec5_title" value="{{ $settings['shipping_sec5_title'] ?? '5. Courier Partners' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="shipping_sec5_desc" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_sec5_desc'] ?? 'We partner exclusively with top-tier courier networks including Bluedart, Delhivery, Shadowfax, and Xpressbees to ensure safe transit and zero tampering.' }}</textarea>
                    </div>

                    <!-- Custom / Additional Terms -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Additional Shipping Clauses &amp; Disclaimers (Optional)</label>
                        <textarea name="shipping_custom_content" rows="3" placeholder="Optional extra paragraphs, international disclaimers, festival delays..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['shipping_custom_content'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Return Policy -->
            <div x-show="activePolicyTab === 'return'" class="space-y-6" style="display: none;">
                <div class="flex items-center justify-between p-3.5 bg-rose-50/70 border border-rose-100 rounded-xl text-xs text-rose-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                        <span>Applies to the storefront return &amp; exchange policy page <code>/return-policy</code>.</span>
                    </div>
                    <a href="{{ route('returnPolicy') }}" target="_blank" class="font-bold underline hover:text-rose-700 shrink-0">View Live Page ↗</a>
                </div>

                <!-- Hero / Top Banner -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Page Header</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Top Badge</label>
                            <input type="text" name="return_policy_badge" value="{{ $settings['return_policy_badge'] ?? 'Customer Guarantee' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Main Heading</label>
                            <input type="text" name="return_policy_title" value="{{ $settings['return_policy_title'] ?? 'Returns & Hygiene Standards' }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle / Summary</label>
                        <textarea name="return_policy_subtitle" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_policy_subtitle'] ?? 'Your complete confidence and hygiene are our highest priorities. Review our straightforward intimate return policies below.' }}</textarea>
                    </div>
                </div>

                <!-- Hygiene Regulations Banner -->
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Intimate Hygiene Regulations Notice</h3>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notice Heading</label>
                        <input type="text" name="return_banner_title" value="{{ $settings['return_banner_title'] ?? 'Intimate Hygiene Regulations' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notice Description</label>
                        <textarea name="return_banner_desc" rows="3"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_banner_desc'] ?? 'Due to strict Indian health & safety standards for second-skin intimates, items tried directly on the skin without underwear or protective hygiene stickers cannot be restocked or refunded.' }}</textarea>
                    </div>
                </div>

                <!-- Structured Policy Sections -->
                <div class="space-y-4">
                    <!-- Section 1 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 1: Eligible Items for 7-Day Return / Exchange</label>
                        <input type="text" name="return_sec1_title" value="{{ $settings['return_sec1_title'] ?? '1. Eligible Items for 7-Day Return / Exchange' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="return_sec1_desc" rows="3" placeholder="Leave blank to use default Bras, Loungewear, Damaged item bullets"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_sec1_desc'] ?? '' }}</textarea>
                    </div>

                    <!-- Section 2 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 2: Final Sale / Non-Returnable Items</label>
                        <input type="text" name="return_sec2_title" value="{{ $settings['return_sec2_title'] ?? '2. Final Sale (Non-Returnable) Items' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="return_sec2_desc" rows="3" placeholder="Leave blank to use default panties, adhesive bras, clearance bullets"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_sec2_desc'] ?? '' }}</textarea>
                    </div>

                    <!-- Section 3 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 3: How to Initiate a Return</label>
                        <input type="text" name="return_sec3_title" value="{{ $settings['return_sec3_title'] ?? '3. How to Initiate a Return' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="return_sec3_desc" rows="2" placeholder="Leave blank to use default contact & reverse pickup instructions"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_sec3_desc'] ?? '' }}</textarea>
                    </div>

                    <!-- Section 4 -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section 4: Refund Processing &amp; Timelines</label>
                        <input type="text" name="return_sec4_title" value="{{ $settings['return_sec4_title'] ?? '4. Refund Processing' }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        <textarea name="return_sec4_desc" rows="2"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_sec4_desc'] ?? 'Upon receipt and quality inspection at our atelier (usually 2 business days after receipt), refunds are processed directly to your original payment method (Credit card, UPI, Net Banking) within 3 to 5 banking days. COD orders are refunded via instant UPI or NEFT transfer.' }}</textarea>
                    </div>

                    <!-- Custom / Additional Terms -->
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Additional Return Terms &amp; Conditions (Optional)</label>
                        <textarea name="return_custom_content" rows="3" placeholder="Optional extra return stipulations, international return guidelines..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">{{ $settings['return_custom_content'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Universal Product Page Delivery & Returns Accordion (Common Across All Themes) -->
        <div id="product-page-accordion" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold shadow-sm shadow-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-slate-900">Product Page Delivery &amp; Returns Accordion</h2>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Common Across All Themes
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">Universal writeups rendered inside the "Delivery Information" and "Returns &amp; Exchange" drawers on product detail pages across all themes (Buttercup, Saaya, Vyra, Lumière, Organic, etc.)</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Delivery Information Writeup</label>
                    <textarea name="product_delivery_info" rows="3" 
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none text-slate-800"
                              placeholder="Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.">{{ $settings['product_delivery_info'] ?? $settings['organic_delivery_info'] ?? 'Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.' }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1.5">Displayed inside the "Delivery Information" accordion drawer on single product pages across all storefront themes.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Returns &amp; Exchange Writeup</label>
                    <textarea name="product_returns_info" rows="3" 
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none text-slate-800"
                              placeholder="Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.">{{ $settings['product_returns_info'] ?? $settings['organic_returns_info'] ?? 'Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.' }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1.5">Displayed inside the "Returns &amp; Exchange" accordion drawer on single product pages across all storefront themes.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Shipping & Delivery Policies</h2>
                    <p class="text-xs text-slate-500">Thresholds and fees applied automatically during checkout across all 4 themes</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Free Shipping Minimum (₹)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" name="free_shipping_threshold" 
                               value="{{ $settings['free_shipping_threshold'] ?? 1499 }}" 
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none" required>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Carts at or above this amount receive complimentary shipping</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Standard Shipping Cost (₹)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" name="shipping_cost" 
                               value="{{ $settings['shipping_cost'] ?? 99 }}" 
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none" required>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Standard flat shipping applied when cart is below threshold</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Cash on Delivery (COD) Fee (₹)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" name="cod_charge" 
                               value="{{ $settings['cod_charge'] ?? 40 }}" 
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none" required>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Extra convenience charge when paying via COD</p>
                </div>
            </div>
        </div>

        <!-- Tax & Legal Information -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tax & GST Invoicing Details</h2>
                    <p class="text-xs text-slate-500">Appears on print invoices, packing slips, and order confirmations</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">GST / Tax Rate (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="tax_percentage" 
                               value="{{ $settings['tax_percentage'] ?? 18 }}" 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 font-semibold text-sm">%</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">GSTIN Registration Number</label>
                    <input type="text" name="gstin" 
                           value="{{ $settings['gstin'] ?? '27AABCS1429B1ZB' }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm uppercase focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
            </div>
        </div>

        <!-- Store Helpline & Support Contact -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Customer Helpline & Brand Contact</h2>
                    <p class="text-xs text-slate-500">Contact information rendered in theme footers, customer receipts, and notifications</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Phone Helpline</label>
                    <input type="text" name="support_phone" 
                           value="{{ $settings['support_phone'] ?? '+91 98765 43210' }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">WhatsApp Order Helpline</label>
                    <input type="text" name="support_whatsapp" 
                           value="{{ $settings['support_whatsapp'] ?? '+91 98765 43210' }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Support Email</label>
                    <input type="email" name="support_email" 
                           value="{{ $settings['support_email'] ?? 'support@lingerie.store' }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Store Registered Address</label>
                <textarea name="store_address" rows="2" 
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">{{ $settings['store_address'] ?? '101 Fashion Boulevard, Bandra West, Mumbai 400050' }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Store Announcement Banner</label>
                <input type="text" name="announcement_bar" 
                       value="{{ $settings['announcement_bar'] ?? 'Complimentary Shipping on all orders over ₹1,499 | Use code WELCOME10 for 10% off' }}" 
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Invoice / Bill Thank You Footer Note</label>
                <input type="text" name="invoice_footer" 
                       value="{{ $settings['invoice_footer'] ?? \App\Models\Setting::invoiceFooter() }}" 
                       placeholder="e.g. Thank you for choosing Nex-on!"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                <p class="text-[11px] text-slate-400 mt-1">Leave blank to automatically display "Thank you for choosing [Store Name]!"</p>
            </div>
        </div>

        <!-- Store Operations & Toggles -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Operations & Checkout Toggles</h2>
                    <p class="text-xs text-slate-500">Live feature flags for payment methods and emergency maintenance</p>
                </div>
            </div>

            <div class="space-y-4">
                <label class="flex items-start gap-3.5 p-4 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer transition">
                    <input type="checkbox" name="enable_cod" value="1" 
                           {{ ($settings['enable_cod'] ?? '1') == '1' ? 'checked' : '' }} 
                           class="w-4 h-4 mt-0.5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">Enable Cash on Delivery (COD)</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Allows shoppers to select Cash on Delivery at checkout with dynamic handling fee</span>
                    </div>
                </label>

                <label class="flex items-start gap-3.5 p-4 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer transition">
                    <input type="checkbox" name="maintenance_mode" value="1" 
                           {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }} 
                           class="w-4 h-4 mt-0.5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">Storefront Maintenance Banner</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Display notice on the storefront that order processing is undergoing scheduled maintenance</span>
                    </div>
                </label>

                <!-- Order Push Webhooks -->
                <div class="pt-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Order Push Webhook Endpoints</label>
                    <input type="text" name="order_webhook_urls" 
                           value="{{ $settings['order_webhook_urls'] ?? '' }}" 
                           placeholder="https://discord.com/api/webhooks/..., https://hooks.slack.com/services/..." 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1.5">Comma-separated URLs. Whenever a customer places an order, real-time JSON push notifications are dispatched to Discord, Slack, Harold, or your ERP.</p>
                </div>
            </div>
        </div>

        <!-- Mobile App (PWA) & Install Prompt Settings -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-rose-700 text-white flex items-center justify-center font-bold shadow-sm shadow-red-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="3" ry="3" stroke-width="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Mobile App (PWA) &amp; Install Prompt</h2>
                    <p class="text-xs text-slate-500">Configure the floating mobile app installation prompt dialog (matching mobile design benchmark) across all 4 themes</p>
                </div>
            </div>

            <div class="space-y-4">
                <label class="flex items-start gap-3.5 p-4 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer transition">
                    <input type="checkbox" name="pwa_prompt_enabled" value="1" 
                           {{ ($settings['pwa_prompt_enabled'] ?? '1') == '1' ? 'checked' : '' }} 
                           class="w-4 h-4 mt-0.5 text-red-600 rounded border-slate-300 focus:ring-red-500">
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">Enable "Install Mobile App" Storefront Prompt</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Shows the floating app installation card on mobile &amp; desktop with automated once-per-24h dismissal logic and native iOS/Android support</span>
                    </div>
                </label>

                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Prompt Headline</label>
                        <input type="text" name="pwa_prompt_title" 
                               value="{{ $settings['pwa_prompt_title'] ?? 'Install ' . ($settings['store_name'] ?? config('app.name', 'Nex-on')) . ' App' }}" 
                               placeholder="Install {{ config('app.name', 'Nex-on') }} App"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Prompt Subtitle / Benefit Copy</label>
                        <input type="text" name="pwa_prompt_description" 
                               value="{{ $settings['pwa_prompt_description'] ?? 'Get quick access to our collection! Install our app for a faster, better shopping experience on your device.' }}" 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none">
                    </div>
                </div>

                <div class="p-3.5 bg-red-50/70 border border-red-200/60 rounded-xl flex items-center justify-between text-xs text-red-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>PWA assets active: <code>/manifest.json</code>, <code>/sw.js</code>, 192px/512px maskable icons, and native Chromium/iOS Safari Add-to-Home-Screen workflows.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save All Settings</span>
            </button>
        </div>
    </form>

    <!-- Bulk Price Adjustment Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Bulk Catalog Price Adjuster</h2>
                <p class="text-xs text-slate-500">Quickly adjust prices across an entire category or the whole catalog for sales and seasonal campaigns</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.adjustBulkPrices') }}" class="space-y-4">
            @csrf
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Target Scope</label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none bg-white">
                        <option value="">All Catalog Products</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Adjustment Type</label>
                    <select name="adjustment_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none bg-white">
                        <option value="increase_percent">Increase by Percentage (+%)</option>
                        <option value="decrease_percent">Discount / Decrease by Percentage (-%)</option>
                        <option value="increase_fixed">Increase by Fixed Amount (+₹)</option>
                        <option value="decrease_fixed">Discount / Decrease by Fixed Amount (-₹)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Value (% or ₹)</label>
                    <input type="number" step="0.01" min="0.01" name="adjustment_value" placeholder="e.g. 10 or 150" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-400">Updates variant pricing across all matching products in a single database transaction.</span>
                <button type="submit" onclick="return confirm('Are you sure you want to adjust prices across the selected scope? This will update live product prices immediately.')"
                        class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition">
                    Apply Price Adjustment
                </button>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoInput = document.getElementById('siteLogoInput');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const previewEl = document.getElementById('newLogoPreview');
                    const wrapperEl = document.getElementById('newLogoPreviewWrapper');
                    if (previewEl && wrapperEl) {
                        previewEl.src = evt.target.result;
                        wrapperEl.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection

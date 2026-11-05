{{-- resources/views/admin/themes.blade.php
     Admin — Theme switcher.
     Controller passes: $templates (Collection of Template) --}}

<x-admin-layout>

<div class="max-w-4xl mx-auto px-6 py-10">

  {{-- Admin Panel Appearance Theme (White vs Black Theme) --}}
  <div class="mb-12 bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs"
       x-data="{ switchDark: document.documentElement.classList.contains('dark') }"
       @admin-theme-changed.window="switchDark = $event.detail.isDark">
      
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
          <div>
              <div class="flex items-center gap-2 text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                  <span>Admin Interface</span>
                  <span>•</span>
                  <span>System Appearance</span>
              </div>
              <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Admin Panel Theme Options</h2>
              <p class="mt-1 text-xs sm:text-sm text-slate-500">Choose your visual appearance for the Admin Suite. Switch between the classic White Theme and the sleek Black Theme.</p>
          </div>
          
          <!-- Live Black Theme Switch Button -->
          <div class="flex items-center gap-3 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/80 shrink-0">
              <span class="text-xs font-bold text-slate-700 select-none">Black Theme:</span>
              <button type="button" 
                      @click="window.toggleAdminTheme()" 
                      class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      :class="switchDark ? 'bg-indigo-600' : 'bg-slate-300'"
                      role="switch" 
                      :aria-checked="switchDark.toString()"
                      :title="switchDark ? 'Black Theme is ON — Click to switch to White Theme' : 'Black Theme is OFF — Click to switch to Black Theme'">
                  <span class="sr-only">Toggle Black Theme</span>
                  <span aria-hidden="true" 
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                        :class="switchDark ? 'translate-x-5' : 'translate-x-0'"></span>
              </button>
              <span class="text-xs font-black uppercase tracking-wider min-w-[28px]" 
                    :class="switchDark ? 'text-indigo-600' : 'text-slate-400'" 
                    x-text="switchDark ? 'ON' : 'OFF'"></span>
          </div>
      </div>

      <!-- Theme Cards (White Theme vs Black Theme) -->
      <div class="grid sm:grid-cols-2 gap-5 mt-6">
          <!-- White Theme Option Card -->
          <div @click="if (switchDark) window.toggleAdminTheme('light')" 
               class="cursor-pointer rounded-2xl border-2 transition-all p-5 flex flex-col justify-between group"
               :class="!switchDark ? 'border-indigo-600 ring-2 ring-indigo-200/60 bg-indigo-50/15' : 'border-slate-200 hover:border-slate-300 bg-white'">
              <div>
                  <!-- Mockup Swatch Preview -->
                  <div class="h-28 rounded-xl bg-slate-100 border border-slate-200/80 p-3 mb-4 flex flex-col gap-2 overflow-hidden shadow-2xs">
                      <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                          <div class="flex items-center gap-2">
                              <span class="w-3 h-3 rounded-md bg-indigo-600"></span>
                              <span class="h-2 w-16 bg-slate-300 rounded"></span>
                          </div>
                          <span class="h-2 w-8 bg-slate-300 rounded"></span>
                      </div>
                      <div class="grid grid-cols-3 gap-2">
                          <div class="bg-white rounded-lg p-2 border border-slate-200 shadow-2xs">
                              <div class="h-1.5 w-6 bg-slate-200 rounded mb-1"></div>
                              <div class="h-2.5 w-10 bg-slate-800 rounded"></div>
                          </div>
                          <div class="bg-white rounded-lg p-2 border border-slate-200 shadow-2xs">
                              <div class="h-1.5 w-6 bg-slate-200 rounded mb-1"></div>
                              <div class="h-2.5 w-10 bg-slate-800 rounded"></div>
                          </div>
                          <div class="bg-white rounded-lg p-2 border border-slate-200 shadow-2xs">
                              <div class="h-1.5 w-6 bg-slate-200 rounded mb-1"></div>
                              <div class="h-2.5 w-10 bg-slate-800 rounded"></div>
                          </div>
                      </div>
                  </div>

                  <div class="flex items-start justify-between gap-2 mb-1.5">
                      <div class="flex items-center gap-2">
                          <h3 class="text-base font-extrabold text-slate-900">White Theme</h3>
                          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 uppercase tracking-wider">Default</span>
                      </div>
                      <span x-show="!switchDark" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                          Active
                      </span>
                  </div>
                  <p class="text-xs text-slate-500 mb-4">Clean light aesthetics with slate backgrounds, white cards, high daylight contrast, and crisp typography.</p>
              </div>

              <div>
                  <button type="button" 
                          @click.stop="if (switchDark) window.toggleAdminTheme('light')"
                          class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition-all text-center"
                          :class="!switchDark ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 cursor-default' : 'bg-slate-900 hover:bg-slate-800 text-white shadow-xs'">
                      <span x-text="!switchDark ? 'Currently Active' : 'Switch to White Theme'"></span>
                  </button>
              </div>
          </div>

          <!-- Black Theme Option Card -->
          <div @click="if (!switchDark) window.toggleAdminTheme('dark')" 
               class="cursor-pointer rounded-2xl border-2 transition-all p-5 flex flex-col justify-between group"
               :class="switchDark ? 'border-indigo-500 ring-2 ring-indigo-500/30 bg-slate-900/40' : 'border-slate-200 hover:border-slate-300 bg-white'">
              <div>
                  <!-- Mockup Swatch Preview -->
                  <div class="h-28 rounded-xl bg-[#090d16] border border-slate-800 p-3 mb-4 flex flex-col gap-2 overflow-hidden shadow-2xs">
                      <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                          <div class="flex items-center gap-2">
                              <span class="w-3 h-3 rounded-md bg-indigo-500"></span>
                              <span class="h-2 w-16 bg-slate-700 rounded"></span>
                          </div>
                          <span class="h-2 w-8 bg-slate-700 rounded"></span>
                      </div>
                      <div class="grid grid-cols-3 gap-2">
                          <div class="bg-[#0f172a] rounded-lg p-2 border border-slate-800 shadow-2xs">
                              <div class="h-1.5 w-6 bg-slate-700 rounded mb-1"></div>
                              <div class="h-2.5 w-10 bg-slate-200 rounded"></div>
                          </div>
                          <div class="bg-[#0f172a] rounded-lg p-2 border border-slate-800 shadow-2xs">
                              <div class="h-1.5 w-6 bg-slate-700 rounded mb-1"></div>
                              <div class="h-2.5 w-10 bg-slate-200 rounded"></div>
                          </div>
                          <div class="bg-[#0f172a] rounded-lg p-2 border border-slate-800 shadow-2xs">
                              <div class="h-1.5 w-6 bg-slate-700 rounded mb-1"></div>
                              <div class="h-2.5 w-10 bg-slate-200 rounded"></div>
                          </div>
                      </div>
                  </div>

                  <div class="flex items-start justify-between gap-2 mb-1.5">
                      <div class="flex items-center gap-2">
                          <h3 class="text-base font-extrabold text-slate-900">Black Theme</h3>
                          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 uppercase tracking-wider">Executive Dark</span>
                      </div>
                      <span x-show="switchDark" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                          Active
                      </span>
                  </div>
                  <p class="text-xs text-slate-500 mb-4">Deep obsidian dark canvas, sleek charcoal panels, reduced eye strain in low-light environments, and glowing accents.</p>
              </div>

              <div>
                  <button type="button" 
                          @click.stop="if (!switchDark) window.toggleAdminTheme('dark')"
                          class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition-all text-center"
                          :class="switchDark ? 'bg-indigo-600 text-white shadow-xs cursor-default' : 'bg-slate-900 hover:bg-slate-800 text-white shadow-xs'">
                      <span x-text="switchDark ? 'Currently Active' : 'Switch to Black Theme'"></span>
                  </button>
              </div>
          </div>
      </div>
  </div>

  {{-- Page heading: Storefront Themes --}}
  <div class="mb-8 pt-4 border-t border-slate-200/80">
    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
        <span>Customer Storefront</span>
        <span>•</span>
        <span>Public Presentation</span>
    </div>
    <h1 class="text-2xl font-extrabold text-gray-900">Storefront Themes</h1>
    <p class="mt-1 text-sm text-gray-500">Choose which theme customers see on the public website. Only one theme can be active at a time.</p>
  </div>

  {{-- Flash message --}}
  @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      {{ session('success') }}
    </div>
  @endif

  {{-- Theme cards --}}
  <div class="grid sm:grid-cols-2 gap-5">
    @foreach($templates as $t)

      @php
        $previews = [
          'saaya'     => ['bg' => '#3D0C11', 'accent' => '#C9A24B', 'label' => 'Maroon · Gold · Ivory',    'font' => 'Cormorant Garamond'],
          'buttercup' => ['bg' => '#2C2C2C', 'accent' => '#F5C518', 'label' => 'Charcoal · Buttercup',    'font' => 'Playfair Display'],
          'vyra'      => ['bg' => '#1A1A2E', 'accent' => '#E94560', 'label' => 'Navy · Crimson · Ivory',   'font' => 'Italiana'],
          'lumiere'   => ['bg' => '#0F0F0F', 'accent' => '#D4AF37', 'label' => 'Noir · Gold · Champagne',  'font' => 'IM Fell English'],
          'organic'   => ['bg' => '#699E4B', 'accent' => '#F1B927', 'label' => 'Fresh Green · Sunny Gold · Organic Cream', 'font' => 'Nunito'],
        ];
        $p = $previews[$t->key] ?? ['bg' => '#374151', 'accent' => '#9CA3AF', 'label' => '—', 'font' => 'sans-serif'];
      @endphp

      <div class="rounded-xl border {{ $t->is_active ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200' }} overflow-hidden bg-white shadow-sm">

        {{-- Colour swatch strip --}}
        <div class="h-20 flex" style="background:{{ $p['bg'] }}">
          <div class="flex-1"></div>
          <div class="w-16 h-full" style="background:{{ $p['accent'] }}"></div>
        </div>

        <div class="p-5">

          {{-- Name + active badge --}}
          <div class="flex items-start justify-between gap-2 mb-1">
            <h2 class="text-base font-semibold text-gray-900 capitalize">{{ $t->name }}</h2>
            @if($t->is_active)
              <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 border border-green-200">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Active
              </span>
            @endif
          </div>

          {{-- Meta --}}
          <p class="text-xs text-gray-400 mb-0.5">{{ $p['label'] }}</p>
          <p class="text-xs text-gray-400 mb-4">{{ $p['font'] }}</p>

          {{-- Action --}}
          @if($t->is_active)
            <div class="space-y-2">
              <button disabled
                      class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-xs font-medium text-gray-400 cursor-not-allowed">
                Currently Active
              </button>
              <a href="{{ route('admin.settings.index') }}#hero-banners" 
                 class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Edit Hero Banner Image</span>
              </a>
            </div>
          @else
            <form method="POST" action="{{ route('admin.themes.activate', $t) }}">
              @csrf
              <button type="submit"
                      class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors"
                      onclick="return confirm('Switch storefront to {{ addslashes($t->name) }}?')">
                Activate Theme
              </button>
            </form>
          @endif

        </div>
      </div>

    @endforeach
  </div>

  {{-- Route hint for developers --}}
  <p class="mt-8 text-xs text-gray-400">
    Theme key is resolved by <code class="bg-gray-100 px-1 rounded">Template::active()</code> →
    <code class="bg-gray-100 px-1 rounded">storefront.{key}.{page}</code>
  </p>

</div>

</x-admin-layout>

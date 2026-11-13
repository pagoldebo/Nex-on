{{--
    ======================================================================
    THEME: LUMIÈRE — DESIGN TOKENS & TAILWIND RUNTIME CONFIGURATION
    ======================================================================
    File: resources/views/storefront/lumiere/components/tailwind-config.blade.php
    Archetype: Minimalist French Pure Silk Atelier, Editorial & Modern Luxury
    Palette:
      - pink.DEFAULT: #DB2777 (Iconic Haute Couture Silk Pink)
      - pink.dark:    #B81D63 (Deep Mulberry Velvet)
      - pine.DEFAULT: #405549 (Deep French Pine Green — Primary CTA Buttons & Bars)
      - pine.hover:   #33443a (Dark Forest Hover State)
      - butter:       #FFE6A5 (Warm Sunlight Gold)
      - cream:        #FFFAF5 (Pure Silk Canvas Background)
      - blush:        #FFF0F3 (Light Petal Tint Section Divider)
      - cyan:         #E0F7FA (Cool Mist Spotlight Surface)
    Typography:
      - Display: Playfair Display (High-Fashion French Editorial Italic Serif)
      - Body:    Plus Jakarta Sans (Ultra-Crisp Contemporary Body Sans)
    Contract: Injected into <head> by <x-storefront-layout> when $theme->key === 'lumiere'.
--}}
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        pink:   { DEFAULT:'#DB2777', dark:'#B81D63' },
        peach:  '#FFD7BE',
        butter: '#FFE6A5',
        cream:  '#FFFAF5',
        blush:  '#FFF0F3',
        rose:   '#FFB6C1',
        cyan:   '#E0F7FA',
        pine:   { DEFAULT:'#405549', hover:'#33443a', dark:'#28362e' },
      },
      fontFamily: {
        display: ['Playfair Display','serif'],
        body: ['Plus Jakarta Sans','sans-serif'],
      },
    }
  }
}
</script>
<style>
  body{font-family:'Plus Jakarta Sans',sans-serif;background:#FFFAF5;color:#1f2937;--color-accent:#DB2777;}
  .font-display{font-family:'Playfair Display',serif;}

  /* Size pill active styling */
  .size-pill.active,
  .size-pill[aria-selected="true"],
  .pdp-size-pill.active,
  .pdp-size-pill[aria-selected="true"] {
    background-color: #DB2777 !important;
    color: #FFFFFF !important;
    border-color: #DB2777 !important;
    font-weight: 700 !important;
  }
  /* Hide scrollbars for thumbnail strips while allowing smooth touch/wheel scrolling */
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

{{--
    ======================================================================
    THEME: VYRA — DESIGN TOKENS & TAILWIND RUNTIME CONFIGURATION
    ======================================================================
    File: resources/views/storefront/vyra/components/tailwind-config.blade.php
    Archetype: High-Impact Modern Noir, Streetwear & Cyber Aesthetics
    Palette:
      - ink.DEFAULT: #0E0E10 (Deep Background Void)
      - ink.light:   #1A1A1D (Card & Surface Elevation)
      - coral:       #FF3D5A (Primary Neon Accent / Kicker)
      - lime:        #D4FF3D (High-Contrast Cyber Glow)
      - off:         #F2F0EC (High-Legibility Type White)
    Typography:
      - Display: Archivo Black (Heavy Impact Industrial Headings)
      - Body:    Space Grotesk (Clean Modern Monospaced Readability)
    Contract: Injected into <head> by <x-storefront-layout> when $theme->key === 'vyra'.
--}}
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        ink:   { DEFAULT:'#0E0E10', light:'#1A1A1D' },
        coral: { DEFAULT:'#FF3D5A', dark:'#D62847' },
        lime:  '#D4FF3D',
        off:   '#F2F0EC',
      },
      fontFamily: {
        display: ['Archivo Black','sans-serif'],
        body: ['Space Grotesk','sans-serif'],
      },
    }
  }
}
</script>
<style>
  body{font-family:'Space Grotesk',sans-serif;background:#0E0E10;color:#F2F0EC;--color-accent:#FF3D5A;}
  .font-display{font-family:'Archivo Black',sans-serif;}

  /* Size pill active styling */
  .size-pill.active,
  .size-pill[aria-selected="true"],
  .pdp-size-pill.active,
  .pdp-size-pill[aria-selected="true"] {
    background-color: #D4FF3D !important;
    color: #0E0E10 !important;
    border-color: #D4FF3D !important;
    font-weight: 700 !important;
  }
</style>

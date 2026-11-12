{{--
    ======================================================================
    THEME: BUTTERCUP — DESIGN TOKENS & TAILWIND RUNTIME CONFIGURATION
    ======================================================================
    File: resources/views/storefront/buttercup/components/tailwind-config.blade.php
    Archetype: Cloud-Soft, Pastel Comfort Intimates & Loungewear
    Palette:
      - blush.DEFAULT: #FBE7E4 (Soft Blush Cloud Accent)
      - blush.deep:    #F3D2CD (Secondary Contrast Tint)
      - rose.DEFAULT:  #D89AA0 (Primary Brand Accent / Buttons)
      - cream:         #FFFAF5 (Warm Organic Cotton Background)
      - sage:          #A8B89A (Herbal Calming Tone)
      - ink:           #4A3B3A (Warm Espresso Typography)
    Typography:
      - Display: Fraunces (Warm Editorial Soft-Serif Headings)
      - Body:    Quicksand (Curved Organic Humanist Body Sans)
    Contract: Injected into <head> by <x-storefront-layout> when $theme->key === 'buttercup'.
--}}
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        blush: { DEFAULT:'#FBE7E4', deep:'#F3D2CD' },
        rose:  { DEFAULT:'#D89AA0', dark:'#B97680' },
        cream: '#FFFAF5',
        sage:  { DEFAULT:'#A8B89A', dark:'#7F9272' },
        ink:   '#4A3B3A',
      },
      fontFamily: {
        display: ['Fraunces','serif'],
        body: ['Quicksand','sans-serif'],
      },
      borderRadius: { '4xl':'2.5rem' },
    }
  }
}
</script>
<style>
  body{font-family:'Quicksand',sans-serif;background:#FFFAF5;color:#4A3B3A;--color-accent:#D89AA0;}
  .font-display{font-family:'Fraunces',serif;}

  /* Size pill active styling */
  .size-pill.active,
  .size-pill[aria-selected="true"],
  .pdp-size-pill.active,
  .pdp-size-pill[aria-selected="true"] {
    background-color: #D89AA0 !important;
    color: #FFFAF5 !important;
    border-color: #D89AA0 !important;
    font-weight: 700 !important;
  }
</style>

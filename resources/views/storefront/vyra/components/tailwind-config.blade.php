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

  /* ═══════════════════════════════════════════════════════════════
     VYRA THEME: PRODUCT RICH TEXT / HTML HIGHLIGHTS NORMALIZATION
     Converts legacy dark-colored inline styles into ultra-crisp,
     high-contrast cyberpunk/noir typography on #0E0E10 background.
     ═══════════════════════════════════════════════════════════════ */
  .product-rich-text {
    color: #F2F0EC !important;
  }
  .product-rich-text p,
  .product-rich-text div,
  .product-rich-text span,
  .product-rich-text li,
  .product-rich-text td,
  .product-rich-text th,
  .product-rich-text label {
    color: #E5E7EB !important;
  }
  /* Spec labels (e.g., Works:, Fabric:, Saree Length:, etc.) */
  .product-rich-text li > span:first-child,
  .product-rich-text .spec-label,
  .product-rich-text [style*="color: #6b7280"],
  .product-rich-text [style*="color:#6b7280"],
  .product-rich-text [style*="color: rgb(107, 114, 128)"],
  .product-rich-text .text-gray-500,
  .product-rich-text .text-gray-600 {
    color: #9CA3AF !important;
    font-weight: 500 !important;
  }
  /* Spec values & bold elements (e.g., Embroidery, Handwoven tussar, 5.5 meters) */
  .product-rich-text strong,
  .product-rich-text b,
  .product-rich-text li > span:last-child:not(:first-child),
  .product-rich-text [style*="color: #111827"],
  .product-rich-text [style*="color:#111827"],
  .product-rich-text [style*="color: rgb(17, 24, 39)"] {
    color: #FFFFFF !important;
    font-weight: 600 !important;
  }
  /* Section Headings (e.g., Story, Wash & Care:, Production Time:) */
  .product-rich-text h1,
  .product-rich-text h2,
  .product-rich-text h3,
  .product-rich-text h4,
  .product-rich-text h5,
  .product-rich-text h6,
  .product-rich-text [style*="color: #1a1a1a"],
  .product-rich-text [style*="color:#1a1a1a"],
  .product-rich-text [style*="font-family: Georgia"] {
    color: #D4FF3D !important;
    font-family: 'Space Grotesk', sans-serif !important;
    letter-spacing: 0.05em;
  }
  /* Red / Maroon highlight accents (e.g. RATRI | রাত্রি, Nobomi night) convert to vibrant Vyra Coral Neon */
  .product-rich-text [style*="color: #990000"],
  .product-rich-text [style*="color:#990000"],
  .product-rich-text [style*="color: rgb(153, 0, 0)"],
  .product-rich-text [style*="color: #c53030"],
  .product-rich-text [style*="color:#c53030"],
  .product-rich-text [style*="color: #e53e3e"],
  .product-rich-text [style*="color:#e53e3e"] {
    color: #FF3D5A !important;
  }
  /* Clean divider lines */
  .product-rich-text li,
  .product-rich-text tr,
  .product-rich-text [style*="border-bottom: 1px solid #e5e7eb"],
  .product-rich-text [style*="border-top: 1px solid #e5e7eb"],
  .product-rich-text [style*="border-bottom"],
  .product-rich-text [style*="border-top"] {
    border-color: rgba(255, 61, 90, 0.25) !important;
  }
  /* Gold / colored accent left border (e.g., story box) */
  .product-rich-text [style*="border-left"] {
    border-left-color: #D4FF3D !important;
  }
  /* Box backgrounds (disclaimers/alerts) */
  .product-rich-text [style*="background-color: #fff"],
  .product-rich-text [style*="background-color:#fff"],
  .product-rich-text [style*="background-color: rgb(255, 245, 245)"] {
    background-color: #1A1A1D !important;
    border-left-color: #FF3D5A !important;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
  }
  /* General dark gray body text classes */
  .product-rich-text .text-gray-700,
  .product-rich-text .text-gray-800,
  .product-rich-text .text-gray-900,
  .product-rich-text [style*="color: #374151"],
  .product-rich-text [style*="color:#374151"],
  .product-rich-text [style*="color: #4b5563"],
  .product-rich-text [style*="color:#4b5563"],
  .product-rich-text [style*="color: #4a5568"],
  .product-rich-text [style*="color:#4a5568"] {
    color: #D1D5DB !important;
  }
</style>

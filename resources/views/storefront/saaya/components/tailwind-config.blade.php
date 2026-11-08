{{--
    ======================================================================
    THEME: SAAYA — DESIGN TOKENS & TAILWIND RUNTIME CONFIGURATION
    ======================================================================
    File: resources/views/storefront/saaya/components/tailwind-config.blade.php
    Archetype: Royal Indian Trousseau, Heritage Zari Silk & Velvet Couture
    Palette:
      - maroon.DEFAULT: #3D0C11 (Deep Heritage Velvet Burgundy)
      - maroon.dark:    #260609 (Shadow Contrast)
      - gold.DEFAULT:   #C9A24B (Antique Zari Gold Foil Accent)
      - gold.light:     #E3CC8C (Champagne Shimmer)
      - ivory:          #F7F1E6 (Silk Cream Canvas Background)
      - blush:          #E8C4C0 (Rose Quartz Accent)
    Typography:
      - Display: Cormorant Garamond (Regal Aristocratic Classical Serif)
      - Body:    Jost (Balanced Geometric Clean Sans)
    Contract: Injected into <head> by <x-storefront-layout> when $theme->key === 'saaya'.
--}}
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        maroon: { DEFAULT:'#3D0C11', dark:'#260609', light:'#5A1620' },
        gold:   { DEFAULT:'#C9A24B', light:'#E3CC8C', dark:'#9A7A33' },
        ivory:  '#F7F1E6',
        blush:  '#E8C4C0',
      },
      fontFamily: {
        display: ['Cormorant Garamond','serif'],
        body:    ['Jost','sans-serif'],
      },
    }
  }
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  body { font-family:'Jost',sans-serif; background:#F7F1E6; color:#3D0C11; -webkit-font-smoothing:antialiased; }
  ::selection { background:#C9A24B; color:#F7F1E6; }

  /* Foil-frame reveal — signature Saaya element */
  .foil-frame { position:relative; }
  .foil-frame svg.frame-line { position:absolute;inset:0;width:100%;height:100%;pointer-events:none; }
  .foil-frame svg.frame-line rect {
    fill:none; stroke:#C9A24B; stroke-width:1.5;
    stroke-dasharray:1500; stroke-dashoffset:1500;
    transition:stroke-dashoffset 1.8s cubic-bezier(.4,0,.2,1);
  }
  .foil-frame.in-view svg.frame-line rect { stroke-dashoffset:0; }

  /* Scroll/fade reveal */
  .reveal { opacity:0; transform:translateY(24px); transition:opacity .9s ease,transform .9s ease; }
  .reveal.in-view { opacity:1; transform:translateY(0); }

  /* Gold underline grow on desktop nav */
  .underline-grow { position:relative; }
  .underline-grow::after {
    content:''; position:absolute; left:0; bottom:-4px;
    height:1px; width:0; background:#C9A24B; transition:width .35s ease;
  }
  .underline-grow:hover::after { width:100%; }

  /* Mega-menu fade */
  .menu-panel {
    opacity:0; visibility:hidden; transform:translateY(-8px);
    transition:opacity .25s ease, transform .25s ease, visibility .25s;
  }
  .menu-wrap:hover .menu-panel, .menu-wrap.active .menu-panel {
    opacity:1; visibility:visible; transform:translateY(0);
  }

  /* Gold CTA button */
  .gold-btn {
    background:#C9A24B; color:#3D0C11;
    transition:background .3s ease, transform .3s ease, box-shadow .3s ease;
  }
  .gold-btn:hover { background:#E3CC8C; transform:translateY(-2px); box-shadow:0 10px 30px -10px rgba(201,162,75,.6); }

  /* Card image zoom */
  .card-img-zoom { transition:transform 1.1s cubic-bezier(.16,1,.3,1); }
  .card-img-wrap:hover .card-img-zoom { transform:scale(1.06); }

  /* Dotted gold divider */
  .petal-divider {
    background-image:radial-gradient(circle,#C9A24B 1.5px,transparent 1.5px);
    background-size:14px 14px; height:1.5px;
  }

  /* Marquee promo bar */
  .marquee-track { display:flex; width:max-content; animation:marquee 28s linear infinite; }
  @keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }

  /* Tab bar */
  .tab-bar {
    position:fixed; left:0; right:0; bottom:0; z-index:55;
    background:#F7F1E6; border-top:1px solid rgba(201,162,75,.35);
    padding-bottom:env(safe-area-inset-bottom);
    box-shadow:0 -8px 24px -8px rgba(61,12,17,.12);
  }
  .tab-item { position:relative; display:flex; flex-direction:column; align-items:center;
              justify-content:center; gap:3px; padding:10px 0; color:#3D0C11; opacity:.55;
              transition:opacity .25s ease, transform .25s ease; }
  .tab-item.active { opacity:1; }
  .tab-item.active svg { transform:translateY(-2px); }
  .tab-item:active { transform:scale(.92); }
  .tab-item .tab-dot { position:absolute; top:4px; width:4px; height:4px; border-radius:50%;
                       background:#C9A24B; opacity:0; transition:opacity .25s ease; }
  .tab-item.active .tab-dot { opacity:1; }
  .tab-badge { position:absolute; top:2px; right:18%; background:#C9A24B; color:#3D0C11;
               font-size:9px; font-weight:600; width:15px; height:15px; border-radius:50%;
               display:flex; align-items:center; justify-content:center; }

  /* Category bottom sheet */
  #sheetOverlay {
    position:fixed; inset:0; z-index:70; background:rgba(38,6,9,.55);
    opacity:0; visibility:hidden; transition:opacity .3s ease, visibility .3s;
  }
  #sheetOverlay.open { opacity:1; visibility:visible; }
  .bottom-sheet {
    position:fixed; left:0; right:0; bottom:0; z-index:71;
    background:#F7F1E6; border-radius:24px 24px 0 0;
    max-height:80vh; overflow-y:auto;
    padding-bottom:calc(env(safe-area-inset-bottom) + 24px);
    transform:translateY(100%);
    transition:transform .38s cubic-bezier(.16,1,.3,1);
    box-shadow:0 -10px 40px -10px rgba(61,12,17,.3);
  }
  #sheetOverlay.open .bottom-sheet { transform:translateY(0); }
  .sheet-handle { width:40px; height:4px; background:#C9A24B; border-radius:99px; margin:12px auto 4px; }

  /* Page fade-in */
  @keyframes pageFadeIn { from{opacity:0;} to{opacity:1;} }

  /* Accessibility: respect reduced motion */
  @media (prefers-reduced-motion:reduce) {
    .foil-frame svg.frame-line rect { transition:none; }
    .reveal { transition:none; }
    .marquee-track { animation:none; }
  }
</style>

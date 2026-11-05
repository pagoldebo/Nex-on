{{-- resources/views/storefront/lumiere/components/tailwind-config.blade.php --}}
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
</style>

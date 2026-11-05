{{-- resources/views/storefront/vyra/components/tailwind-config.blade.php --}}
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
</style>

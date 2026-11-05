{{-- resources/views/storefront/buttercup/components/tailwind-config.blade.php --}}
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
</style>

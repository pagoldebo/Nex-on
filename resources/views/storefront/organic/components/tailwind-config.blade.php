{{-- resources/views/storefront/organic/components/tailwind-config.blade.php
     Organic Market design tokens: fresh leaf greens, warm earth, sunshine gold, and natural cream. --}}
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        organic: {
          50:  '#F4F9F1',
          100: '#E6F3E0',
          200: '#CEE8C3',
          300: '#A9D796',
          400: '#83C268',
          500: '#699E4B', // Core Organic Leaf Green
          600: '#56843C',
          700: '#43682F',
          800: '#345025',
          900: '#2A4B1A', // Deep Forest Olive
          950: '#14250B',
        },
        citrus: {
          DEFAULT: '#F95F09',
          warm:    '#F7A422',
          gold:    '#FFC107',
          light:   '#FFF9EB',
        },
        cream: {
          DEFAULT: '#F9F8F3',
          soft:    '#F3F4EE',
          pure:    '#FFFFFF',
        },
        earth: {
          dark:  '#222222',
          muted: '#666666',
          light: '#8E8E93',
          border:'#E5E7EB',
        }
      },
      fontFamily: {
        display: ['Nunito', 'sans-serif'],
        body:    ['Open Sans', 'sans-serif'],
        heading: ['Nunito', 'sans-serif'],
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
        '4xl': '2rem',
      },
      boxShadow: {
        'organic': '0 10px 30px -5px rgba(105, 158, 75, 0.15)',
        'organic-hover': '0 15px 35px -5px rgba(105, 158, 75, 0.25)',
      }
    }
  }
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<style>
  :root {
    --color-primary: #699E4B;
    --color-accent: #699E4B;
    --color-citrus: #F95F09;
    --color-gold: #F7A422;
    --color-bg: #F9F8F3;
    --color-text: #222222;
  }
  body {
    font-family: 'Open Sans', sans-serif;
    background-color: #F9F8F3;
    color: #222222;
    -webkit-font-smoothing: antialiased;
  }
  h1, h2, h3, h4, h5, h6, .font-display {
    font-family: 'Nunito', sans-serif;
    font-weight: 700;
  }
  ::selection {
    background: #699E4B;
    color: #FFFFFF;
  }
</style>


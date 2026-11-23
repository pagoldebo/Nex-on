/**
 * ======================================================================
 * THEME: SAAYA — TAILWIND CONFIG & THEME JS
 * ======================================================================
 */
tailwind.config = {
    theme: {
        extend: {
            colors: {
                maroon: { DEFAULT: '#3D0C11', dark: '#260609', light: '#5A1620' },
                gold:   { DEFAULT: '#C9A24B', light: '#E3CC8C', dark: '#9A7A33' },
                ivory:  '#F7F1E6',
                blush:  '#E8C4C0',
            },
            fontFamily: {
                display: ['Cormorant Garamond', 'serif'],
                body:    ['Jost', 'sans-serif'],
            },
        }
    }
};

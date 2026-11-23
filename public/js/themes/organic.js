/**
 * ======================================================================
 * THEME: VERDIA (ORGANIC) — TAILWIND CONFIG & THEME JS
 * ======================================================================
 */
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
                    500: '#699E4B',
                    600: '#56843C',
                    700: '#43682F',
                    800: '#345025',
                    900: '#2A4B1A',
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
};

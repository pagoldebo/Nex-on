/**
 * ======================================================================
 * THEME: LUMIÈRE — TAILWIND CONFIG & THEME JS
 * ======================================================================
 */
tailwind.config = {
    theme: {
        extend: {
            colors: {
                pink:   { DEFAULT: '#DB2777', dark: '#B81D63' },
                peach:  '#FFD7BE',
                butter: '#FFE6A5',
                cream:  '#FFFAF5',
                blush:  '#FFF0F3',
                rose:   '#FFB6C1',
                cyan:   '#E0F7FA',
                pine:   { DEFAULT: '#405549', hover: '#33443a', dark: '#28362e' },
            },
            fontFamily: {
                display: ['Playfair Display', 'serif'],
                body: ['Plus Jakarta Sans', 'sans-serif'],
            },
        }
    }
};

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** Maldives palette — sampled from brand color sheet */
const palette = {
    iru: '#E8A84A',
    dhooni: '#F5C761',
    ruh: '#54B289',
    moodhu: '#B8E6D4',
    miyaru: '#85CAE9',
    madi: '#5999CF',
    muraka: '#1E2D5B',
};

function scale(base, { light = true, dark = true } = {}) {
    return {
        50: light ? mix(base, '#ffffff', 0.92) : base,
        100: light ? mix(base, '#ffffff', 0.84) : base,
        200: light ? mix(base, '#ffffff', 0.68) : base,
        300: light ? mix(base, '#ffffff', 0.52) : base,
        400: light ? mix(base, '#ffffff', 0.28) : base,
        500: base,
        600: dark ? mix(base, '#000000', 0.12) : base,
        700: dark ? mix(base, '#000000', 0.24) : base,
        800: dark ? mix(base, '#000000', 0.38) : base,
        900: dark ? mix(base, '#000000', 0.52) : base,
        950: dark ? mix(base, '#000000', 0.68) : base,
    };
}

function mix(hex, target, weight) {
    const parse = (h) => {
        const n = parseInt(h.replace('#', ''), 16);
        return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
    };
    const [r1, g1, b1] = parse(hex);
    const [r2, g2, b2] = parse(target);
    const w = weight;
    const r = Math.round(r1 * (1 - w) + r2 * w);
    const g = Math.round(g1 * (1 - w) + g2 * w);
    const b = Math.round(b1 * (1 - w) + b2 * w);
    return `#${[r, g, b].map((x) => x.toString(16).padStart(2, '0')).join('')}`;
}

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                iru: scale(palette.iru),
                dhooni: scale(palette.dhooni),
                ruh: scale(palette.ruh),
                moodhu: scale(palette.moodhu),
                miyaru: scale(palette.miyaru),
                madi: scale(palette.madi),
                muraka: scale(palette.muraka, { light: false }),
                brand: {
                    DEFAULT: palette.madi,
                    foreground: '#ffffff',
                    muted: palette.miyaru,
                },
                accent: {
                    DEFAULT: palette.iru,
                    foreground: palette.muraka,
                },
                surface: {
                    DEFAULT: '#ffffff',
                    muted: palette.moodhu,
                },
            },
            boxShadow: {
                card: '0 1px 3px 0 rgb(30 45 91 / 0.06), 0 1px 2px -1px rgb(30 45 91 / 0.06)',
                'card-hover': '0 4px 12px 0 rgb(30 45 91 / 0.1)',
            },
            letterSpacing: {
                label: '0.12em',
            },
        },
    },

    plugins: [forms],
};

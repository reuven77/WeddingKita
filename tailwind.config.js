import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Safelist class yang di-generate secara dinamis via PHP (match() / ternary)
    // Tanpa ini, Tailwind purge class-class ini saat production build
    safelist: [
        // Ribbon kategori (background + text + border)
        'bg-sky-ribbon',   'text-white',  'border-sky-ribbon',
        'bg-blossom-pink', 'text-plum-ink', 'border-blossom-pink',
        'bg-meadow-green', 'border-meadow-green',
        'bg-marigold',     'border-marigold',
        'bg-poppy-red',    'border-poppy-red',
        'hover:opacity-90',
        // Stamp badge
        'bg-petal-cream/60', 'text-plum-ink/60',
        'bg-meadow-green/20', 'text-meadow-green',
        'bg-blossom-pink/20', 'text-blossom-pink',
        'bg-poppy-red/20',    'text-poppy-red',
        'bg-marigold/20',     'text-marigold',
        'bg-sky-ribbon/20',   'text-sky-ribbon',
        // Shadow utilities used in dynamic contexts
        'shadow-[2px_2px_0px_rgba(44,30,51,1)]',
        'shadow-[4px_4px_0px_rgba(44,30,51,1)]',
        // fill utilities
        'fill-marigold', 'fill-meadow-green',
    ],

    theme: {
        extend: {
            colors: {
                'petal-cream': '#FFF7EA',
                'plum-ink': '#2C1E33',
                'marigold': '#FFB627',
                'sky-ribbon': '#2F8FE0',
                'blossom-pink': '#FF6F91',
                'meadow-green': '#3FAE68',
                'poppy-red': '#E4572E',
            },
            fontFamily: {
                display: ['"Instrument Serif"', 'serif'],
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"Space Mono"', ...defaultTheme.fontFamily.mono],
            },
            keyframes: {
                swing: {
                    '0%, 100%': { transform: 'rotate(-2deg)' },
                    '20%': { transform: 'rotate(2deg)' },
                    '40%': { transform: 'rotate(-3deg)' },
                    '60%': { transform: 'rotate(1deg)' },
                    '80%': { transform: 'rotate(-1deg)' },
                }
            },
            animation: {
                swing: 'swing 1s ease-in-out',
            },
        },
    },


    plugins: [forms],
};

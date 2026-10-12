import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'rj-purple': '#7c3aed',
                'rj-purple-light': '#a855f7',
                'rj-pink': '#ec4899',
                'rj-dark-1': '#1a1030',
                'rj-dark-2': '#231243',
                'rj-dark-3': '#2d1754',
            },
        },
    },

    plugins: [forms],
};

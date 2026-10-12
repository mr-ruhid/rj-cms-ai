import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'rj-purple': 'rgb(var(--rj-purple) / <alpha-value>)',
                'rj-purple-light': 'rgb(var(--rj-purple-light) / <alpha-value>)',
                'rj-pink': 'rgb(var(--rj-pink) / <alpha-value>)',
                'rj-dark-1': 'rgb(var(--rj-dark-1) / <alpha-value>)',
                'rj-dark-2': 'rgb(var(--rj-dark-2) / <alpha-value>)',
                'rj-dark-3': 'rgb(var(--rj-dark-3) / <alpha-value>)',
            },
        },
    },

    plugins: [forms],
};

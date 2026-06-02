import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import rtl from 'tailwindcss-rtl';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                primary:   { DEFAULT: '#1B4F72', light: '#2471A3', dark: '#154360' },
                accent:    { DEFAULT: '#2ECC71', light: '#58D68D', dark: '#27AE60' },
                warning:   { DEFAULT: '#F39C12', light: '#F8C471', dark: '#D68910' },
                danger:    { DEFAULT: '#E74C3C', light: '#F1948A', dark: '#CB4335' },
                surface:   '#F8FAFC',
                'surface-dark': '#EEF2F7',
            },
            fontFamily: {
                sans:    ['IBM Plex Sans', ...defaultTheme.fontFamily.sans],
                arabic:  ['IBM Plex Arabic', 'IBM Plex Sans', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                card: '0 1px 4px 0 rgba(0,0,0,.08), 0 0 0 1px rgba(0,0,0,.04)',
            },
        },
    },

    plugins: [forms, rtl],
};

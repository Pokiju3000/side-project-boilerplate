import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/ @type {import('tailwindcss').Config} /
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/.blade.php',
        './storage/framework/views/*.php',
        './resources/views//*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                accent: '#2357EB',      // Blue accent
                lightBlue: '#A7B9D8',     // Light-blue background
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

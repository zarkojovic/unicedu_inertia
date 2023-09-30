import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'gradient': 'linear-gradient(45deg, rgb(229, 100, 70) 0%, rgb(235, 171, 88) 100%)',
            }
        },
        textColor: {
            'primary': '#FFAE1F',
            // 'secondary': '#ffed4a',
            // 'danger': '#e3342f',
        }
    },

    plugins: [forms],
};

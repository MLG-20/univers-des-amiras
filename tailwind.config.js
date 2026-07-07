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
                sans: ['"Work Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                amiras: {
                    cream: '#FDFCFA',
                    ivory: '#F3EDE1',
                    ink: '#1A1815',
                    gold: '#B8923F',
                    bordeaux: '#5C1A28',
                    taupe: '#8A8074',
                },
            },
        },
    },

    plugins: [forms],
};

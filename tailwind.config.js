import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                pca: {
                    darkest: '#028c42', // green-900
                    dark: '#0b9e4f',    // green-800
                    primary: '#028c42', // green-700
                    mid: '#10b981',     // green-600
                    light: '#34d399',   // green-500
                    yellow: '#dfed1f',
                    'yellow-dark': '#c5d11b',
                    surface: '#f0fdf4',
                }
            },
            fontFamily: {
                sans: ['Sora', ...defaultTheme.fontFamily.sans],
                serif: [...defaultTheme.fontFamily.serif],
            },
        },
    },
    plugins: [],
};

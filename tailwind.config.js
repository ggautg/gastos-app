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
                sans: [
                    '"Instrument Sans Variable"',
                    '"Instrument Sans"',
                    ...defaultTheme.fontFamily.sans,
                ],
                display: [
                    '"Fraunces Variable"',
                    '"Fraunces"',
                    ...defaultTheme.fontFamily.serif,
                ],
                mono: [
                    '"JetBrains Mono Variable"',
                    '"JetBrains Mono"',
                    ...defaultTheme.fontFamily.mono,
                ],
            },
            boxShadow: {
                haze: '0 40px 80px -48px rgba(10, 28, 23, 0.55)',
            },
        },
    },
    plugins: [forms],
};

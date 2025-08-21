// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme'
import daisyui from 'daisyui'

export default {
    darkMode: 'class', // <-- REQUIRED for `dark:` utilities to work with the toggle
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        // add more as needed (Vue/TSX etc.)
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [daisyui],
    daisyui: {
        // Keep it tight to the two themes you’re actually using
        themes: ['nord', 'dim'],
        logs: false,
    },
}

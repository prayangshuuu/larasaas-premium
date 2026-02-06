// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme'
import typography from '@tailwindcss/typography'
import daisyui from 'daisyui'

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
                sans: ['"Instrument Sans"', '"Figtree"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // SaaS Brand Kit - Indigo & Slate
                primary: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1', // Brand Primary
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                slate: {
                    850: '#151f32', // Custom dark bg
                    900: '#0f172a',
                    950: '#020617',
                }
            },
            borderRadius: {
                'lg': '0.5rem', // 8px - Standard for cards/inputs
                'xl': '0.75rem', // 12px - Large cards
                '2xl': '1rem',   // 16px - Modals
            },
            boxShadow: {
                'sm': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'DEFAULT': '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                'md': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'lg': '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
            }
        },
    },
    plugins: [
        typography,
    ],
}

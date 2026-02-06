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
        },
    },
    plugins: [
        typography,
        daisyui,
    ],
    daisyui: {
        themes: [
            {
                light: {
                    ...require("daisyui/src/theming/themes")["light"],
                    "primary": "#3b82f6", // blue-500
                    "primary-content": "#ffffff",
                    "secondary": "#a855f7", // purple-500
                    "accent": "#f59e0b", // amber-500
                    "neutral": "#374151", // gray-700
                    "base-100": "#ffffff",
                    "base-200": "#f9fafb", // gray-50
                    "base-300": "#f3f4f6", // gray-100
                    "info": "#3b82f6",
                    "success": "#10b981",
                    "warning": "#f59e0b",
                    "error": "#ef4444",
                    "--rounded-box": "1rem",
                    "--rounded-btn": "0.5rem",
                },
                dark: {
                    ...require("daisyui/src/theming/themes")["dark"],
                    "primary": "#3b82f6",
                    "primary-content": "#ffffff",
                    "secondary": "#a855f7",
                    "accent": "#fbbf24",
                    "neutral": "#1f2937",
                    "base-100": "#0f172a", // slate-900
                    "base-200": "#1e293b", // slate-800
                    "base-300": "#334155", // slate-700
                    "info": "#3b82f6",
                    "success": "#10b981",
                    "warning": "#f59e0b",
                    "error": "#ef4444",
                    "--rounded-box": "1rem",
                    "--rounded-btn": "0.5rem",
                },
            },
        ],
    }
}

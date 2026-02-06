import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import flattenColorPalette from 'tailwindcss/lib/util/flattenColorPalette';
import svgToDataUri from 'mini-svg-data-uri';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Instrument Sans"', '"Figtree"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Aceternity Dark Theme Palette
                slate: {
                    850: '#151f32',
                    900: '#0f172a',
                    950: '#020617',
                },
                // Custom colors used in Aceternity components
                neutral: colors.neutral,
                zinc: colors.zinc,
            },
            animation: {
                shimmer: "shimmer 2s linear infinite",
                spotlight: "spotlight 2s ease .75s 1 forwards",
                meteor: "meteor 5s linear infinite",
                scroll: "scroll var(--animation-duration, 40s) var(--animation-direction, forwards) linear infinite",
            },
            keyframes: {
                shimmer: {
                    from: {
                        backgroundPosition: "0 0"
                    },
                    to: {
                        backgroundPosition: "-200% 0"
                    }
                },
                spotlight: {
                    "0%": {
                        opacity: 0,
                        transform: "translate(-72%, -62%) scale(0.5)"
                    },
                    "100%": {
                        opacity: 1,
                        transform: "translate(-50%,-40%) scale(1)"
                    }
                },
                meteor: {
                    "0%": { transform: "rotate(215deg) translateX(0)", opacity: "1" },
                    "70%": { opacity: "1" },
                    "100%": {
                        transform: "rotate(215deg) translateX(-500px)",
                        opacity: "0",
                    },
                },
                scroll: {
                    to: {
                        transform: "translate(calc(-50% - 0.5rem))",
                    },
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        addVariablesForColors,
        function ({ matchUtilities, theme }) {
            matchUtilities(
                {
                    "bg-grid": (value) => ({
                        backgroundImage: `url("${svgToDataUri(
                            `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="none" stroke="${value}"><path d="M0 .5H31.5V32"/></svg>`
                        )}")`,
                    }),
                    "bg-grid-small": (value) => ({
                        backgroundImage: `url("${svgToDataUri(
                            `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="8" height="8" fill="none" stroke="${value}"><path d="M0 .5H31.5V32"/></svg>`
                        )}")`,
                    }),
                    "bg-dot": (value) => ({
                        backgroundImage: `url("${svgToDataUri(
                            `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="16" height="16" fill="none"><circle fill="${value}" id="pattern-circle" cx="10" cy="10" r="1.6257413380501518"></circle></svg>`
                        )}")`,
                    }),
                },
                { values: flattenColorPalette(theme("backgroundColor")), type: "color" }
            );
        },
    ],
};

function addVariablesForColors({ addBase, theme }) {
    let allColors = flattenColorPalette(theme("colors"));
    let newVars = Object.fromEntries(
        Object.entries(allColors).map(([key, val]) => [`--${key}`, val])
    );

    addBase({
        ":root": newVars,
    });
}

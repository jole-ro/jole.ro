/** @type {import('tailwindcss').Config} */
export default {
    content: ['./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}'],
    theme: {
        extend: {
            colors: {
                primary: '#7e22ce',
                'primary-hover': '#9B50E0',
                secondary: '#ec4899'
            },
            borderRadius: {
                'none': '0px',
                'sm': '4px',
                DEFAULT: '8px',
                'md': '12px',
                'lg': '16px',
                'xl': '20px',
                '2xl': '24px',
                '3xl': '32px',
                'full': '9999px',
                'button': '8px'
            }
        },
        fontFamily: {
            sans: ['Inter', 'sans-serif'],
            pacifico: ['Pacifico', 'cursive'],
        }
    },
    plugins: [],
}

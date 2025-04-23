/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.{js,ts,jsx,tsx}",
    "./resources/**/*.vue",
    "./resources/js/**/*.{js,ts,jsx,tsx,vue}",
  ],
  theme: {
    extend: {
      colors: {
        'primary': {
          50: '#fafafa',
          100: '#f4f4f5',
          200: '#e4e4e7',
          300: '#d4d4d8',
          400: '#a1a1aa',
          500: '#71717a',
          600: '#52525b',
          700: '#3f3f46',
          800: '#27272a',
          900: '#18181b',
          950: '#09090b',
        },
        'accent': {
          100: '#ffffff',
          200: '#fafafa',
          300: '#ededed',
          400: '#d6d6d6',
          500: '#a3a3a3',
          600: '#525252',
          700: '#262626',
          800: '#171717',
          900: '#0a0a0a',
        },
      },
      fontFamily: {
        sans: ['var(--font-sans)'],
      },
    },
  },
  plugins: [],
}
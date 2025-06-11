import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1A237E', // professional deep blue
          light: '#3949AB',
          dark: '#0D133D',
        },
        accent: {
          DEFAULT: '#009688', // muted teal
          light: '#4DB6AC',
          dark: '#00695C',
        },
        secondary: {
          DEFAULT: '#546E7A', // slate gray
          light: '#90A4AE',
          dark: '#29434E',
        },
        success: '#43A047',   // professional green
        warning: '#FFB300',   // amber
        danger: '#D32F2F',    // strong red
        gray: {
          50: '#F8FAFC',
          100: '#F1F5F9',
          200: '#E2E8F0',
          300: '#CBD5E1',
          400: '#94A3B8',
          500: '#64748B',
          600: '#475569',
          700: '#334155',
          800: '#1E293B',
          900: '#0F172A',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [forms],
} 
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
          DEFAULT: '#5B21B6', // deep purple
          light: '#7C3AED',
          dark: '#3C1361',
        },
        accent: {
          DEFAULT: '#06B6D4', // teal
          light: '#67E8F9',
          dark: '#0E7490',
        },
        secondary: {
          DEFAULT: '#2563EB', // blue
          light: '#60A5FA',
          dark: '#1E40AF',
        },
        success: '#22C55E',
        warning: '#F59E42',
        danger: '#EF4444',
        gray: {
          50: '#F9FAFB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          300: '#D1D5DB',
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#4B5563',
          700: '#374151',
          800: '#1F2937',
          900: '#111827',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [forms],
} 
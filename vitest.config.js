import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    globals: true,
    include: ['resources/js/**/*.{test,spec}.{js,ts,jsx,tsx}'],
    coverage: {
      reporter: ['text', 'json', 'html'],
      include: ['resources/js/**/*.{js,ts,jsx,tsx}'],
      exclude: ['resources/js/**/*.{test,spec}.{js,ts,jsx,tsx}']
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js')
    }
  }
}) 
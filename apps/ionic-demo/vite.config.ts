/// <reference types="vitest" />

import path from "node:path";
import { fileURLToPath } from "node:url";
import legacy from '@vitejs/plugin-legacy'
import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

const rootDir = path.dirname(fileURLToPath(import.meta.url));

// https://vitejs.dev/config/
export default defineConfig({
  resolve: {
    alias: {
      "@hybrid/capacitor-hybrid-updater": path.resolve(rootDir, "../../packages/capacitor-hybrid-updater/src/index.ts"),
    },
  },
  plugins: [
    react(),
    legacy()
  ],
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: './src/setupTests.ts',
  }
})

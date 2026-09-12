/**
 * Static SPA build config.
 *
 * Use this to generate a deployable static site:
 *   npm run build:static   →  outputs to dist/
 *
 * The output is a pure client-side SPA:
 *   - No server / Nitro / SSR involved
 *   - Works on any static host (Netlify, Vercel, Cloudflare Pages,
 *     GitHub Pages, shared hosting, XAMPP, Apache, Nginx, etc.)
 *   - All routes resolve through index.html (configure your host
 *     to serve index.html for all 404s so client-side routing works)
 */
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import tsconfigPaths from "vite-tsconfig-paths";

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    tsconfigPaths(),
  ],

  build: {
    outDir: "dist",
    emptyOutDir: true,
    // Increase chunk warning threshold — Three.js is large by nature
    chunkSizeWarningLimit: 2000,
    rollupOptions: {
      output: {
        // Split vendor chunks for better caching
        manualChunks(id) {
          if (id.includes("node_modules/three") || id.includes("@react-three")) {
            return "three";
          }
          if (id.includes("node_modules/motion") || id.includes("node_modules/framer-motion")) {
            return "motion";
          }
          if (id.includes("@radix-ui")) {
            return "radix";
          }
          if (id.includes("@tanstack/react-router")) {
            return "router";
          }
          if (id.includes("@tanstack/react-query")) {
            return "query";
          }
          if (id.includes("node_modules/react/") || id.includes("node_modules/react-dom/")) {
            return "react";
          }
        },
      },
    },
  },

  // Resolve the same @ alias as the main project
  resolve: {
    alias: {
      "@": "/src",
    },
  },
});

/**
 * Static SPA build config — npm run build:static → dist/
 * base: "./" makes all asset paths relative so dist/ works at
 * domain root OR any subfolder (e.g. yourdomain.com/evergreen-vision/)
 */
import path from "path";
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import tsconfigPaths from "vite-tsconfig-paths";
import { TanStackRouterVite } from "@tanstack/router-plugin/vite";

export default defineConfig({
  base: "./",   // ← relative paths: works at root OR any subfolder

  plugins: [
    TanStackRouterVite({
      routesDirectory: "./src/routes",
      generatedRouteTree: "./src/routeTree.gen.ts",
      autoCodeSplitting: true,
    }),
    react(),
    tailwindcss(),
    tsconfigPaths(),
  ],

  build: {
    outDir: "dist",
    emptyOutDir: true,
    assetsDir: "assets",
    rollupOptions: {
      input: "index.html",
      output: {
        manualChunks(id) {
          if (id.includes("node_modules/three") || id.includes("@react-three")) return "three";
          if (id.includes("node_modules/motion") || id.includes("framer-motion")) return "motion";
          if (id.includes("@radix-ui")) return "radix";
          if (id.includes("@tanstack/react-router")) return "router";
          if (id.includes("@tanstack/react-query")) return "query";
          if (id.includes("node_modules/react/") || id.includes("node_modules/react-dom/")) return "react";
        },
      },
    },
    chunkSizeWarningLimit: 2500,
  },

  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
});

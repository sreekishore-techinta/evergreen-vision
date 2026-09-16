// @lovable.dev/vite-tanstack-config already includes the following — do NOT add them manually
// or the app will break with duplicate plugins:
//   - TanStack devtools (dev-only, first), tanstackStart, viteReact, tailwindcss, tsConfigPaths,
//     nitro (build-only using cloudflare as a default target), VITE_* env injection, @ path alias,
//     React/TanStack dedupe, error logger plugins, and sandbox detection (port/host/strictPort).
// You can pass additional config via defineConfig({ vite: { ... }, etc... }) if needed.
import { defineConfig } from "@lovable.dev/vite-tanstack-config";

export default defineConfig({
  tanstackStart: {
    // Redirect TanStack Start's bundled server entry to src/server.ts (our SSR error wrapper).
    // nitro/vite builds from this
    server: { entry: "server" },
  },
  vite: {
    server: {
      proxy: {
        // Forward PHP backend calls from Vite dev port → XAMPP Apache (port 80)
        // Use 127.0.0.1 explicitly to force IPv4 — avoids 502 when Node resolves
        // "localhost" to ::1 (IPv6) but Apache listens only on IPv4.
        "/backend": {
          target: "http://127.0.0.1/evergreen-vision",
          changeOrigin: true,
          secure: false,
        },
        "/uploads": {
          target: "http://127.0.0.1/evergreen-vision",
          changeOrigin: true,
          secure: false,
        },
      },
    },
  },
});

/**
 * SPA entry point — used by vite.static.config.ts (npm run build:static).
 *
 * basepath is injected at build time via VITE_BASE_PATH env variable.
 * For domain-root deploy:    VITE_BASE_PATH=/
 * For subfolder deploy:      VITE_BASE_PATH=/evergreen-vision
 * Default (not set):         / (domain root)
 */
import { StrictMode } from "react";
import ReactDOM from "react-dom/client";
import { RouterProvider, createRouter } from "@tanstack/react-router";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { routeTree } from "./routeTree.gen";
import "./styles.css";

const queryClient = new QueryClient();

// Read base path injected at build time (defaults to "/" for domain-root deploy)
const basepath = (import.meta.env.VITE_BASE_PATH as string | undefined) ?? "/";

const router = createRouter({
  routeTree,
  basepath,
  context: { queryClient },
  scrollRestoration: true,
  defaultPreloadStaleTime: 0,
});

declare module "@tanstack/react-router" {
  interface Register {
    router: typeof router;
  }
}

const rootElement = document.getElementById("root")!;

ReactDOM.createRoot(rootElement).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <RouterProvider router={router} />
    </QueryClientProvider>
  </StrictMode>,
);

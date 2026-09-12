import { createFileRoute, redirect } from "@tanstack/react-router";

export const Route = createFileRoute("/applications")({
  beforeLoad: () => {
    throw redirect({ to: "/certificate" });
  },
  component: () => null,
});

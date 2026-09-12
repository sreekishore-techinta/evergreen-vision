import { createFileRoute, redirect } from "@tanstack/react-router";

export const Route = createFileRoute("/why-evergreen")({
  beforeLoad: () => {
    throw redirect({ to: "/segment" });
  },
  component: () => null,
});

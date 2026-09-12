import { createFileRoute } from "@tanstack/react-router";
import { useEffect } from "react";

export const Route = createFileRoute("/admin")({
  component: AdminRedirect,
});

function AdminRedirect() {
  useEffect(() => {
    // Redirect to the PHP admin panel served by Apache on port 80
    const adminUrl =
      window.location.hostname === "localhost"
        ? "http://localhost/evergreen-vision/admin/"
        : `${window.location.origin}/admin/login.php`;

    window.location.replace(adminUrl);
  }, []);

  return (
    <div
      style={{
        minHeight: "100vh",
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        background: "#0a1f0e",
        color: "#fff",
        fontFamily: "Inter, system-ui, sans-serif",
        gap: "16px",
      }}
    >
      {/* Spinner */}
      <div
        style={{
          width: 48,
          height: 48,
          borderRadius: "50%",
          border: "3px solid rgba(255,255,255,.15)",
          borderTopColor: "#52a05a",
          animation: "spin 0.8s linear infinite",
        }}
      />
      <p style={{ fontSize: ".9rem", opacity: 0.7, margin: 0 }}>
        Redirecting to Admin Panel…
      </p>
      <a
        href="http://localhost/evergreen-vision/admin/"
        style={{
          fontSize: ".78rem",
          color: "#7dc285",
          textDecoration: "underline",
          opacity: 0.8,
        }}
      >
        Click here if not redirected
      </a>
      <style>{`@keyframes spin{to{transform:rotate(360deg)}}`}</style>
    </div>
  );
}

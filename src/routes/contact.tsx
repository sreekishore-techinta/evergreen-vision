import { createFileRoute } from "@tanstack/react-router";
import { CheckCircle2, Mail, MapPin, Phone, Send, type LucideIcon } from "lucide-react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { PageHero, Reveal, SectionTitle, heroImage } from "@/components/site";

export const Route = createFileRoute("/contact")({
  head: () => ({
    meta: [
      { title: "Contact Us | EVERGREENINDUSTRY" },
      { name: "description", content: "Speak with Evergreen Industry about sustainable packaging solutions." },
      { property: "og:title", content: "Contact EVERGREENINDUSTRY" },
      { property: "og:description", content: "Start a conversation about better packaging." },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
  }),
  component: Contact,
});

// Detect live hosting or local — API base auto-detected
const API_BASE =
  typeof window !== "undefined"
    ? (() => {
        const path = window.location.pathname;
        const root = path.includes('/admin')
          ? path.substring(0, path.indexOf('/admin'))
          : path.replace(/\/[^/]*\.[^/]+$/, '').replace(/\/$/, '');
        return `${window.location.origin}${root}/backend/api`;
      })()
    : "/backend/api";

type FormState = "idle" | "loading" | "success" | "error";

function Contact() {
  const [state, setState]     = useState<FormState>("idle");
  const [message, setMessage] = useState("");

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setState("loading");
    setMessage("");

    const form    = e.currentTarget;
    const data    = new FormData(form);
    const payload = {
      name:    data.get("name")    as string,
      company: data.get("company") as string,
      email:   data.get("email")   as string,
      phone:   data.get("phone")   as string,
      message: data.get("message") as string,
    };

    try {
      const res  = await fetch(`${API_BASE}/enquiries.php?action=submit`, {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify(payload),
      });
      const json = await res.json();

      if (json.success) {
        setState("success");
        setMessage(json.message || "Enquiry submitted! We'll be in touch soon.");
        form.reset();
      } else {
        setState("error");
        setMessage(json.message || "Something went wrong. Please try again.");
      }
    } catch {
      setState("error");
      setMessage("Network error. Please check your connection and try again.");
    }
  }

  return (
    <>
      <PageHero
        eyebrow="Contact us"
        title="Let's shape a better package."
        copy="Tell us what your business needs. We'll help you explore a more responsible way forward."
        image={heroImage}
      />

      <section className="px-5 py-24 lg:px-10 lg:py-32">
        <div className="mx-auto grid max-w-[1350px] gap-16 lg:grid-cols-[.8fr_1.2fr]">

          {/* Left — contact info */}
          <div>
            <SectionTitle eyebrow="Start a conversation" title="Practical needs. Thoughtful answers." />
            <div className="mt-12 space-y-6 text-sm">
              {(
                [
                  [Mail,    "hello@evergreenindustry.com"],
                  [Phone,   "+91 00000 00000"],
                  [MapPin,  "India · Serving businesses globally"],
                ] as Array<[LucideIcon, string]>
              ).map(([Icon, text]) => (
                <div key={text} className="flex items-center gap-4">
                  <span className="grid size-11 place-items-center rounded-full bg-secondary text-moss">
                    <Icon />
                  </span>
                  <span>{text}</span>
                </div>
              ))}
            </div>
            <p className="mt-8 text-xs leading-5 text-muted-foreground">
              Contact details shown are placeholders until official company information is provided.
            </p>
          </div>

          {/* Right — form */}
          <Reveal>
            <form
              className="rounded-[2rem] bg-secondary p-7 sm:p-10"
              onSubmit={handleSubmit}
              noValidate
            >
              {/* Success state */}
              {state === "success" && (
                <div className="mb-6 flex items-start gap-3 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-800">
                  <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-green-600" />
                  <span>{message}</span>
                </div>
              )}

              {/* Error state */}
              {state === "error" && (
                <div className="mb-6 flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700">
                  <span className="mt-0.5 text-red-500">⚠</span>
                  <span>{message}</span>
                </div>
              )}

              <div className="grid gap-6 sm:grid-cols-2">
                <label className="text-sm font-semibold">
                  Name <span className="text-red-500">*</span>
                  <input
                    name="name"
                    required
                    disabled={state === "loading" || state === "success"}
                    className="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                    placeholder="Your full name"
                  />
                </label>

                <label className="text-sm font-semibold">
                  Company
                  <input
                    name="company"
                    disabled={state === "loading" || state === "success"}
                    className="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                    placeholder="Company name (optional)"
                  />
                </label>

                <label className="text-sm font-semibold sm:col-span-2">
                  Email <span className="text-red-500">*</span>
                  <input
                    name="email"
                    type="email"
                    required
                    disabled={state === "loading" || state === "success"}
                    className="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                    placeholder="you@company.com"
                  />
                </label>

                <label className="text-sm font-semibold sm:col-span-2">
                  Phone
                  <input
                    name="phone"
                    type="tel"
                    disabled={state === "loading" || state === "success"}
                    className="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                    placeholder="+91 00000 00000 (optional)"
                  />
                </label>

                <label className="text-sm font-semibold sm:col-span-2">
                  What can we help with? <span className="text-red-500">*</span>
                  <textarea
                    name="message"
                    required
                    rows={6}
                    disabled={state === "loading" || state === "success"}
                    className="mt-2 w-full rounded-xl border border-border bg-background p-4 outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                    placeholder="Tell us about your packaging requirements, volume, timelines…"
                  />
                </label>
              </div>

              <Button
                type="submit"
                size="lg"
                className="mt-7"
                disabled={state === "loading" || state === "success"}
              >
                {state === "loading" ? (
                  <>
                    <span className="inline-block size-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                    Sending…
                  </>
                ) : state === "success" ? (
                  <>
                    <CheckCircle2 className="size-4" />
                    Sent!
                  </>
                ) : (
                  <>
                    Send enquiry <Send />
                  </>
                )}
              </Button>

              {state === "success" && (
                <button
                  type="button"
                  onClick={() => { setState("idle"); setMessage(""); }}
                  className="ml-4 text-sm text-muted-foreground underline underline-offset-4 hover:text-foreground"
                >
                  Send another
                </button>
              )}
            </form>
          </Reveal>
        </div>
      </section>

      {/* Map placeholder */}
      <section className="bg-forest px-5 py-20 text-cream lg:px-10">
        <div className="mx-auto grid min-h-96 max-w-[1350px] place-items-center rounded-[2rem] border border-cream/20 bg-cream/5 text-center">
          <div>
            <MapPin className="mx-auto mb-5 size-9 text-sage" />
            <h2 className="text-4xl">Location map</h2>
            <p className="mt-3 text-sm text-cream/60">Google Maps placeholder · Official location to be confirmed</p>
          </div>
        </div>
      </section>
    </>
  );
}

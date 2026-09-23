import { createFileRoute } from "@tanstack/react-router";
import { ArrowUpRight, CheckCircle2, Mail, MapPin, Phone, Send } from "lucide-react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { ContactBannerStrip, PageHero, Reveal, SectionTitle, heroImage } from "@/components/site";

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

      {/* ── Brand Banner Strip ── */}
      <section className="-mt-8 px-5 lg:px-10 relative z-20">
        <div className="mx-auto max-w-[1350px]">
          <ContactBannerStrip />
        </div>
      </section>

      <section className="px-5 py-20 lg:px-10 lg:py-28">
        <div className="mx-auto grid max-w-[1350px] gap-16 lg:grid-cols-[.9fr_1.1fr]">

          {/* Left — contact info */}
          <div>
            <SectionTitle eyebrow="Start a conversation" title="Direct contact. Quick responses." />

            <div className="mt-10 space-y-5">
              {/* Phone Card */}
              <div className="flex items-start gap-4 rounded-2xl border border-border/80 bg-secondary/50 p-5 transition-all hover:bg-secondary">
                <a
                  href="tel:+917339285437"
                  className="grid size-12 shrink-0 place-items-center rounded-full bg-[#fbb016] text-[#071f11] shadow-md transition-transform hover:scale-105"
                  aria-label="Call +91 73392 85437"
                >
                  <Phone className="size-5 fill-current" />
                </a>
                <div className="min-w-0">
                  <p className="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Direct Phone Line</p>
                  <a
                    href="tel:+917339285437"
                    className="mt-0.5 text-xl sm:text-2xl font-black text-foreground hover:text-emerald-700 transition-colors block"
                  >
                    73392 85437
                  </a>
                  <p className="text-xs text-muted-foreground font-medium">Call Now for More Details</p>
                  <div className="mt-2.5 flex items-center gap-3">
                    <a
                      href="tel:+917339285437"
                      className="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800"
                    >
                      Call Now <ArrowUpRight className="size-3" />
                    </a>
                    <span className="text-border">·</span>
                    <a
                      href="https://wa.me/917339285437"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex items-center gap-1.5 text-xs font-bold text-green-700 hover:text-green-800"
                    >
                      WhatsApp <ArrowUpRight className="size-3" />
                    </a>
                  </div>
                </div>
              </div>

              {/* Address Card */}
              <div className="flex items-start gap-4 rounded-2xl border border-border/80 bg-secondary/50 p-5 transition-all hover:bg-secondary">
                <div className="grid size-12 shrink-0 place-items-center rounded-full bg-forest text-cream shadow-sm">
                  <MapPin className="size-5 text-emerald-400" />
                </div>
                <div className="min-w-0">
                  <p className="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Factory &amp; Registered Office</p>
                  <div className="mt-1 text-sm font-semibold text-foreground leading-relaxed">
                    <p>NO. 2, THOLILPETTAI,</p>
                    <p>SIDCO INDUSTRIAL ESTATE, N.K. ROAD,</p>
                    <p className="font-extrabold text-emerald-800">THANJAVUR (613006), TAMIL NADU</p>
                  </div>
                  <a
                    href="https://maps.google.com/?q=SIDCO+Industrial+Estate,+N.K.+Road,+Thanjavur+613006"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="mt-2.5 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800"
                  >
                    View on Google Maps <ArrowUpRight className="size-3" />
                  </a>
                </div>
              </div>

              {/* Email Card */}
              <div className="flex items-start gap-4 rounded-2xl border border-border/80 bg-secondary/50 p-5 transition-all hover:bg-secondary">
                <div className="grid size-12 shrink-0 place-items-center rounded-full bg-forest text-cream shadow-sm">
                  <Mail className="size-5 text-emerald-400" />
                </div>
                <div className="min-w-0">
                  <p className="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Email Inquiries</p>
                  <a
                    href="mailto:info@evergreenindustry.com"
                    className="mt-0.5 text-base font-bold text-foreground hover:text-emerald-700 transition-colors block"
                  >
                    info@evergreenindustry.com
                  </a>
                  <p className="text-xs text-muted-foreground">General, sales and export inquiries</p>
                </div>
              </div>
            </div>

            <div className="mt-8 rounded-xl bg-emerald-50 border border-emerald-200/80 p-4 text-xs leading-5 text-emerald-900">
              <strong>Order &amp; Sample Assistance:</strong> Reach out via phone or WhatsApp at <strong>+91 73392 85437</strong> for rapid quotations, product specifications, and custom branding samples.
            </div>
          </div>

          {/* Right — form */}
          <Reveal>
            <form
              className="rounded-[2rem] bg-secondary p-7 sm:p-10 shadow-sm border border-border/60"
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
                    placeholder="+91 73392 85437 (optional)"
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

      {/* Interactive Location Map */}
      <section className="bg-forest px-5 py-20 text-cream lg:px-10">
        <div className="mx-auto max-w-[1350px]">
          <div className="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
              <p className="text-xs font-bold uppercase tracking-[0.2em] text-sage">Location Map</p>
              <h2 className="text-3xl sm:text-4xl font-extrabold text-white mt-1">Our Thanjavur Facility</h2>
              <p className="mt-2 text-sm text-cream/70 max-w-xl">
                No. 2, Tholilpettai, SIDCO Industrial Estate, N.K. Road, Thanjavur (613006), Tamil Nadu
              </p>
            </div>
            <a
              href="https://www.google.com/maps/dir/?api=1&destination=SIDCO+Industrial+Estate,+N.K.+Road,+Thanjavur+613006"
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 self-start rounded-full bg-emerald-500 px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#051c0d] shadow-lg transition hover:bg-[#fbb016] hover:text-[#051c0d]"
            >
              Get Directions <ArrowUpRight className="size-4" />
            </a>
          </div>

          <div className="relative overflow-hidden rounded-[2rem] border border-cream/20 bg-cream/5 shadow-2xl h-[420px]">
            <iframe
              title="Evergreen Industries Location Map - Thanjavur"
              src="https://maps.google.com/maps?q=SIDCO+Industrial+Estate,+N.K.+Road,+Thanjavur+613006&t=&z=15&ie=UTF8&iwloc=&output=embed"
              className="w-full h-full border-0"
              loading="lazy"
              referrerPolicy="no-referrer-when-downgrade"
            />
          </div>
        </div>
      </section>
    </>
  );
}

import { Link, useRouterState } from "@tanstack/react-router";
import { AnimatePresence, motion, useScroll, useTransform } from "motion/react";
import {
  ArrowRight,
  Facebook,
  Instagram,
  Linkedin,
  Mail,
  MapPin,
  Menu,
  Phone,
  X,
  Youtube,
} from "lucide-react";
import { useState, type ReactNode } from "react";
import { Button } from "@/components/ui/button";
import heroImage from "@/assets/evergreen-hero.jpg";
import productsImage from "@/assets/product-collection.jpg";
import manufacturingImage from "@/assets/manufacturing.jpg";
import materialsImage from "@/assets/material-journey.jpg";
import ctaImage from "@/assets/cta-bags-showcase.jpg";
import logoImg from "@/assets/e-logo.png";

export { heroImage, productsImage, manufacturingImage, materialsImage, ctaImage, logoImg };

/* ─── Navigation links ──────────────────────────────────────────────────── */
const nav = [
  ["/", "Home"],
  ["/about", "About"],
  ["/products", "Products"],
  ["/segment", "Solutions"],
  ["/sustainability", "Sustainability"],
  ["/segment", "Why Us"],
  ["/contact", "Contact"],
] as const;

/* ─── SITE HEADER — full-width flat navbar ─────────────────────────────── */
export function SiteHeader() {
  const [open, setOpen] = useState(false);
  const { scrollY } = useScroll();
  const navBg = useTransform(
    scrollY,
    [0, 60],
    ["rgba(255,255,255,0.92)", "rgba(255,255,255,1)"]
  );
  const navShadow = useTransform(
    scrollY,
    [0, 60],
    [
      "0 1px 0px rgba(30,70,40,0.08)",
      "0 2px 20px rgba(30,70,40,0.10)",
    ]
  );

  return (
    <header className="fixed inset-x-0 top-0 z-50">
      {/* Desktop — full-width flat bar */}
      <motion.div
        style={{ backgroundColor: navBg, boxShadow: navShadow }}
        className="hidden lg:flex h-[76px] w-full items-center justify-between px-8 xl:px-16 backdrop-blur-md border-b border-[#e0ede0]"
      >
        {/* Logo — icon mark + brand name in HTML for clarity at small navbar height */}
        <Link to="/" className="flex items-center gap-2.5 shrink-0">
          {/* Show only the icon portion of the logo at a clear, large size */}
          <img
            src={logoImg}
            alt="Evergreen Industries"
            className="h-[52px] w-auto object-contain"
            style={{ maxWidth: "52px" }}
          />
          <div className="leading-[1.15]">
            <p className="text-[15px] font-extrabold tracking-[0.06em] text-[#0f2718]">EVERGREEN</p>
            <p className="text-[11px] font-semibold tracking-[0.18em] text-[#2e7d42]">INDUSTRIES</p>
          </div>
        </Link>

        {/* Center nav */}
        <NavLinks />

        {/* CTA */}
        <Link
          to="/contact"
          className="shrink-0 inline-flex items-center gap-2 rounded-full bg-[#1e5c2e] px-6 py-2.5 text-sm font-semibold text-white transition-all duration-300 hover:bg-[#174d26] hover:shadow-lg hover:-translate-y-0.5"
        >
          Get a Quote <ArrowRight className="size-4" />
        </Link>
      </motion.div>

      {/* Mobile — full-width flat bar */}
      <div className="lg:hidden">
        <motion.div
          style={{ backgroundColor: navBg, boxShadow: navShadow }}
          className="flex h-16 w-full items-center justify-between px-4 backdrop-blur-md border-b border-[#e0ede0]"
        >
          <Link to="/" className="flex items-center gap-2 shrink-0" onClick={() => setOpen(false)}>
            <img
              src={logoImg}
              alt="Evergreen Industries"
              className="h-[42px] w-auto object-contain"
              style={{ maxWidth: "42px" }}
            />
            <div className="leading-[1.15]">
              <p className="text-[13px] font-extrabold tracking-[0.06em] text-[#0f2718]">EVERGREEN</p>
              <p className="text-[10px] font-semibold tracking-[0.16em] text-[#2e7d42]">INDUSTRIES</p>
            </div>
          </Link>
          <button
            className="grid size-9 place-items-center rounded-lg border border-[#d8e8d8] text-[#1a3d22] transition hover:bg-[#f0f7f0]"
            onClick={() => setOpen((v) => !v)}
            aria-label="Toggle navigation"
          >
            {open ? <X className="size-5" /> : <Menu className="size-5" />}
          </button>
        </motion.div>

        <AnimatePresence>
          {open && (
            <motion.nav
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: "auto", opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              transition={{ duration: 0.22 }}
              className="overflow-hidden bg-white border-b border-[#d8e8d8] shadow-lg"
            >
              <div className="flex flex-col px-4 py-3 gap-0.5">
                {nav.map(([to, label]) => (
                  <Link
                    key={`${to}-${label}`}
                    to={to}
                    onClick={() => setOpen(false)}
                    className="rounded-lg px-4 py-3 text-sm font-medium text-[#1a3d22] transition hover:bg-[#f0f7f0]"
                    activeProps={{ className: "rounded-lg px-4 py-3 text-sm font-semibold text-[#1e5c2e] bg-[#f0f7f0]" }}
                  >
                    {label}
                  </Link>
                ))}
                <Link
                  to="/contact"
                  onClick={() => setOpen(false)}
                  className="mt-2 rounded-full bg-[#1e5c2e] px-5 py-3 text-center text-sm font-semibold text-white"
                >
                  Get a Quote
                </Link>
              </div>
            </motion.nav>
          )}
        </AnimatePresence>
      </div>
    </header>
  );
}

function NavLinks() {
  return (
    <nav className="flex items-center gap-1">
      {nav.map(([to, label]) => (
        <Link
          key={`${to}-${label}`}
          to={to}
          className="px-4 py-2 text-[13px] font-medium text-[#3a5c42] transition-all duration-200 hover:text-[#1e5c2e]"
          activeProps={{
            className: "px-4 py-2 text-[13px] font-semibold text-[#1e5c2e] border-b-2 border-[#1e5c2e]",
          }}
        >
          {label}
        </Link>
      ))}
    </nav>
  );
}

/* ─── SITE FOOTER — reference-style 4-col dark footer ──────────────────── */
export function SiteFooter() {
  return (
    <footer className="bg-[#0f2718] text-white">
      {/* Main footer grid */}
      <div className="mx-auto max-w-[1420px] px-6 pt-14 pb-10 lg:px-10">
        <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-[1.6fr_1fr_1.2fr_1fr]">

          {/* Col 1 — Brand */}
          <div>
            <div className="mb-4">
              {/* On dark footer: white-filter makes the green logo visible as white */}
              <img
                src={logoImg}
                alt="Evergreen Industries"
                className="h-14 w-auto object-contain brightness-0 invert"
              />
            </div>
            <p className="text-sm leading-7 text-white/55 max-w-[260px]">
              Sustainable packaging for a better tomorrow. Premium biodegradable &amp; compostable solutions for responsible businesses.
            </p>
            {/* Social icons */}
            <div className="mt-5 flex items-center gap-3">
              {[
                { icon: Facebook, href: "#", label: "Facebook" },
                { icon: Instagram, href: "#", label: "Instagram" },
                { icon: Linkedin, href: "#", label: "LinkedIn" },
                { icon: Youtube, href: "#", label: "YouTube" },
              ].map(({ icon: Icon, href, label }) => (
                <a
                  key={label}
                  href={href}
                  aria-label={label}
                  className="grid size-8 place-items-center rounded-full border border-white/15 text-white/50 transition hover:border-[#4a9a5a] hover:text-[#4a9a5a]"
                >
                  <Icon className="size-3.5" />
                </a>
              ))}
            </div>
          </div>

          {/* Col 2 — Quick Links */}
          <div>
            <p className="mb-4 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7ab87a]">
              Quick Links
            </p>
            <div className="flex flex-col gap-2.5">
              {(
              [
                ["/", "Home"],
                ["/about", "About Us"],
                ["/products", "Products"],
                ["/segment", "Solutions"],
                ["/contact", "Contact"],
              ] as const
            ).map(([to, label]) => (
              <Link
                key={to + label}
                to={to}
                className="text-sm text-white/60 transition hover:text-white"
              >
                {label}
              </Link>
            ))}
            </div>
          </div>

          {/* Col 3 — Our Products */}
          <div>
            <p className="mb-4 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7ab87a]">
              Our Products
            </p>
            <div className="flex flex-col gap-2.5">
              {[
                "Bio Carry Bags",
                "Shopping Bags",
                "T-Shirt Bags",
                "Compostable Waste Bags",
                "Produce & Breathable Pouches",
                "Biopolymer Granules",
              ].map((name) => (
                <Link
                  key={name}
                  to="/products"
                  className="text-sm text-white/60 transition hover:text-white"
                >
                  {name}
                </Link>
              ))}
            </div>
          </div>

          {/* Col 4 — Contact */}
          <div>
            <p className="mb-4 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7ab87a]">
              Contact Us
            </p>
            <div className="flex flex-col gap-3.5">
              <a
                href="tel:+919003062093"
                className="flex items-start gap-2.5 text-sm text-white/60 transition hover:text-white"
              >
                <Phone className="mt-0.5 size-4 shrink-0 text-[#4a9a5a]" />
                +91 90030 62093
              </a>
              <a
                href="mailto:info@evergreenindustry.com"
                className="flex items-start gap-2.5 text-sm text-white/60 transition hover:text-white"
              >
                <Mail className="mt-0.5 size-4 shrink-0 text-[#4a9a5a]" />
                info@evergreenindustry.com
              </a>
              <div className="flex items-start gap-2.5 text-sm text-white/60">
                <MapPin className="mt-0.5 size-4 shrink-0 text-[#4a9a5a]" />
                Coimbatore, Tamil Nadu, India
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div className="border-t border-white/10">
        <div className="mx-auto flex max-w-[1420px] flex-wrap items-center justify-between gap-3 px-6 py-4 lg:px-10">
          <p className="text-xs text-white/35">
            © 2026 Evergreen Industry. All Rights Reserved.
          </p>
          <div className="flex items-center gap-5">
            <a href="#" className="text-xs text-white/35 transition hover:text-white/70">
              Privacy Policy
            </a>
            <a href="#" className="text-xs text-white/35 transition hover:text-white/70">
              Terms &amp; Conditions
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
}

/* ─── Page transition ───────────────────────────────────────────────────── */
export function PageTransition({ children }: { children: ReactNode }) {
  const pathname = useRouterState({ select: (s) => s.location.pathname });
  return (
    <motion.main key={pathname} initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.45 }}>
      {children}
    </motion.main>
  );
}

/* ─── Scroll-reveal wrapper ─────────────────────────────────────────────── */
export function Reveal({
  children,
  className = "",
  delay = 0,
}: {
  children: ReactNode;
  className?: string;
  delay?: number;
}) {
  return (
    <motion.div
      className={className}
      initial={{ opacity: 0, y: 28 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-60px" }}
      transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
    >
      {children}
    </motion.div>
  );
}

/* ─── Eyebrow label ─────────────────────────────────────────────────────── */
export function Eyebrow({ children, light = false }: { children: ReactNode; light?: boolean }) {
  return (
    <p
      className={`mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.22em] ${
        light ? "text-[#7ab87a]" : "text-[#2e7d42]"
      }`}
    >
      <span className={`inline-block h-2.5 w-2.5 rounded-full ${light ? "bg-[#7ab87a]" : "bg-[#2e7d42]"}`} />
      {children}
    </p>
  );
}

/* ─── Section Title ─────────────────────────────────────────────────────── */
export function SectionTitle({
  eyebrow,
  title,
  copy,
  light = false,
}: {
  eyebrow: string;
  title: string;
  copy?: string;
  light?: boolean;
}) {
  return (
    <Reveal className="max-w-2xl">
      <Eyebrow light={light}>{eyebrow}</Eyebrow>
      <h2
        className={`text-[2.2rem] leading-[1.1] sm:text-5xl ${
          light ? "text-white" : "text-[#0f2718]"
        }`}
      >
        {title}
      </h2>
      {copy && (
        <p className={`mt-4 text-sm leading-7 ${light ? "text-white/60" : "text-[#5a7060]"}`}>
          {copy}
        </p>
      )}
    </Reveal>
  );
}

/* ─── Page Hero (used by inner pages) ─────────────────────────────────── */
export function PageHero({
  eyebrow,
  title,
  copy,
  image = heroImage,
}: {
  eyebrow: string;
  title: string;
  copy: string;
  image?: string;
}) {
  return (
    <section className="relative min-h-[72vh] overflow-hidden bg-[#0f2718] text-white">
      <img
        src={image}
        alt="Sustainable packaging by Evergreen Industry"
        width={1920}
        height={1280}
        className="absolute inset-0 h-full w-full object-cover opacity-50 cinematic-zoom"
      />
      <div className="absolute inset-0 bg-gradient-to-r from-[#0f2718] via-[#0f2718]/70 to-transparent" />
      <div className="relative mx-auto flex min-h-[72vh] max-w-[1420px] items-end px-6 pb-20 pt-40 lg:px-10 lg:pb-24">
        <Reveal className="max-w-3xl">
          <Eyebrow light>{eyebrow}</Eyebrow>
          <h1 className="text-5xl leading-[1.05] sm:text-7xl lg:text-[5.5rem]">{title}</h1>
          <p className="mt-6 max-w-2xl text-base leading-7 text-white/70 sm:text-lg">{copy}</p>
        </Reveal>
      </div>
    </section>
  );
}

/* ─── Final CTA (used on inner pages) ──────────────────────────────────── */
export function FinalCTA() {
  return (
    <section className="bg-[#1e5c2e] px-6 py-20 text-white lg:px-10">
      <Reveal className="mx-auto flex max-w-[1250px] flex-col items-start justify-between gap-8 lg:flex-row lg:items-end">
        <div>
          <Eyebrow light>Move forward, responsibly</Eyebrow>
          <h2 className="max-w-2xl text-4xl leading-tight sm:text-6xl">
            Make your next bag a better one.
          </h2>
        </div>
        <Link
          to="/contact"
          className="shrink-0 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-[#1e5c2e] transition hover:bg-white/90 hover:-translate-y-0.5 hover:shadow-lg"
        >
          Talk to our team <ArrowRight className="size-4" />
        </Link>
      </Reveal>
    </section>
  );
}

/* ─── ImagePanel ────────────────────────────────────────────────────────── */
export function ImagePanel({
  src,
  alt,
  className = "",
}: {
  src: string;
  alt: string;
  className?: string;
}) {
  return (
    <div className={`group overflow-hidden rounded-3xl ${className}`}>
      <img
        src={src}
        alt={alt}
        loading="lazy"
        width={1600}
        height={1200}
        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
      />
    </div>
  );
}

/* ─── Metric ────────────────────────────────────────────────────────────── */
export function Metric({ value, label, light = false }: { value: string; label: string; light?: boolean }) {
  return (
    <Reveal>
      <p className={`font-display text-5xl sm:text-6xl ${light ? "text-white" : "text-[#1e5c2e]"}`}>
        {value}
      </p>
      <p className={`mt-2 text-sm ${light ? "text-white/60" : "text-[#5a7060]"}`}>{label}</p>
    </Reveal>
  );
}

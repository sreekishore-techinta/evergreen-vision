import { createFileRoute, Link } from "@tanstack/react-router";
import { motion, useScroll, useTransform, useInView } from "motion/react";
import {
  ArrowDownRight,
  ArrowRight,
  ArrowUpRight,
  CheckCircle2,
  Feather,
  Layers,
  Leaf,
  MoveDown,
  Recycle,
  ShieldCheck,
  Sparkles,
  Wind,
} from "lucide-react";
import React, { useRef, useState, useEffect } from "react";
import { Hero3DCanvas } from "@/components/Hero3DCanvas";
import { Button } from "@/components/ui/button";

import heroBg from "@/assets/hero-premium-bg.jpg";
import productsImg from "@/assets/product-collection.jpg";
import manufacturingImg from "@/assets/manufacturing.jpg";
import materialJourneyImg from "@/assets/material-journey.jpg";
import compostableBagsImg from "@/assets/compostable-bags-blank.jpg";
import biopolymerGranulesImg from "@/assets/biopolymer-granules.jpg";
import ecoLifestyleImg from "@/assets/eco-lifestyle-bag.jpg";
import wasteRollBagsImg from "@/assets/compostable-waste-bags.jpg";
import producePouchesImg from "@/assets/breathable-produce-pouches.jpg";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: "EVERGREENINDUSTRY — Premium Circular Packaging & Biopolymers" },
      {
        name: "description",
        content:
          "Award-winning sustainable packaging, biodegradable carry bags, and compostable solutions engineered for the world's most responsible brands.",
      },
      { property: "og:title", content: "EVERGREENINDUSTRY — Better Packaging. A Greener Tomorrow." },
      {
        property: "og:description",
        content:
          "Luxury eco packaging crafted from renewable cassava and corn biopolymers. 100% compostable, zero microplastics.",
      },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
  }),
  component: HomePage,
});

/* -------------------------------------------------------------------------- */
/*                                ANIMATED STAT                               */
/* -------------------------------------------------------------------------- */
function AnimatedCounter({ target, suffix = "", prefix = "" }: { target: number; suffix?: string; prefix?: string }) {
  const [count, setCount] = useState(0);
  const ref = useRef<HTMLSpanElement>(null);
  const isInView = useInView(ref, { once: true, margin: "-50px" });

  useEffect(() => {
    if (!isInView) return;
    let start = 0;
    const duration = 1800;
    const steps = 40;
    const stepTime = duration / steps;
    const increment = target / steps;

    const timer = setInterval(() => {
      start += increment;
      if (start >= target) {
        setCount(target);
        clearInterval(timer);
      } else {
        setCount(Math.floor(start));
      }
    }, stepTime);

    return () => clearInterval(timer);
  }, [isInView, target]);

  return (
    <span ref={ref} className="tabular-nums">
      {prefix}
      {count}
      {suffix}
    </span>
  );
}

/* -------------------------------------------------------------------------- */
/*                                MAIN HOMEPAGE                               */
/* -------------------------------------------------------------------------- */
function HomePage() {
  const heroRef = useRef<HTMLDivElement>(null);
  const { scrollYProgress } = useScroll({
    target: heroRef,
    offset: ["start start", "end start"],
  });

  const heroParallax = useTransform(scrollYProgress, [0, 1], [0, 180]);
  const heroOpacity = useTransform(scrollYProgress, [0, 0.8], [1, 0]);

  return (
    <div className="relative min-h-screen bg-[#eef4ee] text-[#162a1c] selection:bg-[#7da482] selection:text-[#2d5016] font-sans overflow-x-hidden">
      {/* ==================================================================== */}
      {/* HERO SECTION: CINEMATIC 3D PRODUCT STORYTELLING                     */}
      {/* ==================================================================== */}
      <section
        ref={heroRef}
        className="relative min-h-screen w-full overflow-hidden flex flex-col justify-center pt-24 pb-16"
        style={{ background: "#0d1f14" }}
      >
        {/* ── BACKGROUND: Natural botanical image, photographic depth, NO green wash ── */}
        <div className="absolute inset-0 pointer-events-none overflow-hidden">
          <motion.img
            style={{ y: heroParallax, opacity: 1, filter: "brightness(0.72) saturate(0.92)" }}
            src={heroBg}
            alt="Lush cinematic tropical botanical forest — cassava leaves and bamboo with golden god-rays"
            className="absolute inset-0 w-full h-[115%] object-cover object-center"
          />
          {/* ONLY a minimal cinematic gradient for left-side text legibility — NOT a green wash */}
          <div
            className="absolute inset-0"
            style={{
              background:
                "linear-gradient(105deg, rgba(10,22,14,0.88) 0%, rgba(10,22,14,0.62) 38%, rgba(10,22,14,0.18) 62%, rgba(10,22,14,0.05) 100%)",
            }}
          />
          {/* Subtle bottom vignette for grounding */}
          <div
            className="absolute bottom-0 left-0 right-0 h-40"
            style={{
              background:
                "linear-gradient(to top, rgba(10,22,14,0.72) 0%, transparent 100%)",
            }}
          />
          {/* Very subtle atmospheric warm-green glow top-right — purely atmospheric, not a wash */}
          <div
            className="absolute top-0 right-0 w-[55%] h-[60%] pointer-events-none"
            style={{
              background:
                "radial-gradient(ellipse at 80% 20%, rgba(100,160,100,0.07) 0%, transparent 65%)",
            }}
          />
        </div>

        {/* ── 3D CANVAS: Bag + Minimal Leaves + Particles (z-10, pointer-events-none) ── */}
        <Hero3DCanvas />

        {/* ── HERO CONTENT LAYOUT ── */}
        <motion.div
          style={{ opacity: heroOpacity }}
          className="relative z-20 mx-auto w-full max-w-[1480px] px-6 sm:px-10 lg:px-16 my-auto py-10 lg:py-14 grid lg:grid-cols-12 gap-10 lg:gap-6 items-center"
        >
          {/* ─── LEFT: Brand messaging ─── */}
          <motion.div
            className="lg:col-span-6 xl:col-span-5 max-w-xl"
            initial={{ opacity: 0, y: 40 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.9, ease: [0.22, 1, 0.36, 1] }}
          >
            {/* Eyebrow label */}
            <motion.div
              className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border mb-7"
              style={{
                borderColor: "rgba(125,164,130,0.35)",
                background: "rgba(10,22,14,0.55)",
                backdropFilter: "blur(12px)",
              }}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.7, delay: 0.15, ease: [0.22, 1, 0.36, 1] }}
            >
              <Sparkles className="size-3" style={{ color: "#9cb99e" }} />
              <span
                className="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.24em]"
                style={{ color: "#c4d9c6" }}
              >
                Next-Generation Biopolymers
              </span>
            </motion.div>

            {/* Headline: two-tone typographic hierarchy */}
            <motion.h1
              className="font-display leading-[1.06] tracking-tight"
              style={{ fontSize: "clamp(2.25rem, 4.5vw, 4rem)" }}
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.85, delay: 0.25, ease: [0.22, 1, 0.36, 1] }}
            >
              <span
                className="block"
                style={{ color: "#f5f0e8", letterSpacing: "-0.01em" }}
              >
                Better Packaging.
              </span>
              <span
                className="font-serif italic font-normal block mt-1.5"
                style={{
                  color: "#9cb99e",
                  letterSpacing: "0em",
                  fontSize: "0.93em",
                }}
              >
                A Greener Tomorrow.
              </span>
            </motion.h1>

            {/* Supporting paragraph */}
            <motion.p
              className="mt-6 leading-relaxed font-light"
              style={{
                fontSize: "clamp(0.875rem, 1.4vw, 1rem)",
                color: "rgba(245,240,232,0.72)",
                maxWidth: "42ch",
              }}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.38, ease: [0.22, 1, 0.36, 1] }}
            >
              Crafted from renewable cassava and corn biopolymers. Engineered with industrial tensile
              resilience to replace conventional plastics without a single trace of microplastics.
            </motion.p>

            {/* CTAs */}
            <motion.div
              className="mt-9 flex flex-wrap items-center gap-3.5"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.52, ease: [0.22, 1, 0.36, 1] }}
            >
              {/* Primary CTA — warm ivory bg, deep forest text */}
              <Button
                asChild
                className="group h-12 px-7 rounded-full font-semibold text-xs uppercase tracking-[0.15em] transition-all duration-350 shadow-lg"
                style={{
                  background: "#f0ebe0",
                  color: "#1a3321",
                  boxShadow: "0 4px 24px rgba(0,0,0,0.28), 0 1px 4px rgba(0,0,0,0.18)",
                }}
                onMouseEnter={e => {
                  (e.currentTarget as HTMLElement).style.background = "#d4e8d5";
                  (e.currentTarget as HTMLElement).style.color = "#0e2617";
                }}
                onMouseLeave={e => {
                  (e.currentTarget as HTMLElement).style.background = "#f0ebe0";
                  (e.currentTarget as HTMLElement).style.color = "#1a3321";
                }}
              >
                <Link to="/products" className="flex items-center gap-2.5">
                  <span>Explore Products</span>
                  <ArrowRight className="size-3.5 transition-transform duration-300 group-hover:translate-x-1" />
                </Link>
              </Button>

              {/* Secondary CTA — transparent, ivory border */}
              <Button
                asChild
                variant="outline"
                className="group h-12 px-6 rounded-full font-medium text-xs uppercase tracking-[0.15em] transition-all duration-300"
                style={{
                  background: "transparent",
                  borderColor: "rgba(245,240,232,0.3)",
                  color: "rgba(245,240,232,0.88)",
                  backdropFilter: "blur(8px)",
                }}
                onMouseEnter={e => {
                  const el = e.currentTarget as HTMLElement;
                  el.style.background = "rgba(245,240,232,0.1)";
                  el.style.borderColor = "rgba(245,240,232,0.55)";
                }}
                onMouseLeave={e => {
                  const el = e.currentTarget as HTMLElement;
                  el.style.background = "transparent";
                  el.style.borderColor = "rgba(245,240,232,0.3)";
                }}
              >
                <Link to="/about" className="flex items-center gap-2">
                  <span>Discover Evergreen</span>
                </Link>
              </Button>
            </motion.div>

            {/* Minimal cert strip */}
            <motion.div
              className="mt-10 flex items-center gap-5"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ duration: 1, delay: 0.72 }}
            >
              {["ASTM D6400", "EN 13432", "Zero Microplastics"].map((cert) => (
                <span
                  key={cert}
                  className="text-[10px] font-mono uppercase tracking-widest"
                  style={{ color: "rgba(156,185,158,0.75)" }}
                >
                  {cert}
                </span>
              ))}
            </motion.div>
          </motion.div>

          {/* ─── RIGHT: Scroll indicator (desktop) ─── */}
          <motion.div
            className="hidden lg:flex lg:col-span-6 xl:col-span-7 flex-col items-end justify-end self-stretch pointer-events-none"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 1.0, delay: 0.6 }}
          >
            {/* Scroll indicator */}
            <motion.div
              className="flex items-center gap-3 mt-auto"
              style={{ color: "rgba(245,240,232,0.45)" }}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ duration: 1, delay: 1.1 }}
            >
              <span className="text-[10px] uppercase tracking-[0.22em] font-mono">Scroll</span>
              <MoveDown className="size-3.5 animate-bounce" style={{ color: "#9cb99e" }} />
            </motion.div>
          </motion.div>
        </motion.div>
      </section>

      {/* ==================================================================== */}
      {/* 1. BRAND STATEMENT: ASYMMETRIC PINTEREST EDITORIAL LAYOUT           */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-40 px-6 sm:px-10 lg:px-16 overflow-hidden bg-[#eef4ee]">
        <div className="mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            {/* Left large typography statement */}
            <div className="lg:col-span-7 space-y-8">
              <div className="flex items-center gap-3 text-xs uppercase tracking-[0.25em] font-bold text-[#355a3f]">
                <span className="size-2 rounded-full bg-[#355a3f]" />
                <span>Our Point of View</span>
              </div>

              <h2 className="font-display text-4xl sm:text-6xl lg:text-7xl leading-[1.04] text-[#0e2617]">
                Made for today.
                <br />
                <span className="font-serif italic font-normal text-[#4a7354]">
                  Mindful of what comes next.
                </span>
              </h2>

              <p className="max-w-2xl text-lg sm:text-xl text-[#3d5042] font-light leading-relaxed">
                Single-use plastics were designed to last centuries for seconds of utility. At EVERGREENINDUSTRY, we reverse
                this paradigm: developing high-performance packaging derived from living botanical starch that carries your
                commerce today, and regenerates the soil tomorrow.
              </p>

              <div className="grid sm:grid-cols-2 gap-6 pt-4 border-t border-[#e2d8c7]">
                <div className="space-y-2">
                  <p className="text-xs font-bold uppercase tracking-wider text-[#162a1c]">Material Purity</p>
                  <p className="text-sm text-[#526456] leading-relaxed">
                    Zero petroleum plasticizers, zero phthalates, and zero PFAS coatings. Completely food-contact safe.
                  </p>
                </div>
                <div className="space-y-2">
                  <p className="text-xs font-bold uppercase tracking-wider text-[#162a1c]">Commercial Feasibility</p>
                  <p className="text-sm text-[#526456] leading-relaxed">
                    Drop-in compatibility with retail checkouts, automated bag dispensers, and commercial heat-sealers.
                  </p>
                </div>
              </div>
            </div>

            {/* Right overlapping Pinterest composition */}
            <div className="lg:col-span-5 relative">
              <div className="relative mx-auto max-w-md lg:max-w-none">
                {/* Main photographic panel: Granules & leaves macro */}
                <div className="relative rounded-[2.5rem] overflow-hidden shadow-2xl shadow-forest/15 border border-[#c8dac8] bg-[#d8ebd8] aspect-[4/5]">
                  <img
                    src={biopolymerGranulesImg}
                    alt="Natural cornstarch and cassava biopolymer pellets"
                    className="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#2d5016]/70 via-transparent to-transparent" />
                  <div className="absolute bottom-6 left-6 right-6 text-[#fbf8f3]">
                    <span className="text-[10px] uppercase font-mono tracking-widest text-[#9cb99e]">
                      FIG. 01 — STARCH PELLET MATRIX
                    </span>
                    <p className="font-display text-xl text-[#fbf8f3] mt-1">
                      Raw Botanical Polymer
                    </p>
                  </div>
                </div>

                {/* Floating overlapping editorial card */}
                <motion.div
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.8, delay: 0.2 }}
                  className="absolute -bottom-8 -left-6 sm:-left-10 bg-[#f0f7f0]/95 backdrop-blur-md p-6 rounded-2xl border border-[#c8dac8] shadow-xl max-w-[240px]"
                >
                  <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-[#4a7354]">
                    Circular Cycle
                  </span>
                  <p className="font-display text-2xl text-[#2d5016] mt-1">100%</p>
                  <p className="text-xs text-[#526456] mt-1 leading-snug">
                    Biodegrades organically into nutrient-rich humus and water.
                  </p>
                </motion.div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 2. PRODUCT WORLD: PINTEREST EDITORIAL VISUAL BOARD                  */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-36 px-6 sm:px-10 lg:px-16 bg-[#e5f0e5] border-y border-[#c8dac8]">
        <div className="mx-auto max-w-[1450px]">
          {/* Section Header */}
          <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
            <div className="space-y-3 max-w-2xl">
              <span className="text-xs uppercase tracking-[0.22em] font-bold text-[#355a3f]">
                Curated Collection • 2026
              </span>
              <h2 className="font-display text-4xl sm:text-6xl text-[#0e2617] leading-tight">
                The Sustainable Collection.
              </h2>
              <p className="text-base sm:text-lg text-[#526456] font-light">
                An editorial showcase of plant-based packaging forms designed around daily commercial interactions.
              </p>
            </div>
            <Button
              asChild
              variant="outline"
              className="rounded-full border-[#162a1c]/30 text-[#162a1c] hover:bg-[#162a1c] hover:text-[#fbf8f3] text-xs font-semibold uppercase tracking-wider px-6 h-11 self-start md:self-auto transition-colors"
            >
              <Link to="/products" className="flex items-center gap-2">
                <span>View Full Catalog</span>
                <ArrowUpRight className="size-4" />
              </Link>
            </Button>
          </div>

          {/* Balanced 2x2 Equal-Sized Editorial Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 items-stretch">
            {/* CARD 1: D-Cut Carry Bags */}
            <div className="group relative flex flex-col h-full rounded-[2rem] overflow-hidden bg-[#d8ebd8] border border-[#b8d4b8] transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
              <div className="aspect-[16/10] overflow-hidden relative bg-[#c8dac8]">
                <img
                  src={compostableBagsImg}
                  alt="D-Cut compostable grocery carry bags"
                  className="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                />
              </div>
              <div className="flex flex-col flex-1 p-7 sm:p-9 lg:p-10 space-y-4 bg-[#f0f7f0]">
                <div className="flex items-center justify-between gap-3">
                  <span className="text-xs font-mono tracking-widest uppercase text-[#5f7e66]">
                    SERIES 01 // CARRY
                  </span>
                  <span className="text-xs font-semibold px-3 py-1 rounded-full bg-[#7da482]/15 text-[#24452e] shrink-0">
                    Commercial High-Tensile
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-3xl text-[#0e2617]">
                  D-Cut Compostable Carry Bag
                </h3>
                <p className="text-sm sm:text-base text-[#526456] leading-relaxed flex-1">
                  Engineered with reinforced punch-out handles and side gussets. Formulated for grocery retailers, apparel
                  boutiques, and department chains requiring smooth touch and tear resistance.
                </p>
                <div className="pt-4 mt-auto flex items-center justify-between border-t border-[#e8dfd3] gap-4">
                  <span className="text-xs font-medium text-[#2d4734] truncate">
                    Cassava & Corn Matrix • 25–40 Micron
                  </span>
                  <Link
                    to="/products"
                    className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#0e2617] group-hover:text-[#355a3f] transition-colors shrink-0"
                  >
                    <span>Explore Specifications</span>
                    <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-1" />
                  </Link>
                </div>
              </div>
            </div>

            {/* CARD 2: Luxury Loop-Handle Tote */}
            <div className="group relative flex flex-col h-full rounded-[2rem] overflow-hidden bg-[#d8ebd8] border border-[#b8d4b8] transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
              <div className="aspect-[16/10] overflow-hidden relative bg-[#c8dac8]">
                <img
                  src={ecoLifestyleImg}
                  alt="Minimalist luxury lifestyle shopping bag"
                  className="w-full h-full object-cover object-[center_35%] transition-transform duration-700 group-hover:scale-105"
                />
              </div>
              <div className="flex flex-col flex-1 p-7 sm:p-9 lg:p-10 space-y-4 bg-[#f0f7f0]">
                <div className="flex items-center justify-between gap-3">
                  <span className="text-xs font-mono tracking-widest uppercase text-[#5f7e66]">
                    SERIES 02 // RETAIL
                  </span>
                  <span className="text-xs font-semibold px-3 py-1 rounded-full bg-[#7da482]/15 text-[#24452e] shrink-0">
                    Loop Handle
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-3xl text-[#0e2617]">
                  Soft-Loop Boutique Tote
                </h3>
                <p className="text-sm sm:text-base text-[#526456] leading-relaxed flex-1">
                  Silky tactile surface texture with ultrasonic welded loop handles. Designed for high-end lifestyle stores,
                  premium apparel, and natural cosmetics with dependable durability.
                </p>
                <div className="pt-4 mt-auto flex items-center justify-between border-t border-[#e8dfd3] gap-4">
                  <span className="text-xs font-medium text-[#2d4734] truncate">
                    Up to 12 kg capacity • Reinforced weld
                  </span>
                  <Link
                    to="/products"
                    className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#0e2617] group-hover:text-[#355a3f] transition-colors shrink-0"
                  >
                    <span>Explore Specifications</span>
                    <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-1" />
                  </Link>
                </div>
              </div>
            </div>

            {/* CARD 3: Waste Management Bin Liners & Heavy Duty Roll */}
            <div className="group relative flex flex-col h-full rounded-[2rem] overflow-hidden bg-[#d8ebd8] border border-[#b8d4b8] transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
              <div className="aspect-[16/10] overflow-hidden relative bg-[#c8dac8]">
                <img
                  src={wasteRollBagsImg}
                  alt="Compostable organic waste roll bags and heavy-duty liners"
                  className="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                />
              </div>
              <div className="flex flex-col flex-1 p-7 sm:p-9 lg:p-10 space-y-4 bg-[#f0f7f0]">
                <div className="flex items-center justify-between gap-3">
                  <span className="text-xs font-mono tracking-widest uppercase text-[#5f7e66]">
                    SERIES 03 // DISPOSAL
                  </span>
                  <span className="text-xs font-semibold px-3 py-1 rounded-full bg-[#355a3f]/10 text-[#24452e] shrink-0">
                    Puncture Proof
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-3xl text-[#0e2617]">
                  Compostable Waste & Roll Bags
                </h3>
                <p className="text-sm sm:text-base text-[#526456] leading-relaxed flex-1">
                  Heavy-gauge perforated roll bags for municipal organic waste sorting, hospitality bin liners, and wet food
                  scraps. Certified to break down alongside organic waste in commercial composting plants.
                </p>
                <div className="pt-4 mt-auto flex items-center justify-between border-t border-[#e8dfd3] gap-4">
                  <span className="text-xs font-medium text-[#2d4734] truncate">
                    Leak-sealed star bottom • Multi-gauge
                  </span>
                  <Link
                    to="/products"
                    className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#0e2617] group-hover:text-[#355a3f] transition-colors shrink-0"
                  >
                    <span>Explore Specifications</span>
                    <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-1" />
                  </Link>
                </div>
              </div>
            </div>

            {/* CARD 4: Food Service & Produce Pouches */}
            <div className="group relative flex flex-col h-full rounded-[2rem] overflow-hidden bg-[#d8ebd8] border border-[#b8d4b8] transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
              <div className="aspect-[16/10] overflow-hidden relative bg-[#c8dac8]">
                <img
                  src={producePouchesImg}
                  alt="Breathable farm and fresh organic produce pouches"
                  className="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                />
              </div>
              <div className="flex flex-col flex-1 p-7 sm:p-9 lg:p-10 space-y-4 bg-[#f0f7f0]">
                <div className="flex items-center justify-between gap-3">
                  <span className="text-xs font-mono tracking-widest uppercase text-[#5f7e66]">
                    SERIES 04 // AGRI & PRODUCE
                  </span>
                  <span className="text-xs font-semibold px-3 py-1 rounded-full bg-[#7da482]/15 text-[#24452e] shrink-0">
                    Breathable Film
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-3xl text-[#0e2617]">
                  Breathable Farm & Produce Pouches
                </h3>
                <p className="text-sm sm:text-base text-[#526456] leading-relaxed flex-1">
                  Engineered with microscopic breathability pores that prolong freshness in farm produce, herbs, and leafy greens
                  while avoiding moisture condensation and preserving crispness.
                </p>
                <div className="pt-4 mt-auto flex items-center justify-between border-t border-[#e8dfd3] gap-4">
                  <span className="text-xs font-medium text-[#2d4734] truncate">
                    Direct Food Contact Certified
                  </span>
                  <Link
                    to="/products"
                    className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#0e2617] group-hover:text-[#355a3f] transition-colors shrink-0"
                  >
                    <span>Explore Specifications</span>
                    <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-1" />
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 3. “ONE MATERIAL. MANY POSSIBILITIES.” SHOWCASE                     */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-36 px-6 sm:px-10 lg:px-16 bg-[#2d5016] text-[#fbf8f3] overflow-hidden">
        {/* Subtle background ambient ring */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] rounded-full border border-cream/5 pointer-events-none" />

        <div className="relative mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 items-center">
            {/* Left Narrative */}
            <div className="lg:col-span-5 space-y-6">
              <span className="text-xs uppercase tracking-[0.25em] font-bold text-[#9cb99e]">
                Material Metamorphosis
              </span>
              <h2 className="font-display text-4xl sm:text-6xl text-[#fbf8f3] leading-tight">
                One material shift.
                <br />
                <span className="font-serif italic font-normal text-[#9cb99e]">
                  Many possibilities.
                </span>
              </h2>
              <p className="text-base sm:text-lg text-[#fbf8f3]/75 font-light leading-relaxed">
                By tuning the molecular architecture of our corn and cassava biopolymer formulations, we achieve varying
                tensile properties—from soft touch luxury retail totes to rugged municipal waste containment.
              </p>

              {/* Technical Property Pills — premium 3D cards */}
              <div className="space-y-4 pt-4">
                {/* Card 1 */}
                <div
                  className="group/card relative flex items-start gap-4 p-5 rounded-2xl
                    bg-gradient-to-br from-white/10 to-white/[0.03]
                    border border-white/20
                    backdrop-blur-md
                    shadow-[0_8px_32px_rgba(0,0,0,0.35),0_2px_8px_rgba(0,0,0,0.25),inset_0_1px_0_rgba(255,255,255,0.12)]
                    transition-all duration-300
                    hover:-translate-y-1
                    hover:shadow-[0_16px_48px_rgba(0,0,0,0.45),0_4px_12px_rgba(0,0,0,0.3),inset_0_1px_0_rgba(255,255,255,0.18)]
                    hover:border-[#9cb99e]/40"
                >
                  {/* Subtle top-edge highlight for 3D bevel */}
                  <div className="absolute inset-x-0 top-0 h-px rounded-t-2xl bg-gradient-to-r from-transparent via-white/25 to-transparent pointer-events-none" />
                  <div className="p-3 rounded-xl bg-gradient-to-br from-[#7da482]/40 to-[#7da482]/10 text-[#9cb99e] mt-0.5 shadow-[0_4px_12px_rgba(125,164,130,0.3),inset_0_1px_0_rgba(255,255,255,0.15)] border border-[#9cb99e]/20 shrink-0">
                    <Feather className="size-5" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-sm text-[#fbf8f3] drop-shadow-sm">Tensile Elasticity & Tear Resistance</h4>
                    <p className="text-xs text-[#fbf8f3]/70 mt-1 leading-relaxed">
                      Surpasses standard HDPE carry resistance under dynamic payload testing.
                    </p>
                  </div>
                </div>

                {/* Card 2 */}
                <div
                  className="group/card relative flex items-start gap-4 p-5 rounded-2xl
                    bg-gradient-to-br from-white/10 to-white/[0.03]
                    border border-white/20
                    backdrop-blur-md
                    shadow-[0_8px_32px_rgba(0,0,0,0.35),0_2px_8px_rgba(0,0,0,0.25),inset_0_1px_0_rgba(255,255,255,0.12)]
                    transition-all duration-300
                    hover:-translate-y-1
                    hover:shadow-[0_16px_48px_rgba(0,0,0,0.45),0_4px_12px_rgba(0,0,0,0.3),inset_0_1px_0_rgba(255,255,255,0.18)]
                    hover:border-[#9cb99e]/40"
                >
                  <div className="absolute inset-x-0 top-0 h-px rounded-t-2xl bg-gradient-to-r from-transparent via-white/25 to-transparent pointer-events-none" />
                  <div className="p-3 rounded-xl bg-gradient-to-br from-[#7da482]/40 to-[#7da482]/10 text-[#9cb99e] mt-0.5 shadow-[0_4px_12px_rgba(125,164,130,0.3),inset_0_1px_0_rgba(255,255,255,0.15)] border border-[#9cb99e]/20 shrink-0">
                    <Wind className="size-5" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-sm text-[#fbf8f3] drop-shadow-sm">Non-Toxic Atmospheric Inks</h4>
                    <p className="text-xs text-[#fbf8f3]/70 mt-1 leading-relaxed">
                      Water-based soy and vegetable printing inks that compost alongside the film.
                    </p>
                  </div>
                </div>

                {/* Card 3 */}
                <div
                  className="group/card relative flex items-start gap-4 p-5 rounded-2xl
                    bg-gradient-to-br from-white/10 to-white/[0.03]
                    border border-white/20
                    backdrop-blur-md
                    shadow-[0_8px_32px_rgba(0,0,0,0.35),0_2px_8px_rgba(0,0,0,0.25),inset_0_1px_0_rgba(255,255,255,0.12)]
                    transition-all duration-300
                    hover:-translate-y-1
                    hover:shadow-[0_16px_48px_rgba(0,0,0,0.45),0_4px_12px_rgba(0,0,0,0.3),inset_0_1px_0_rgba(255,255,255,0.18)]
                    hover:border-[#9cb99e]/40"
                >
                  <div className="absolute inset-x-0 top-0 h-px rounded-t-2xl bg-gradient-to-r from-transparent via-white/25 to-transparent pointer-events-none" />
                  <div className="p-3 rounded-xl bg-gradient-to-br from-[#7da482]/40 to-[#7da482]/10 text-[#9cb99e] mt-0.5 shadow-[0_4px_12px_rgba(125,164,130,0.3),inset_0_1px_0_rgba(255,255,255,0.15)] border border-[#9cb99e]/20 shrink-0">
                    <ShieldCheck className="size-5" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-sm text-[#fbf8f3] drop-shadow-sm">Zero Microplastic Breakdown</h4>
                    <p className="text-xs text-[#fbf8f3]/70 mt-1 leading-relaxed">
                      Degrades into organic nutrients without persistent synthetic fragments.
                    </p>
                  </div>
                </div>
              </div>

              <div className="pt-4">
                <Button
                  asChild
                  className="rounded-full bg-[#7da482] text-[#0c1e13] hover:bg-[#9cb99e] font-semibold text-xs uppercase tracking-wider px-8 h-12"
                >
                  <Link to="/sustainability" className="flex items-center gap-2">
                    <span>Explore Material Science</span>
                    <ArrowRight className="size-4" />
                  </Link>
                </Button>
              </div>
            </div>

            {/* Right Photographic Visual Center with floating spec badges */}
            <div className="lg:col-span-7 relative">
              <div className="relative mx-auto rounded-[2.5rem] overflow-hidden border border-white/20 shadow-[0_32px_80px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.3)] bg-[#3a6420] aspect-[16/11]">
                {/* Top bevel edge for 3D depth */}
                <div className="absolute inset-x-0 top-0 h-px z-10 bg-gradient-to-r from-transparent via-white/30 to-transparent pointer-events-none" />
                <img
                  src={productsImg}
                  alt="Evergreen packaging portfolio overview"
                  className="w-full h-full object-cover"
                />
              </div>

              {/* Floating Spec 1 */}
              <motion.div
                initial={{ opacity: 0, x: -20 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.8 }}
                className="absolute -top-6 -left-6 bg-[#3a6420]/90 backdrop-blur-md p-5 rounded-2xl border border-cream/15 shadow-xl max-w-[200px]"
              >
                <span className="text-[10px] uppercase font-mono tracking-widest text-[#9cb99e]">TENSILE</span>
                <p className="font-display text-2xl text-[#fbf8f3] mt-0.5">24+ MPa</p>
                <p className="text-[11px] text-[#fbf8f3]/65 mt-0.5">High longitudinal tear load</p>
              </motion.div>

              {/* Floating Spec 2 */}
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.8, delay: 0.2 }}
                className="absolute -bottom-6 -right-4 sm:-right-6 bg-[#3a6420]/90 backdrop-blur-md p-5 rounded-2xl border border-cream/15 shadow-xl max-w-[220px]"
              >
                <span className="text-[10px] uppercase font-mono tracking-widest text-[#9cb99e]">STANDARDS</span>
                <p className="font-display text-xl text-[#fbf8f3] mt-0.5">ASTM D6400</p>
                <p className="text-[11px] text-[#fbf8f3]/65 mt-0.5">EN 13432 Compost Certification</p>
              </motion.div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 4. SUSTAINABILITY STORY: MATERIAL -> PRODUCT -> RESPONSIBLE FUTURE   */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-36 px-6 sm:px-10 lg:px-16 bg-[#eef4ee]">
        <div className="mx-auto max-w-[1450px]">
          <div className="text-center max-w-2xl mx-auto mb-20 space-y-4">
            <span className="text-xs uppercase tracking-[0.25em] font-bold text-[#355a3f]">
              The Lifecycle Journey
            </span>
            <h2 className="font-display text-4xl sm:text-6xl text-[#0e2617]">
              Material → Product → Earth.
            </h2>
            <p className="text-base sm:text-lg text-[#526456] font-light">
              How our circular ecosystem flows from renewable crop harvesting to certified planetary return.
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">

            {/* ── CARD 01 · Mint → Emerald ── */}
            <motion.div
              initial={{ opacity: 0, y: 40, rotateX: 8 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.7, ease: "easeOut" }}
              whileHover={{ y: -10, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative rounded-[2rem] p-8 sm:p-10 flex flex-col justify-between overflow-hidden
                bg-gradient-to-br from-[#d4edd6] via-[#c2e0c5] to-[#a8d1ac]
                border border-[#8ec492]/60
                shadow-[0_8px_32px_rgba(74,115,84,0.18),0_2px_8px_rgba(74,115,84,0.12),inset_0_1px_0_rgba(255,255,255,0.7)]
                hover:shadow-[0_24px_60px_rgba(74,115,84,0.35),0_8px_20px_rgba(74,115,84,0.2),inset_0_1px_0_rgba(255,255,255,0.8)]
                transition-all duration-500 cursor-pointer"
            >
              {/* Top bevel highlight */}
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/80 to-transparent pointer-events-none" />
              {/* Corner glow orb */}
              <div className="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-[#7da482]/20 blur-2xl pointer-events-none transition-all duration-500 group-hover:bg-[#7da482]/35 group-hover:scale-110" />
              {/* Bottom gradient fade */}
              <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-[#8ec492]/30 to-transparent pointer-events-none rounded-b-[2rem]" />

              <div className="relative z-10">
                <div className="flex items-center justify-between mb-8">
                  <span className="font-mono text-4xl sm:text-5xl font-light text-[#1a4a24] opacity-80">01</span>
                  <span className="text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full
                    bg-gradient-to-r from-[#4a7354] to-[#2d5016] text-white
                    shadow-[0_4px_12px_rgba(45,80,22,0.4),inset_0_1px_0_rgba(255,255,255,0.2)]">
                    Genesis
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-[1.75rem] text-[#0e2617] mb-4 leading-snug">
                  Renewable Plant Starch
                </h3>
                <p className="text-sm text-[#24452e]/80 leading-relaxed">
                  Sourced from ethically harvested cassava tubers and non-GMO cornstarch. Transformed through clean enzymatic processes into uniform biopolymer resin granules.
                </p>
              </div>

              <div className="relative z-10 pt-6 mt-6 border-t border-[#7da482]/30 flex items-center justify-between">
                <span className="text-xs text-[#1a4a24] font-bold tracking-wide">Zero petroleum feedstocks</span>
                <div className="p-2 rounded-xl bg-gradient-to-br from-[#4a7354] to-[#2d5016] shadow-[0_4px_12px_rgba(45,80,22,0.4)]">
                  <Leaf className="size-4 text-[#c2e0c5]" />
                </div>
              </div>
            </motion.div>

            {/* ── CARD 02 · Teal → Deep Forest ── */}
            <motion.div
              initial={{ opacity: 0, y: 40, rotateX: 8 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.7, delay: 0.15, ease: "easeOut" }}
              whileHover={{ y: -10, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative rounded-[2rem] p-8 sm:p-10 flex flex-col justify-between overflow-hidden
                bg-gradient-to-br from-[#1a3d2b] via-[#1e4a32] to-[#0f2a1c]
                border border-[#3a6b44]/60
                shadow-[0_8px_32px_rgba(15,42,28,0.45),0_2px_8px_rgba(15,42,28,0.3),inset_0_1px_0_rgba(156,185,158,0.15)]
                hover:shadow-[0_24px_60px_rgba(15,42,28,0.6),0_8px_20px_rgba(15,42,28,0.4),inset_0_1px_0_rgba(156,185,158,0.25)]
                transition-all duration-500 cursor-pointer"
            >
              {/* Top bevel highlight */}
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#9cb99e]/40 to-transparent pointer-events-none" />
              {/* Corner glow orb */}
              <div className="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-[#4a7354]/25 blur-2xl pointer-events-none transition-all duration-500 group-hover:bg-[#6fa876]/35 group-hover:scale-110" />
              {/* Bottom gradient */}
              <div className="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-[#0a1f14]/50 to-transparent pointer-events-none rounded-b-[2rem]" />
              {/* Subtle diagonal stripe texture */}
              <div className="absolute inset-0 opacity-[0.04] pointer-events-none"
                style={{ backgroundImage: "repeating-linear-gradient(45deg, #9cb99e 0px, #9cb99e 1px, transparent 1px, transparent 12px)" }} />

              <div className="relative z-10">
                <div className="flex items-center justify-between mb-8">
                  <span className="font-mono text-4xl sm:text-5xl font-light text-[#9cb99e]/70">02</span>
                  <span className="text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full
                    bg-gradient-to-r from-[#9cb99e]/20 to-[#7da482]/10 text-[#9cb99e]
                    border border-[#9cb99e]/20
                    shadow-[0_4px_12px_rgba(156,185,158,0.15),inset_0_1px_0_rgba(255,255,255,0.08)]">
                    Precision Craft
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-[1.75rem] text-[#e8f5ea] mb-4 leading-snug">
                  Low-Energy Blown Extrusion
                </h3>
                <p className="text-sm text-[#9cb99e]/80 leading-relaxed">
                  Extruded on calibrated low-heat blown film lines to retain tensile flexibility without adding plasticizers. Seamed with ultrasonic thermal precision for maximum weight support.
                </p>
              </div>

              <div className="relative z-10 pt-6 mt-6 border-t border-[#9cb99e]/15 flex items-center justify-between">
                <span className="text-xs text-[#9cb99e] font-bold tracking-wide">Solar-assisted processing</span>
                <div className="p-2 rounded-xl bg-gradient-to-br from-[#7da482]/30 to-[#3a6420]/40
                  border border-[#9cb99e]/20
                  shadow-[0_4px_12px_rgba(125,164,130,0.2),inset_0_1px_0_rgba(255,255,255,0.08)]">
                  <Layers className="size-4 text-[#9cb99e]" />
                </div>
              </div>
            </motion.div>

            {/* ── CARD 03 · Amber-Gold → Moss ── */}
            <motion.div
              initial={{ opacity: 0, y: 40, rotateX: 8 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.7, delay: 0.3, ease: "easeOut" }}
              whileHover={{ y: -10, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative rounded-[2rem] p-8 sm:p-10 flex flex-col justify-between overflow-hidden
                bg-gradient-to-br from-[#3d6b1e] via-[#2d5016] to-[#1a3810]
                border border-[#6a9e42]/40
                shadow-[0_8px_32px_rgba(29,65,14,0.5),0_2px_8px_rgba(29,65,14,0.35),inset_0_1px_0_rgba(185,220,130,0.12)]
                hover:shadow-[0_24px_60px_rgba(29,65,14,0.65),0_8px_20px_rgba(29,65,14,0.45),inset_0_1px_0_rgba(185,220,130,0.22)]
                transition-all duration-500 cursor-pointer"
            >
              {/* Top bevel highlight — warm golden tone */}
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#c8e87a]/50 to-transparent pointer-events-none" />
              {/* Corner glow orb — lime accent */}
              <div className="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-[#8fc040]/20 blur-3xl pointer-events-none transition-all duration-500 group-hover:bg-[#a8d454]/30 group-hover:scale-110" />
              {/* Bottom gradient */}
              <div className="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-[#0e2208]/60 to-transparent pointer-events-none rounded-b-[2rem]" />
              {/* Warm lime dot-grid texture */}
              <div className="absolute inset-0 opacity-[0.06] pointer-events-none"
                style={{ backgroundImage: "radial-gradient(circle, #c8e87a 1px, transparent 1px)", backgroundSize: "20px 20px" }} />

              <div className="relative z-10">
                <div className="flex items-center justify-between mb-8">
                  <span className="font-mono text-4xl sm:text-5xl font-light text-[#c8e87a]/60">03</span>
                  <span className="text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full
                    bg-gradient-to-r from-[#c8e87a]/20 to-[#8fc040]/15 text-[#c8e87a]
                    border border-[#c8e87a]/25
                    shadow-[0_4px_12px_rgba(200,232,122,0.15),inset_0_1px_0_rgba(255,255,255,0.08)]">
                    Return
                  </span>
                </div>
                <h3 className="font-display text-2xl sm:text-[1.75rem] text-[#f0fce4] mb-4 leading-snug">
                  Biological Decomposition
                </h3>
                <p className="text-sm text-[#b8d89a]/80 leading-relaxed">
                  Upon disposal in compost or ambient soil, microorganisms consume the organic polymers, returning them completely to water, biomass, and fertile carbon within 90 to 180 days.
                </p>
              </div>

              <div className="relative z-10 pt-6 mt-6 border-t border-[#c8e87a]/15 flex items-center justify-between">
                <span className="text-xs text-[#c8e87a] font-bold tracking-wide">100% soil enrichment</span>
                <div className="p-2 rounded-xl bg-gradient-to-br from-[#8fc040]/30 to-[#4a7a10]/40
                  border border-[#c8e87a]/20
                  shadow-[0_4px_12px_rgba(143,192,64,0.25),inset_0_1px_0_rgba(255,255,255,0.08)]">
                  <Recycle className="size-4 text-[#c8e87a]" />
                </div>
              </div>
            </motion.div>

          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 5. WHY EVERGREEN: EDITORIAL FEATURE ARCHITECTURE                    */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-36 px-6 sm:px-10 lg:px-16 bg-[#e5f0e5] border-t border-[#c8dac8]">
        <div className="mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 items-start mb-16">
            <div className="lg:col-span-5 space-y-4">
              <span className="text-xs uppercase tracking-[0.25em] font-bold text-[#355a3f]">
                The Evergreen Distinction
              </span>
              <h2 className="font-display text-4xl sm:text-6xl text-[#0e2617] leading-tight">
                Responsibility that performs.
              </h2>
            </div>
            <div className="lg:col-span-7">
              <p className="text-base sm:text-xl text-[#526456] font-light leading-relaxed">
                We believe sustainability without operational durability is a compromise no modern business can sustain.
                Evergreen blends rigorous material chemistry with reliable commercial production standards.
              </p>
            </div>
          </div>

          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {/* Feature 01 */}
            <div className="p-8 rounded-2xl bg-[#f0f7f0] border border-[#c8dac8] space-y-4 hover:border-[#7da482] transition-colors">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 01 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Material Intelligence</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Proprietary biopolymer blending optimizing elasticity, oxygen barrier, and weight capacity for real-world loads.
              </p>
            </div>

            {/* Feature 02 */}
            <div className="p-8 rounded-2xl bg-[#f0f7f0] border border-[#c8dac8] space-y-4 hover:border-[#7da482] transition-colors">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 02 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Reliable Quality</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Zero-pinhole quality guarantees, strict gauge uniformity, and batch tensile verification on every production run.
              </p>
            </div>

            {/* Feature 03 */}
            <div className="p-8 rounded-2xl bg-[#f0f7f0] border border-[#c8dac8] space-y-4 hover:border-[#7da482] transition-colors">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 03 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Circular Thinking</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Designing with end-of-life as the primary parameter. Fully compatible with industrial composting facilities.
              </p>
            </div>

            {/* Feature 04 */}
            <div className="p-8 rounded-2xl bg-[#f0f7f0] border border-[#c8dac8] space-y-4 hover:border-[#7da482] transition-colors">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 04 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Responsible Plant</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Manufacturing facilities operating with closed-loop process cooling and audited renewable power inputs.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 6. CINEMATIC SUSTAINABLE MANUFACTURING VISUAL                       */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-40 px-6 sm:px-10 lg:px-16 bg-[#2d5016] text-[#fbf8f3] overflow-hidden">
        <div className="mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            {/* Left Photographic Frame with Overlay Badge */}
            <div className="lg:col-span-7 relative">
              <div className="relative rounded-[2.5rem] overflow-hidden border border-white/20
                shadow-[0_32px_80px_rgba(0,0,0,0.55),0_8px_24px_rgba(0,0,0,0.35)]
                aspect-[4/3] bg-[#3a6420]">
                {/* Top bevel edge for 3D depth */}
                <div className="absolute inset-x-0 top-0 h-px z-10 bg-gradient-to-r from-transparent via-white/30 to-transparent pointer-events-none" />
                <img
                  src={manufacturingImg}
                  alt="Sustainable manufacturing facility in Tamil Nadu"
                  className="w-full h-full object-cover"
                />
              </div>

              <motion.div
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.8 }}
                className="absolute -bottom-8 -right-4 sm:-right-8 bg-[#3a6420]/95 backdrop-blur-md p-6 rounded-2xl border border-cream/20 shadow-2xl max-w-[280px]"
              >
                <div className="flex items-center gap-2 text-xs font-mono text-[#9cb99e] uppercase tracking-widest">
                  <CheckCircle2 className="size-4 text-[#7da482]" />
                  <span>Tamil Nadu Facility</span>
                </div>
                <p className="font-display text-xl text-[#fbf8f3] mt-2">
                  Precision with a lighter footprint.
                </p>
                <p className="text-xs text-[#fbf8f3]/65 mt-1 leading-relaxed">
                  State-of-the-art blown film extruders with real-time laser gauge monitoring.
                </p>
              </motion.div>
            </div>

            {/* Right Information Panels */}
            <div className="lg:col-span-5 space-y-8">
              <div className="space-y-4">
                <span className="text-xs uppercase tracking-[0.25em] font-bold text-[#9cb99e]">
                  Manufacturing Mindset
                </span>
                <h2 className="font-display text-4xl sm:text-6xl text-[#fbf8f3] leading-tight">
                  Discipline at industrial scale.
                </h2>
                <p className="text-base text-[#fbf8f3]/75 font-light leading-relaxed">
                  Sustainable alternatives only succeed when they match the cost-efficiencies and supply chain consistency
                  of traditional plastics. Our facility is engineered around high throughput and micro-tolerance accuracy.
                </p>
              </div>

              <div className="space-y-4">

                {/* STEP 01 — premium 3D card */}
                <div className="group/card relative p-5 rounded-2xl overflow-hidden
                  bg-gradient-to-br from-white/12 to-white/[0.03]
                  border border-white/20
                  backdrop-blur-md
                  shadow-[0_8px_32px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3),inset_0_1px_0_rgba(255,255,255,0.13)]
                  transition-all duration-300
                  hover:-translate-y-1
                  hover:shadow-[0_16px_48px_rgba(0,0,0,0.55),0_4px_12px_rgba(0,0,0,0.35),inset_0_1px_0_rgba(255,255,255,0.2)]
                  hover:border-[#9cb99e]/40">
                  {/* Top bevel */}
                  <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent pointer-events-none" />
                  {/* Corner glow */}
                  <div className="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-[#7da482]/15 blur-xl pointer-events-none group-hover/card:bg-[#7da482]/25 transition-all duration-500" />
                  <div className="flex items-start gap-4">
                    <div className="shrink-0 mt-0.5 px-2.5 py-1.5 rounded-lg
                      bg-gradient-to-br from-[#7da482]/40 to-[#3a6420]/50
                      border border-[#9cb99e]/25
                      shadow-[0_4px_12px_rgba(125,164,130,0.3),inset_0_1px_0_rgba(255,255,255,0.12)]">
                      <span className="text-[10px] font-mono tracking-widest uppercase text-[#9cb99e] font-bold">01</span>
                    </div>
                    <div>
                      <h4 className="font-semibold text-base text-[#fbf8f3] drop-shadow-sm">Material Innovation</h4>
                      <p className="text-xs text-[#fbf8f3]/70 mt-1.5 leading-relaxed">
                        Custom polymer compound tuning to match customer elongation and opacity needs.
                      </p>
                    </div>
                  </div>
                </div>

                {/* STEP 02 — premium 3D card */}
                <div className="group/card relative p-5 rounded-2xl overflow-hidden
                  bg-gradient-to-br from-white/12 to-white/[0.03]
                  border border-white/20
                  backdrop-blur-md
                  shadow-[0_8px_32px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3),inset_0_1px_0_rgba(255,255,255,0.13)]
                  transition-all duration-300
                  hover:-translate-y-1
                  hover:shadow-[0_16px_48px_rgba(0,0,0,0.55),0_4px_12px_rgba(0,0,0,0.35),inset_0_1px_0_rgba(255,255,255,0.2)]
                  hover:border-[#9cb99e]/40">
                  <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent pointer-events-none" />
                  <div className="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-[#7da482]/15 blur-xl pointer-events-none group-hover/card:bg-[#7da482]/25 transition-all duration-500" />
                  <div className="flex items-start gap-4">
                    <div className="shrink-0 mt-0.5 px-2.5 py-1.5 rounded-lg
                      bg-gradient-to-br from-[#7da482]/40 to-[#3a6420]/50
                      border border-[#9cb99e]/25
                      shadow-[0_4px_12px_rgba(125,164,130,0.3),inset_0_1px_0_rgba(255,255,255,0.12)]">
                      <span className="text-[10px] font-mono tracking-widest uppercase text-[#9cb99e] font-bold">02</span>
                    </div>
                    <div>
                      <h4 className="font-semibold text-base text-[#fbf8f3] drop-shadow-sm">Quality Control</h4>
                      <p className="text-xs text-[#fbf8f3]/70 mt-1.5 leading-relaxed">
                        Automated burst tests, seam stress verification, and certified food purity compliance.
                      </p>
                    </div>
                  </div>
                </div>

                {/* STEP 03 — premium 3D card */}
                <div className="group/card relative p-5 rounded-2xl overflow-hidden
                  bg-gradient-to-br from-white/12 to-white/[0.03]
                  border border-white/20
                  backdrop-blur-md
                  shadow-[0_8px_32px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3),inset_0_1px_0_rgba(255,255,255,0.13)]
                  transition-all duration-300
                  hover:-translate-y-1
                  hover:shadow-[0_16px_48px_rgba(0,0,0,0.55),0_4px_12px_rgba(0,0,0,0.35),inset_0_1px_0_rgba(255,255,255,0.2)]
                  hover:border-[#9cb99e]/40">
                  <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent pointer-events-none" />
                  <div className="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-[#7da482]/15 blur-xl pointer-events-none group-hover/card:bg-[#7da482]/25 transition-all duration-500" />
                  <div className="flex items-start gap-4">
                    <div className="shrink-0 mt-0.5 px-2.5 py-1.5 rounded-lg
                      bg-gradient-to-br from-[#7da482]/40 to-[#3a6420]/50
                      border border-[#9cb99e]/25
                      shadow-[0_4px_12px_rgba(125,164,130,0.3),inset_0_1px_0_rgba(255,255,255,0.12)]">
                      <span className="text-[10px] font-mono tracking-widest uppercase text-[#9cb99e] font-bold">03</span>
                    </div>
                    <div>
                      <h4 className="font-semibold text-base text-[#fbf8f3] drop-shadow-sm">Responsible Production</h4>
                      <p className="text-xs text-[#fbf8f3]/70 mt-1.5 leading-relaxed">
                        100% internal edge-trim recycling, zero polymer wastewater discharge.
                      </p>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 7. APPLICATIONS: PINTEREST EDITORIAL COLLAGE                         */}
      {/* ==================================================================== */}
      <section className="relative py-28 lg:py-36 px-6 sm:px-10 lg:px-16 bg-[#eef4ee]">
        <div className="mx-auto max-w-[1450px]">
          <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
            <div className="space-y-3 max-w-2xl">
              <span className="text-xs uppercase tracking-[0.22em] font-bold text-[#355a3f]">
                Across Industries
              </span>
              <h2 className="font-display text-4xl sm:text-6xl text-[#0e2617] leading-tight">
                Designed around real-world use.
              </h2>
              <p className="text-base sm:text-lg text-[#526456] font-light">
                Tailored solutions across supermarkets, dining chains, luxury boutiques, and municipal operations.
              </p>
            </div>
            <Button
              asChild
              variant="outline"
              className="rounded-full border-[#162a1c]/30 text-[#162a1c] hover:bg-[#162a1c] hover:text-[#fbf8f3] text-xs font-semibold uppercase tracking-wider px-6 h-11"
            >
              <Link to="/certificate" className="flex items-center gap-2">
                <span>View CPCB Certificate</span>
                <ArrowRight className="size-4" />
              </Link>
            </Button>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {/* App 1 */}
            <div className="group p-8 rounded-[2rem] bg-[#e5f0e5] border border-[#c8dac8] hover:border-[#7da482] transition-all duration-500 hover:-translate-y-1 hover:shadow-xl space-y-4">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 01 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Retail & Grocery</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                High-volume, comfortable-grip carry bags engineered to replace polyethylene at cash-counter speeds.
              </p>
              <div className="pt-4 text-xs font-semibold text-[#24452e] flex items-center gap-1 group-hover:underline">
                <span>Explore grocery bags</span>
                <ArrowDownRight className="size-4" />
              </div>
            </div>

            {/* App 2 */}
            <div className="group p-8 rounded-[2rem] bg-[#e5f0e5] border border-[#c8dac8] hover:border-[#7da482] transition-all duration-500 hover:-translate-y-1 hover:shadow-xl space-y-4">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 02 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Food & Restaurants</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Grease-resistant takeaway carry bags, flat-bottom bags, and direct food-contact produce sacks.
              </p>
              <div className="pt-4 text-xs font-semibold text-[#24452e] flex items-center gap-1 group-hover:underline">
                <span>Explore dining solutions</span>
                <ArrowDownRight className="size-4" />
              </div>
            </div>

            {/* App 3 */}
            <div className="group p-8 rounded-[2rem] bg-[#e5f0e5] border border-[#c8dac8] hover:border-[#7da482] transition-all duration-500 hover:-translate-y-1 hover:shadow-xl space-y-4">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 03 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Hospitality & Events</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Custom luxury matte bags for premium hotel amenities, conference kits, and boutique gift packs.
              </p>
              <div className="pt-4 text-xs font-semibold text-[#24452e] flex items-center gap-1 group-hover:underline">
                <span>Explore luxury totes</span>
                <ArrowDownRight className="size-4" />
              </div>
            </div>

            {/* App 4 */}
            <div className="group p-8 rounded-[2rem] bg-[#e5f0e5] border border-[#c8dac8] hover:border-[#7da482] transition-all duration-500 hover:-translate-y-1 hover:shadow-xl space-y-4">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 04 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Waste Management</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Certified municipal bin liners that facilitate wet organic waste diversion without contamination.
              </p>
              <div className="pt-4 text-xs font-semibold text-[#24452e] flex items-center gap-1 group-hover:underline">
                <span>Explore waste liners</span>
                <ArrowDownRight className="size-4" />
              </div>
            </div>

            {/* App 5 */}
            <div className="group p-8 rounded-[2rem] bg-[#e5f0e5] border border-[#c8dac8] hover:border-[#7da482] transition-all duration-500 hover:-translate-y-1 hover:shadow-xl space-y-4">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 05 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Agriculture & Nursery</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Plantable sapling nursery bags and mulch films that naturally dissolve in farming soil beds.
              </p>
              <div className="pt-4 text-xs font-semibold text-[#24452e] flex items-center gap-1 group-hover:underline">
                <span>Explore agri films</span>
                <ArrowDownRight className="size-4" />
              </div>
            </div>

            {/* App 6 */}
            <div className="group p-8 rounded-[2rem] bg-[#e5f0e5] border border-[#c8dac8] hover:border-[#7da482] transition-all duration-500 hover:-translate-y-1 hover:shadow-xl space-y-4">
              <span className="text-xs font-mono text-[#7da482] tracking-widest">[ 06 ]</span>
              <h3 className="font-display text-2xl text-[#0e2617]">Commercial & Apparel</h3>
              <p className="text-sm text-[#526456] leading-relaxed">
                Compostable garment polybags and moisture-barrier mailer envelopes for modern ethical brands.
              </p>
              <div className="pt-4 text-xs font-semibold text-[#24452e] flex items-center gap-1 group-hover:underline">
                <span>Explore commercial mailers</span>
                <ArrowDownRight className="size-4" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 8. IMPACT / STATISTICS: PINTEREST EDITORIAL PINBOARD                 */}
      {/* ==================================================================== */}
      <section className="relative py-14 sm:py-16 lg:py-20 px-4 sm:px-8 lg:px-12 bg-[#eef4ee] border-y border-[#c8dac8]/70 overflow-hidden">
        {/* Subtle decorative background watermark */}
        <div className="absolute -top-24 right-0 w-96 h-96 rounded-full bg-[#7da482]/10 blur-3xl pointer-events-none" />

        <div className="mx-auto max-w-[1450px]">
          {/* Header - Compact Pinterest Board Bar */}
          <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8 lg:mb-10">
            <div className="space-y-2 max-w-2xl">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/70 border border-[#b8d4b8] text-[11px] font-mono uppercase tracking-widest text-[#24452e] shadow-sm">
                <span className="w-2 h-2 rounded-full bg-[#4a7354] animate-pulse" />
                <span>Impact Pinboard · Verified 2026</span>
              </div>
              <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl text-[#0e2617] leading-tight tracking-tight">
                Measurable reduction. <span className="font-serif italic font-normal text-[#355a3f]">Visible progress.</span>
              </h2>
            </div>
            <p className="text-xs sm:text-sm text-[#526456] max-w-md font-light leading-relaxed">
              Every bag manufactured replaces fossil carbon with botanical starch, transforming industrial packaging waste into certified organic nourishment.
            </p>
          </div>

          {/* Pinterest Pinboard Grid - Standardized Exact Same Size & Alignment */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6 items-stretch">

            {/* PIN 01 · Botanical Resin */}
            <motion.div
              initial={{ opacity: 0, y: 25 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              whileHover={{ y: -6, scale: 1.015 }}
              className="group relative h-full rounded-[2rem] p-4 sm:p-5 bg-white border border-[#b8d4b8] shadow-[0_4px_20px_rgba(20,50,25,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(20,50,25,0.2)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                {/* Pin Top Bar: Category Pill & Link Button */}
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #MaterialOrigin
                  </span>
                  <div className="size-7 rounded-full bg-[#f0f7f0] border border-[#c8dac8] flex items-center justify-center text-[#24452e] group-hover:bg-[#0e2617] group-hover:text-[#fbf8f3] group-hover:border-[#0e2617] transition-colors shadow-sm shrink-0">
                    <ArrowUpRight className="size-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                  </div>
                </div>

                {/* Media Image Snippet - Uniform Height */}
                <div className="relative w-full h-36 rounded-2xl overflow-hidden mb-4 bg-[#e5f0e5] border border-[#c8dac8]/60">
                  <img
                    src={biopolymerGranulesImg}
                    alt="Raw botanical biopolymer granules"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                  <span className="absolute bottom-2.5 left-2.5 text-[10px] font-mono font-medium text-white/95 bg-black/40 backdrop-blur-md px-2 py-0.5 rounded-md border border-white/10">
                    Corn & Cassava Pellets
                  </span>
                </div>

                {/* Big Stat & Editorial Details */}
                <div className="space-y-1">
                  <p className="font-display text-4xl sm:text-5xl text-[#0e2617] tracking-tight">
                    <AnimatedCounter target={100} suffix="%" />
                  </p>
                  <h4 className="text-xs font-bold uppercase tracking-wider text-[#355a3f]">
                    Plant-Derived Base
                  </h4>
                  <p className="text-[11px] text-[#526456] leading-relaxed min-h-[34px]">
                    Ethically sourced starch synthesized into high-tensile organic resin.
                  </p>
                </div>
              </div>

              {/* Bottom Baseline Bar */}
              <div className="flex items-center justify-between pt-3 mt-4 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>100% Bio-based</span>
                <CheckCircle2 className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

            {/* PIN 02 · Soil Degradation */}
            <motion.div
              initial={{ opacity: 0, y: 25 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: 0.1 }}
              whileHover={{ y: -6, scale: 1.015 }}
              className="group relative h-full rounded-[2rem] p-4 sm:p-5 bg-white border border-[#b8d4b8] shadow-[0_4px_20px_rgba(20,50,25,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(20,50,25,0.2)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                {/* Pin Top Bar */}
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #SoilReturn
                  </span>
                  <div className="size-7 rounded-full bg-[#f0f7f0] border border-[#c8dac8] flex items-center justify-center text-[#24452e] group-hover:bg-[#0e2617] group-hover:text-[#fbf8f3] group-hover:border-[#0e2617] transition-colors shadow-sm shrink-0">
                    <ArrowUpRight className="size-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                  </div>
                </div>

                {/* Media Image Snippet - Uniform Height */}
                <div className="relative w-full h-36 rounded-2xl overflow-hidden mb-4 bg-[#e5f0e5] border border-[#c8dac8]/60">
                  <img
                    src={materialJourneyImg}
                    alt="Natural soil decomposition cycle"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                  <span className="absolute bottom-2.5 left-2.5 text-[10px] font-mono font-medium text-white/95 bg-black/40 backdrop-blur-md px-2 py-0.5 rounded-md border border-white/10">
                    Compost Soil Return
                  </span>
                </div>

                {/* Big Stat */}
                <div className="space-y-1">
                  <p className="font-display text-4xl sm:text-5xl text-[#0e2617] tracking-tight">
                    <AnimatedCounter target={180} suffix=" D" />
                  </p>
                  <h4 className="text-xs font-bold uppercase tracking-wider text-[#355a3f]">
                    Soil Degradation
                  </h4>
                  <p className="text-[11px] text-[#526456] leading-relaxed min-h-[34px]">
                    Breaks down into pure organic carbon and water without chemical catalysts.
                  </p>
                </div>
              </div>

              {/* Bottom Baseline Bar */}
              <div className="flex items-center justify-between pt-3 mt-4 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>90 – 180 Days</span>
                <Recycle className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

            {/* PIN 03 · Zero Microplastics */}
            <motion.div
              initial={{ opacity: 0, y: 25 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: 0.2 }}
              whileHover={{ y: -6, scale: 1.015 }}
              className="group relative h-full rounded-[2rem] p-4 sm:p-5 bg-white border border-[#b8d4b8] shadow-[0_4px_20px_rgba(20,50,25,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(20,50,25,0.2)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                {/* Pin Top Bar */}
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #ZeroPlastic
                  </span>
                  <div className="size-7 rounded-full bg-[#f0f7f0] border border-[#c8dac8] flex items-center justify-center text-[#24452e] group-hover:bg-[#0e2617] group-hover:text-[#fbf8f3] group-hover:border-[#0e2617] transition-colors shadow-sm shrink-0">
                    <ArrowUpRight className="size-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                  </div>
                </div>

                {/* Media Image Snippet - Uniform Height */}
                <div className="relative w-full h-36 rounded-2xl overflow-hidden mb-4 bg-[#e5f0e5] border border-[#c8dac8]/60">
                  <img
                    src={producePouchesImg}
                    alt="Purity tested non-toxic packaging film"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                  <span className="absolute bottom-2.5 left-2.5 text-[10px] font-mono font-medium text-white/95 bg-black/40 backdrop-blur-md px-2 py-0.5 rounded-md border border-white/10">
                    Zero Toxin Certified
                  </span>
                </div>

                {/* Big Stat */}
                <div className="space-y-1">
                  <p className="font-display text-4xl sm:text-5xl text-[#0e2617] tracking-tight">
                    <AnimatedCounter target={0} suffix="%" />
                  </p>
                  <h4 className="text-xs font-bold uppercase tracking-wider text-[#355a3f]">
                    Microplastic Residue
                  </h4>
                  <p className="text-[11px] text-[#526456] leading-relaxed min-h-[34px]">
                    100% molecular cleavage into harmless, natural organic biomass.
                  </p>
                </div>
              </div>

              {/* Bottom Baseline Bar */}
              <div className="flex items-center justify-between pt-3 mt-4 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>No synthetic persistence</span>
                <ShieldCheck className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

            {/* PIN 04 · Tensile Payload */}
            <motion.div
              initial={{ opacity: 0, y: 25 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: 0.3 }}
              whileHover={{ y: -6, scale: 1.015 }}
              className="group relative h-full rounded-[2rem] p-4 sm:p-5 bg-white border border-[#b8d4b8] shadow-[0_4px_20px_rgba(20,50,25,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(20,50,25,0.2)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                {/* Pin Top Bar */}
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #TensileRating
                  </span>
                  <div className="size-7 rounded-full bg-[#f0f7f0] border border-[#c8dac8] flex items-center justify-center text-[#24452e] group-hover:bg-[#0e2617] group-hover:text-[#fbf8f3] group-hover:border-[#0e2617] transition-colors shadow-sm shrink-0">
                    <ArrowUpRight className="size-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                  </div>
                </div>

                {/* Media Image Snippet - Uniform Height */}
                <div className="relative w-full h-36 rounded-2xl overflow-hidden mb-4 bg-[#e5f0e5] border border-[#c8dac8]/60">
                  <img
                    src={compostableBagsImg}
                    alt="Industrial payload rating certified bags"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                  <span className="absolute bottom-2.5 left-2.5 text-[10px] font-mono font-medium text-white/95 bg-black/40 backdrop-blur-md px-2 py-0.5 rounded-md border border-white/10">
                    Puncture & Tear Tested
                  </span>
                </div>

                {/* Big Stat */}
                <div className="space-y-1">
                  <p className="font-display text-4xl sm:text-5xl text-[#0e2617] tracking-tight">
                    <AnimatedCounter target={15} suffix=" kg+" />
                  </p>
                  <h4 className="text-xs font-bold uppercase tracking-wider text-[#355a3f]">
                    Tensile Capacity
                  </h4>
                  <p className="text-[11px] text-[#526456] leading-relaxed min-h-[34px]">
                    Validated against dynamic retail drop tests and heavy commercial fills.
                  </p>
                </div>
              </div>

              {/* Bottom Baseline Bar */}
              <div className="flex items-center justify-between pt-3 mt-4 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>Tested Commercial Load</span>
                <Feather className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

          </div>
        </div>
      </section>


    </div>
  );
}

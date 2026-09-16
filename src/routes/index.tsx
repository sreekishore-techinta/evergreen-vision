import { createFileRoute, Link } from "@tanstack/react-router";
import { motion } from "motion/react";
import {
  ArrowRight,
  Award,
  BadgeCheck,
  CheckCircle2,
  Cpu,
  Leaf,
  Package,
  Recycle,
  Settings2,
  ShieldCheck,
  Sprout,
  Star,
  Truck,
  Zap,
} from "lucide-react";
import { Reveal, Eyebrow, ctaImage, logoImg } from "@/components/site";

/* ── Asset imports ──────────────────────────────────────────────────────── */
import heroDesktopVideo from "@/assets/hero-desktop.mp4";
import heroMobileVideo  from "@/assets/hero-mobile.mp4";
import imgCarryBags     from "@/assets/compostable-bags-blank.jpg";
import imgWasteBags     from "@/assets/compostable-waste-bags.jpg";
import imgProduce       from "@/assets/breathable-produce-pouches.jpg";
import imgGranules      from "@/assets/biopolymer-granules.jpg";
import imgLifestyle     from "@/assets/eco-lifestyle-bag.jpg";
import imgShopping      from "@/assets/product-shopping-bag.jpg";
import aboutImg         from "@/assets/sprout-in-hands.jpg";
import ctaBagsImg       from "@/assets/cta-bags-showcase.jpg";
import manufacturingImg from "@/assets/manufacturing.jpg";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: "Evergreen Industry | Sustainable Biodegradable Packaging" },
      { name: "description", content: "Premium biodegradable and compostable carry bags for responsible modern businesses. High-quality sustainable packaging for a cleaner world." },
      { property: "og:title", content: "Evergreen Industry | Better Packaging for a Greener Tomorrow" },
      { property: "og:description", content: "Sustainable alternatives to conventional plastic — engineered for usability, durability and the realities of modern business." },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
  }),
  component: Index,
});

/* ══════════════════════════════════════════════════════════════════════════
   PRODUCT CARDS DATA — actual Evergreen product categories
   ══════════════════════════════════════════════════════════════════════════ */
const PRODUCTS = [
  {
    name: "Bio Carry Bags",
    desc: "100% compostable D-cut bags crafted from plant-derived starch. Zero microplastics, full retail strength.",
    image: imgCarryBags,
  },
  {
    name: "Shopping Bags",
    desc: "Premium loop-handle shopping bags. Customisable prints, food-contact safe, and fully biodegradable.",
    image: imgShopping,
  },
  {
    name: "T-Shirt Bags",
    desc: "Lightweight compostable T-shirt bags ideal for grocery, retail and takeaway applications.",
    image: imgLifestyle,
  },
  {
    name: "Compostable Waste Bags",
    desc: "Heavy-duty compostable liners for household, commercial and industrial waste streams.",
    image: imgWasteBags,
  },
  {
    name: "Produce Pouches",
    desc: "Breathable micro-perforated pouches that extend shelf life while remaining fully compostable.",
    image: imgProduce,
  },
  {
    name: "Biopolymer Granules",
    desc: "Corn & cassava starch-based biopolymer resin — the raw material behind every Evergreen product.",
    image: imgGranules,
  },
] as const;

/* ══════════════════════════════════════════════════════════════════════════
   WHY CHOOSE US — verified Evergreen differentiators
   ══════════════════════════════════════════════════════════════════════════ */
const WHY_CARDS = [
  {
    icon: Leaf,
    title: "Eco-Friendly",
    desc: "Biodegradable & compostable per ASTM D6400 & EN 13432.",
  },
  {
    icon: ShieldCheck,
    title: "High Quality",
    desc: "CIPET audited, ISO certified, 3-stage QC process.",
  },
  {
    icon: Settings2,
    title: "Customisable",
    desc: "Custom sizes, prints and formulations as per brand needs.",
  },
  {
    icon: Truck,
    title: "Reliable Supply",
    desc: "Dedicated Tamil Nadu blown-film facility for bulk orders.",
  },
] as const;

/* ══════════════════════════════════════════════════════════════════════════
   TRUST / CERTIFICATIONS
   ══════════════════════════════════════════════════════════════════════════ */
const TRUST_ITEMS = [
  { icon: BadgeCheck, label: "ASTM D6400 Certified" },
  { icon: BadgeCheck, label: "EN 13432 Compliant" },
  { icon: BadgeCheck, label: "CIPET Audited" },
  { icon: BadgeCheck, label: "ISO Certified Facility" },
  { icon: BadgeCheck, label: "Zero Microplastics" },
  { icon: BadgeCheck, label: "100% Bio-Derived Starch" },
] as const;

/* ══════════════════════════════════════════════════════════════════════════
   HOMEPAGE COMPONENT
   ══════════════════════════════════════════════════════════════════════════ */
function Index() {
  return (
    <main className="overflow-x-hidden bg-white">

      {/* ════════════════════════════════════════════════════════════════════
          §1  HERO — pure full-screen video, no overlays, no text
              Two separate <video> elements — CSS hides/shows per breakpoint.
              Mobile (<768 px): hero-mobile.mp4
              Desktop (≥768 px): hero-desktop.mp4
          ════════════════════════════════════════════════════════════════════ */}
      <section className="relative h-[100svh] min-h-[600px] w-full overflow-hidden">

        {/* Mobile video — visible only below md breakpoint */}
        <video
          autoPlay
          muted
          loop
          playsInline
          preload="auto"
          aria-label="Evergreen Industry sustainable packaging"
          className="absolute inset-0 h-full w-full object-cover block md:hidden"
        >
          <source src={heroMobileVideo} type="video/mp4" />
        </video>

        {/* Desktop video — visible only at md breakpoint and above */}
        <video
          autoPlay
          muted
          loop
          playsInline
          preload="auto"
          aria-label="Evergreen Industry sustainable packaging"
          className="absolute inset-0 h-full w-full object-cover hidden md:block"
        >
          <source src={heroDesktopVideo} type="video/mp4" />
        </video>

      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §2  OUR PRODUCTS — horizontal card row matching reference
          ════════════════════════════════════════════════════════════════════ */}
      <section className="bg-white px-6 py-20 lg:px-12 lg:py-24">
        <div className="mx-auto max-w-[1420px]">

          {/* Section header row */}
          <div className="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <Reveal>
              <Eyebrow>Our Products</Eyebrow>
              <h2 className="text-[2rem] leading-tight text-[#0f2718] sm:text-4xl">
                Wide range of eco-friendly<br className="hidden sm:block" /> packaging solutions
              </h2>
              <p className="mt-2 max-w-md text-sm leading-6 text-[#5a7060]">
                for your business needs.
              </p>
            </Reveal>
            <Reveal delay={0.15}>
              <Link
                to="/products"
                className="inline-flex items-center gap-2 rounded-full border border-[#1e5c2e] px-5 py-2.5 text-sm font-semibold text-[#1e5c2e] transition-all duration-300 hover:bg-[#1e5c2e] hover:text-white whitespace-nowrap"
              >
                View All Products <ArrowRight className="size-4" />
              </Link>
            </Reveal>
          </div>

          {/* Product cards — responsive grid (6 items matching reference) */}
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            {PRODUCTS.map((product, i) => (
              <Reveal key={product.name} delay={i * 0.07}>
                <Link to="/products" className="group block">
                  <div className="overflow-hidden rounded-2xl border border-[#e8f0e8] bg-[#f8faf8] transition-all duration-400 hover:-translate-y-1.5 hover:border-[#a5d6a7] hover:shadow-[0_12px_40px_rgba(30,92,46,0.13)]">
                    {/* Image */}
                    <div className="aspect-square overflow-hidden">
                      <img
                        src={product.image}
                        alt={product.name}
                        width={400}
                        height={400}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-108"
                        style={{ transformOrigin: "center" }}
                      />
                    </div>
                    {/* Card body */}
                    <div className="p-3.5">
                      <h3 className="text-[13px] font-bold leading-tight text-[#0f2718]">{product.name}</h3>
                      <div className="mt-2 flex items-center justify-between">
                        <p className="line-clamp-1 text-[11px] text-[#5a7060]">{product.desc.split(".")[0]}</p>
                        <span className="ml-2 grid size-6 shrink-0 place-items-center rounded-full bg-[#e8f5e9] text-[#1e5c2e] transition group-hover:bg-[#1e5c2e] group-hover:text-white">
                          <ArrowRight className="size-3" />
                        </span>
                      </div>
                    </div>
                  </div>
                </Link>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §3  ABOUT + WHY CHOOSE US — two-panel row matching reference
          ════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-20 lg:px-12 lg:py-24">
        <div className="mx-auto grid max-w-[1420px] gap-6 lg:grid-cols-[1.2fr_1fr]">

          {/* About panel */}
          <Reveal className="overflow-hidden rounded-3xl bg-white border border-[#dceede] shadow-sm">
            <div className="grid h-full gap-0 md:grid-cols-[1fr_1fr]">
              {/* Text */}
              <div className="flex flex-col justify-between p-8 lg:p-10">
                <div>
                  <Eyebrow>About Evergreen</Eyebrow>
                  <h2 className="text-2xl leading-snug text-[#0f2718] sm:text-3xl">
                    Packaging Today for a Greener Tomorrow
                  </h2>
                  <p className="mt-4 text-sm leading-7 text-[#5a7060]">
                    At Evergreen Industry, we are committed to providing sustainable packaging solutions that help reduce plastic waste and protect our environment. Our biodegradable carry bags are designed for strength, reliability, and a cleaner, greener future.
                  </p>
                </div>
                <Link
                  to="/about"
                  className="mt-6 inline-flex items-center gap-2 self-start rounded-full border border-[#1e5c2e] px-5 py-2.5 text-sm font-semibold text-[#1e5c2e] transition hover:bg-[#1e5c2e] hover:text-white"
                >
                  Know More <ArrowRight className="size-4" />
                </Link>
              </div>
              {/* Image */}
              <div className="relative min-h-[260px] overflow-hidden rounded-2xl m-3">
                <img
                  src={aboutImg}
                  alt="Hands holding a sprout — sustainable future"
                  width={600}
                  height={600}
                  className="h-full w-full object-cover cinematic-zoom"
                />
                <div className="absolute bottom-3 right-3 rounded-xl bg-white/90 px-3 py-2 text-center backdrop-blur-sm shadow-sm">
                  <p className="text-[10px] font-bold uppercase tracking-widest text-[#1e5c2e]">A Greener</p>
                  <p className="text-[10px] font-bold uppercase tracking-widest text-[#1e5c2e]">Tomorrow</p>
                </div>
              </div>
            </div>
          </Reveal>

          {/* Why Choose Us panel */}
          <Reveal delay={0.1} className="overflow-hidden rounded-3xl bg-[#1e5c2e] text-white shadow-sm">
            <div className="p-8 lg:p-10">
              <Eyebrow light>Why Choose Us?</Eyebrow>
              <h2 className="mb-7 text-2xl leading-snug text-white sm:text-3xl">
                The Evergreen Difference
              </h2>
              <div className="grid grid-cols-2 gap-4">
                {WHY_CARDS.map((card, i) => (
                  <motion.div
                    key={card.title}
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, delay: i * 0.1 }}
                    className="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm transition hover:bg-white/15"
                  >
                    <div className="mb-2.5 grid size-9 place-items-center rounded-xl bg-white/20">
                      <card.icon className="size-4.5 text-white" />
                    </div>
                    <p className="text-[13px] font-bold text-white">{card.title}</p>
                    <p className="mt-1 text-[11px] leading-5 text-white/65">{card.desc}</p>
                  </motion.div>
                ))}
              </div>
            </div>
          </Reveal>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §4  IMPACT — horizontal stats ticker strip
          ════════════════════════════════════════════════════════════════════ */}
      <section className="border-y border-[#e0ede0] bg-white px-6 py-16 lg:px-12">
        <div className="mx-auto max-w-[1420px]">
          <Reveal className="mb-10 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <Eyebrow>Our Impact in Numbers</Eyebrow>
              <p className="text-sm text-[#5a7060]">Together, we can make a big difference.</p>
            </div>
          </Reveal>

          {/* Stats row */}
          <div className="grid grid-cols-2 gap-6 lg:grid-cols-4">
            {[
              { icon: Leaf, value: "100%", label: "Eco Friendly", sub: "Bio-derived raw material" },
              { icon: Award, value: "500+", label: "Happy Clients", sub: "Across industries" },
              { icon: Package, value: "1M+", label: "Bags Supplied", sub: "And growing" },
              { icon: Zap, value: "5+", label: "Years of Trust", sub: "In sustainable packaging" },
            ].map((stat, i) => (
              <Reveal key={stat.label} delay={i * 0.1}>
                <div className="group rounded-2xl border border-[#e0ede0] bg-[#f8faf8] p-6 text-center transition hover:border-[#a5d6a7] hover:bg-white hover:shadow-[0_8px_32px_rgba(30,92,46,0.10)]">
                  <div className="mx-auto mb-3 grid size-12 place-items-center rounded-full bg-[#e8f5e9]">
                    <stat.icon className="size-5 text-[#1e5c2e]" />
                  </div>
                  <p className="font-display text-4xl font-normal text-[#1e5c2e] sm:text-5xl">{stat.value}</p>
                  <p className="mt-1.5 text-sm font-bold text-[#0f2718]">{stat.label}</p>
                  <p className="mt-0.5 text-[11px] text-[#5a7060]">{stat.sub}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §5  LARGE SPLIT CTA — image left / CTA content right
          ════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-20 lg:px-12 lg:py-24">
        <div className="mx-auto grid max-w-[1420px] gap-10 overflow-hidden rounded-3xl bg-white shadow-sm border border-[#dceede] lg:grid-cols-2">

          {/* Left — product showcase image */}
          <div className="relative min-h-[320px] overflow-hidden lg:min-h-[480px]">
            <img
              src={ctaBagsImg}
              alt="Evergreen biodegradable bags — reduce, reuse, recycle"
              width={900}
              height={700}
              className="h-full w-full object-cover cinematic-zoom"
            />
            {/* Overlay text badges */}
            <div className="absolute inset-0 bg-gradient-to-r from-[#0f2718]/40 to-transparent" />
            <div className="absolute bottom-6 left-6 flex flex-col gap-2">
              {["Reduce", "Reuse", "Recycle"].map((word) => (
                <span
                  key={word}
                  className="self-start rounded-lg bg-white/15 px-3 py-1 text-sm font-bold text-white backdrop-blur-sm border border-white/25"
                >
                  {word}
                </span>
              ))}
            </div>
          </div>

          {/* Right — CTA content */}
          <div className="flex flex-col justify-center px-8 py-10 lg:px-12 lg:py-14">
            <Reveal>
              <Eyebrow>Make the Switch</Eyebrow>
              <h2 className="text-3xl leading-tight text-[#0f2718] sm:text-4xl lg:text-[2.6rem]">
                Make the Switch to<br />
                <span className="text-[#1e5c2e]">Sustainable Packaging</span>
              </h2>
              <p className="mt-4 text-sm leading-7 text-[#5a7060]">
                Choose Evergreen biodegradable bags and be part of the solution for a cleaner, greener planet.
              </p>

              {/* Supporting points */}
              <div className="mt-7 flex flex-col gap-3">
                {[
                  { icon: Leaf, text: "Better for the Environment" },
                  { icon: Sprout, text: "Safer for Future Generations" },
                  { icon: Recycle, text: "Perfect for Your Business" },
                ].map(({ icon: Icon, text }) => (
                  <div key={text} className="flex items-center gap-3">
                    <div className="grid size-8 shrink-0 place-items-center rounded-full bg-[#e8f5e9]">
                      <Icon className="size-4 text-[#1e5c2e]" />
                    </div>
                    <span className="text-sm font-medium text-[#3a5c42]">{text}</span>
                  </div>
                ))}
              </div>

              {/* CTA buttons */}
              <div className="mt-8 flex flex-wrap gap-3">
                <Link
                  to="/contact"
                  className="inline-flex items-center gap-2 rounded-full bg-[#1e5c2e] px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#174d26] hover:-translate-y-0.5 hover:shadow-lg"
                >
                  Get a Quote <ArrowRight className="size-4" />
                </Link>
                <Link
                  to="/contact"
                  className="inline-flex items-center gap-2 rounded-full border-2 border-[#1e5c2e] px-6 py-3 text-sm font-semibold text-[#1e5c2e] transition hover:bg-[#1e5c2e] hover:text-white"
                >
                  Contact Us
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §6  CERTIFICATIONS & TRUST STRIP
          ════════════════════════════════════════════════════════════════════ */}
      <section className="border-y border-[#e0ede0] bg-white px-6 py-14 lg:px-12">
        <div className="mx-auto max-w-[1420px]">
          <Reveal className="mb-8 text-center">
            <Eyebrow>Certifications &amp; Standards</Eyebrow>
            <h2 className="text-2xl text-[#0f2718] sm:text-3xl">
              Trusted by industry. Verified by standards.
            </h2>
          </Reveal>
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            {TRUST_ITEMS.map((item, i) => (
              <Reveal key={item.label} delay={i * 0.07}>
                <div className="flex flex-col items-center gap-2 rounded-2xl border border-[#e0ede0] bg-[#f8faf8] p-5 text-center transition hover:border-[#a5d6a7] hover:bg-white hover:shadow-sm">
                  <item.icon className="size-7 text-[#1e5c2e]" />
                  <p className="text-[12px] font-semibold leading-snug text-[#0f2718]">{item.label}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §7  INDUSTRIES / APPLICATIONS
          ════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-20 lg:px-12 lg:py-24">
        <div className="mx-auto max-w-[1420px]">
          <div className="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <Reveal>
              <Eyebrow>Industries We Serve</Eyebrow>
              <h2 className="text-[2rem] leading-tight text-[#0f2718] sm:text-4xl">
                Designed for every business need.
              </h2>
            </Reveal>
            <Reveal delay={0.1}>
              <Link
                to="/segment"
                className="inline-flex items-center gap-2 rounded-full border border-[#1e5c2e] px-5 py-2.5 text-sm font-semibold text-[#1e5c2e] transition hover:bg-[#1e5c2e] hover:text-white whitespace-nowrap"
              >
                View All Solutions <ArrowRight className="size-4" />
              </Link>
            </Reveal>
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {[
              {
                icon: Package,
                title: "Retail & Commerce",
                desc: "D-cut and loop-handle carry bags for boutique, grocery and fashion retail.",
                color: "#e8f5e9",
              },
              {
                icon: Cpu,
                title: "Food Service",
                desc: "Takeaway bags, food-contact pouches and breathable bakery wraps.",
                color: "#f1f8e9",
              },
              {
                icon: Sprout,
                title: "Agriculture",
                desc: "Biodegradable mulch films and nursery bags for sustainable farming.",
                color: "#e8f5e9",
              },
              {
                icon: Recycle,
                title: "Waste Management",
                desc: "Heavy-duty compostable liners for residential and commercial waste.",
                color: "#f1f8e9",
              },
            ].map((item, i) => (
              <Reveal key={item.title} delay={i * 0.08}>
                <Link to="/segment" className="group block h-full">
                  <div
                    className="flex h-full min-h-[220px] flex-col rounded-2xl border border-[#e0ede0] p-7 transition-all duration-400 hover:-translate-y-2 hover:shadow-[0_12px_40px_rgba(30,92,46,0.12)]"
                    style={{ backgroundColor: item.color }}
                  >
                    <div className="mb-auto">
                      <div className="mb-4 grid size-11 place-items-center rounded-xl bg-white shadow-sm">
                        <item.icon className="size-5 text-[#1e5c2e]" />
                      </div>
                      <h3 className="text-base font-bold text-[#0f2718]">{item.title}</h3>
                      <p className="mt-2 text-[13px] leading-6 text-[#5a7060]">{item.desc}</p>
                    </div>
                    <div className="mt-5 flex items-center gap-1.5 text-[13px] font-semibold text-[#1e5c2e]">
                      Learn more
                      <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-1" />
                    </div>
                  </div>
                </Link>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §8  MANUFACTURING HIGHLIGHT — premium split section
          ════════════════════════════════════════════════════════════════════ */}
      <section className="bg-white px-6 py-20 lg:px-12 lg:py-24">
        <div className="mx-auto grid max-w-[1420px] gap-14 lg:grid-cols-2 lg:items-center">
          <Reveal>
            <Eyebrow>Our Facility</Eyebrow>
            <h2 className="text-[2rem] leading-tight text-[#0f2718] sm:text-4xl">
              Precision manufacturing.<br />
              <span className="text-[#1e5c2e]">A lighter footprint.</span>
            </h2>
            <p className="mt-5 max-w-xl text-sm leading-7 text-[#5a7060]">
              Our state-of-the-art Tamil Nadu blown-film extrusion facility combines material innovation, process discipline and quality control — so sustainable choices remain commercially practical.
            </p>

            {/* Feature list */}
            <div className="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
              {[
                "Blown-film extrusion technology",
                "CIPET audited facility",
                "Laser gauge calibration",
                "Seam stress testing",
                "Food-contact safety checks",
                "Custom formulation R&D",
              ].map((feat) => (
                <div key={feat} className="flex items-center gap-2.5">
                  <CheckCircle2 className="size-4 shrink-0 text-[#1e5c2e]" />
                  <span className="text-[13px] text-[#3a5c42]">{feat}</span>
                </div>
              ))}
            </div>

            <Link
              to="/about"
              className="mt-8 inline-flex items-center gap-2 rounded-full bg-[#1e5c2e] px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#174d26] hover:-translate-y-0.5 hover:shadow-lg"
            >
              About Our Facility <ArrowRight className="size-4" />
            </Link>
          </Reveal>

          {/* Image */}
          <Reveal delay={0.15} className="overflow-hidden rounded-3xl shadow-[0_20px_60px_rgba(15,39,24,0.14)]">
            <img
              src={manufacturingImg}
              alt="Evergreen Industry manufacturing facility"
              width={800}
              height={640}
              className="h-full w-full object-cover transition-transform duration-700 hover:scale-105 aspect-[4/3]"
            />
          </Reveal>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §9  TRUST BUILDER — "What Our Clients Say" section header
               (No fabricated testimonials — replaced with a verified
                brand-trust section using confirmed Evergreen facts)
          ════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-20 lg:px-12 lg:py-24">
        <div className="mx-auto max-w-[1420px]">
          <Reveal className="mb-10 text-center">
            <Eyebrow>Why Businesses Choose Us</Eyebrow>
            <h2 className="text-[2rem] leading-tight text-[#0f2718] sm:text-4xl">
              The values that drive every bag we make.
            </h2>
          </Reveal>

          <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {[
              {
                icon: Leaf,
                headline: "Plant-Derived Materials",
                body: "Every product starts with corn and cassava starch-based biopolymer resin — zero petroleum, zero microplastics.",
              },
              {
                icon: ShieldCheck,
                headline: "Internationally Certified",
                body: "ASTM D6400 and EN 13432 certified, CIPET audited, and manufactured to ISO quality standards.",
              },
              {
                icon: Settings2,
                headline: "Fully Customisable",
                body: "Size, thickness, print, and formulation — tailored to your brand's exact requirements at scale.",
              },
              {
                icon: Truck,
                headline: "Reliable Bulk Supply",
                body: "A dedicated blown-film extrusion facility ensures consistent quality and on-time delivery for large orders.",
              },
              {
                icon: Recycle,
                headline: "Circular by Design",
                body: "Our bags are designed to degrade back into biomass — no landfill, no plastic residue.",
              },
              {
                icon: Star,
                headline: "Proven Across Industries",
                body: "Trusted across retail, food service, agriculture, horticulture, healthcare and waste management sectors.",
              },
            ].map((card, i) => (
              <Reveal key={card.headline} delay={i * 0.08}>
                <div className="flex flex-col gap-3 rounded-2xl border border-[#dceede] bg-white p-7 shadow-sm transition hover:shadow-[0_8px_32px_rgba(30,92,46,0.10)] hover:-translate-y-1">
                  <div className="grid size-11 place-items-center rounded-xl bg-[#e8f5e9]">
                    <card.icon className="size-5 text-[#1e5c2e]" />
                  </div>
                  <h3 className="text-base font-bold text-[#0f2718]">{card.headline}</h3>
                  <p className="text-[13px] leading-6 text-[#5a7060]">{card.body}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ════════════════════════════════════════════════════════════════════
          §10  FINAL CTA BANNER — pill/curved edges, gap above footer
          ════════════════════════════════════════════════════════════════════ */}
      {/* Outer wrapper: white bg creates the visible gap between CTA and footer */}
      <div className="bg-white px-4 pb-10 pt-4 sm:px-8 sm:pb-14 lg:px-12 lg:pb-16">
        <section
          className="relative overflow-hidden px-6 py-20 text-white lg:px-12 lg:py-28 rounded-[2rem] sm:rounded-[2.5rem] lg:rounded-[3rem]"
          style={{ background: "linear-gradient(135deg, #0f2718 0%, #1e5c2e 60%, #2e7d42 100%)" }}
        >
          {/* Decorative blobs */}
          <div className="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/5 blur-3xl" />
          <div className="pointer-events-none absolute bottom-0 left-0 h-48 w-48 rounded-full bg-[#4caf50]/15 blur-2xl" />

          <div className="relative mx-auto flex max-w-[1420px] flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <Reveal className="max-w-2xl">
              <div className="mb-3 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-widest text-white/80 backdrop-blur-sm">
                <Sprout className="size-3" />
                Ready to go green?
              </div>
              <h2 className="text-3xl leading-tight text-white sm:text-5xl lg:text-[3rem]">
                Start your sustainable<br />packaging journey today.
              </h2>
              <p className="mt-4 text-sm leading-7 text-white/65">
                Join hundreds of businesses already making a positive environmental impact with Evergreen Industry.
              </p>
            </Reveal>

            <Reveal delay={0.15} className="flex shrink-0 flex-col gap-3 sm:flex-row">
              <Link
                to="/contact"
                className="inline-flex items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-[#1e5c2e] shadow-lg transition hover:bg-white/90 hover:-translate-y-0.5 hover:shadow-xl"
              >
                Get a Quote <ArrowRight className="size-4" />
              </Link>
              <Link
                to="/products"
                className="inline-flex items-center justify-center gap-2 rounded-full border-2 border-white/40 px-7 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10"
              >
                Explore Products
              </Link>
            </Reveal>
          </div>
        </section>
      </div>

    </main>
  );
}

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
import heroHeroImg      from "@/assets/home hero sec.png";
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
   PRODUCT CARDS DATA
   ══════════════════════════════════════════════════════════════════════════ */
const PRODUCTS = [
  { name: "Bio Carry Bags",          desc: "100% compostable D-cut bags crafted from plant-derived starch. Zero microplastics, full retail strength.", image: imgCarryBags },
  { name: "Shopping Bags",           desc: "Premium loop-handle shopping bags. Customisable prints, food-contact safe, and fully biodegradable.",      image: imgShopping },
  { name: "T-Shirt Bags",            desc: "Lightweight compostable T-shirt bags ideal for grocery, retail and takeaway applications.",                 image: imgLifestyle },
  { name: "Compostable Waste Bags",  desc: "Heavy-duty compostable liners for household, commercial and industrial waste streams.",                    image: imgWasteBags },
  { name: "Produce Pouches",         desc: "Breathable micro-perforated pouches that extend shelf life while remaining fully compostable.",             image: imgProduce },
  { name: "Biopolymer Granules",     desc: "Corn & cassava starch-based biopolymer resin — the raw material behind every Evergreen product.",          image: imgGranules },
] as const;

const WHY_CARDS = [
  { icon: Leaf,       title: "Eco-Friendly",    desc: "Biodegradable & compostable per ASTM D6400 & EN 13432." },
  { icon: ShieldCheck,title: "High Quality",    desc: "CIPET audited, ISO certified, 3-stage QC process." },
  { icon: Settings2,  title: "Customisable",    desc: "Custom sizes, prints and formulations as per brand needs." },
  { icon: Truck,      title: "Reliable Supply", desc: "Dedicated Tamil Nadu blown-film facility for bulk orders." },
] as const;

const TRUST_ITEMS = [
  { icon: BadgeCheck, label: "ASTM D6400 Certified" },
  { icon: BadgeCheck, label: "EN 13432 Compliant" },
  { icon: BadgeCheck, label: "CIPET Audited" },
  { icon: BadgeCheck, label: "ISO Certified Facility" },
  { icon: BadgeCheck, label: "Zero Microplastics" },
  { icon: BadgeCheck, label: "100% Bio-Derived Starch" },
] as const;

/* ══════════════════════════════════════════════════════════════════════════
   HOMEPAGE
   ══════════════════════════════════════════════════════════════════════════ */
function Index() {
  return (
    <main className="overflow-x-hidden bg-white">

      {/* ══ §1 HERO ══════════════════════════════════════════════════════════
          Image background · left-aligned content · text readable via local
          gradient only behind copy area · balanced vertical rhythm
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="w-full">
        <div className="relative w-full overflow-hidden" style={{ minHeight: "clamp(460px, 52vw, 700px)" }}>

          {/* Full-bleed hero image */}
          <img
            src={heroHeroImg}
            alt="Evergreen Industry sustainable packaging hero"
            className="absolute inset-0 h-full w-full object-cover object-center"
          />

          {/* Local gradient — only behind the text column, leaves right side clean */}
          <div className="absolute inset-0 bg-gradient-to-r from-white/72 via-white/38 to-transparent pointer-events-none" />

          {/* Content column */}
          <div
            className="relative z-10 flex h-full flex-col justify-center px-6 py-14 sm:px-10 lg:px-16 xl:px-20"
            style={{ minHeight: "clamp(460px, 52vw, 700px)" }}
          >
            {/* Eyebrow */}
            <motion.p
              initial={{ opacity: 0, y: -10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.4 }}
              className="mb-4 text-[10.5px] font-bold uppercase tracking-[0.28em] text-[#1e5c2e]"
            >
              Let's Reduce Plastic
            </motion.p>

            {/* Headline — strong hierarchy, tight leading */}
            <motion.h1
              initial={{ opacity: 0, y: 18 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.55, delay: 0.1 }}
              className="max-w-[12ch] text-[2.6rem] font-extrabold leading-[1.07] tracking-tight text-[#0a1c0e] sm:text-[3.2rem] lg:text-[4rem]"
            >
              Sustainable<br />Packaging<br />
              for a <span className="text-[#1e7a38]">Greener<br className="hidden sm:block" /> Tomorrow</span>
            </motion.h1>

            {/* Sub-copy */}
            <motion.p
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.45, delay: 0.22 }}
              className="mt-5 max-w-[38ch] text-[13.5px] leading-[1.75] text-[#1e3628] sm:text-sm"
            >
              High-quality biodegradable and compostable packaging<br className="hidden sm:block" />
              solutions for a cleaner, healthier planet.
            </motion.p>

            {/* CTA row */}
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.4, delay: 0.32 }}
              className="mt-7 flex flex-wrap items-center gap-3"
            >
              <Link
                to="/contact"
                className="inline-flex items-center gap-2 rounded-full bg-[#1a7a35] px-7 py-3 text-[13.5px] font-bold text-white shadow-[0_4px_20px_rgba(26,122,53,0.45)] transition-all duration-200 hover:bg-[#158030] hover:-translate-y-px hover:shadow-[0_6px_26px_rgba(26,122,53,0.55)]"
              >
                <Leaf className="size-4" />
                Get a Quote
                <ArrowRight className="size-4" />
              </Link>

              <a
                href="https://wa.me/919876543210"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 rounded-full border-2 border-[#0d1f0f]/30 bg-white/70 px-7 py-3 text-[13.5px] font-bold text-[#0d1f0f] backdrop-blur-sm transition-all duration-200 hover:bg-white hover:-translate-y-px hover:border-[#1a7a35]"
              >
                <svg viewBox="0 0 24 24" className="size-4 fill-[#25d366]" aria-hidden="true">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                  <path d="M12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.102 1.523 5.824L0 24l6.335-1.502A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.003-1.368l-.36-.214-3.728.884.916-3.618-.236-.373A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                </svg>
                WhatsApp Now
              </a>
            </motion.div>
          </div>
        </div>

        {/* ── §3 TRUST STRIP — equal-height cards, consistent alignment ── */}
        <div className="w-full border-t border-[#e4ede4] bg-white">
          <div className="mx-auto grid max-w-[1420px] grid-cols-3 sm:grid-cols-6 divide-x divide-[#e4ede4]">
            {[
              { icon: Leaf,    line1: "100%",            line2: "Eco Friendly",   grad: "from-emerald-500 to-green-600" },
              { icon: Recycle, line1: "Biodegradable",   line2: "& Compostable",  grad: "from-teal-500 to-emerald-600" },
              { icon: Sprout,  line1: "Non-Toxic",       line2: "& Safe",         grad: "from-lime-500 to-green-500" },
              { icon: Award,   line1: "ISO Certified",   line2: "Quality",        grad: "from-amber-400 to-emerald-500" },
              { icon: Package, line1: "Custom Branding", line2: "Available",      grad: "from-green-500 to-teal-500" },
              { icon: Truck,   line1: "Pan India",       line2: "Supply",         grad: "from-emerald-500 to-cyan-500" },
            ].map(({ icon: Icon, line1, line2, grad }, i) => (
              <motion.div
                key={line1}
                initial={{ opacity: 0, y: 16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.45, delay: 0.6 + i * 0.07 }}
                whileHover={{ y: -2, transition: { duration: 0.18 } }}
                className="group flex flex-col items-center gap-2.5 px-4 py-5 text-center cursor-default"
              >
                <div className={`grid size-10 place-items-center rounded-xl bg-gradient-to-br ${grad} shadow-[0_3px_12px_rgba(16,185,129,0.3)] transition-transform duration-200 group-hover:scale-105`}>
                  <Icon className="size-[18px] text-white" />
                </div>
                <div>
                  <p className="text-[12.5px] font-extrabold leading-tight text-[#0d1f0f]">{line1}</p>
                  <p className="mt-0.5 text-[10.5px] font-medium text-[#4a7054]">{line2}</p>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* ══ §2 OUR PRODUCTS ══════════════════════════════════════════════════
          6-col grid · equal-height cards · consistent square images
          Header row perfectly aligned with "View All Products" button
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="bg-white px-6 py-14 lg:px-12 lg:py-20">
        <div className="mx-auto max-w-[1420px]">

          {/* Section header — baseline-aligned */}
          <div className="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <Reveal>
              <Eyebrow>Our Products</Eyebrow>
              <h2 className="text-[1.9rem] font-extrabold leading-[1.15] text-[#0a1c0e] sm:text-[2.4rem]">
                Wide range of eco-friendly<br className="hidden sm:block" /> packaging solutions
              </h2>
              <p className="mt-2 text-sm leading-relaxed text-[#5a7060]">for your business needs.</p>
            </Reveal>
            <Reveal delay={0.12}>
              <Link
                to="/products"
                className="inline-flex items-center gap-2 self-start rounded-full border border-[#1e5c2e] px-5 py-2.5 text-[13px] font-semibold text-[#1e5c2e] transition-all duration-200 hover:bg-[#1e5c2e] hover:text-white whitespace-nowrap"
              >
                View All Products <ArrowRight className="size-3.5" />
              </Link>
            </Reveal>
          </div>

          {/* Product grid — equal height via h-full flex chain */}
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            {PRODUCTS.map((product, i) => (
              <Reveal key={product.name} delay={i * 0.06} className="h-full">
                <Link to="/products" className="group block h-full">
                  <div className="flex h-full flex-col overflow-hidden rounded-2xl border border-[#e6ede6] bg-[#f8faf8] transition-all duration-300 hover:-translate-y-1.5 hover:border-[#9eceaa] hover:shadow-[0_10px_32px_rgba(20,80,35,0.12)]">
                    {/* Fixed-ratio image */}
                    <div className="aspect-square w-full overflow-hidden bg-[#eef5ee]">
                      <img
                        src={product.image}
                        alt={product.name}
                        width={400}
                        height={400}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.07]"
                      />
                    </div>
                    {/* Card body */}
                    <div className="flex flex-1 flex-col p-3.5">
                      <h3 className="text-[12.5px] font-bold leading-snug text-[#0a1c0e] group-hover:text-[#1e5c2e] transition-colors">{product.name}</h3>
                      <div className="mt-auto flex items-center justify-between pt-2">
                        <p className="line-clamp-1 text-[10.5px] text-[#5a7060]">{product.desc.split(".")[0]}</p>
                        <span className="ml-2 grid size-6 shrink-0 place-items-center rounded-full bg-[#e8f5e9] text-[#1e5c2e] transition-all duration-200 group-hover:bg-[#1e5c2e] group-hover:text-white">
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

      {/* ══ §5 ABOUT + WHY CHOOSE US ══════════════════════════════════════════
          Two-column layout · matched heights · improved internal padding
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f2f7f3] px-6 py-14 lg:px-12 lg:py-20">
        <div className="mx-auto grid max-w-[1420px] items-stretch gap-6 lg:grid-cols-[1.15fr_1fr]">

          {/* About panel */}
          <Reveal className="overflow-hidden rounded-3xl border border-[#cfe4d3] bg-white shadow-[0_6px_28px_rgba(15,40,20,0.07)]">
            <div className="grid h-full md:grid-cols-[1.1fr_0.9fr]">
              <div className="flex flex-col justify-between p-8 sm:p-10">
                <div>
                  <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-1 text-[10.5px] font-bold uppercase tracking-wider text-[#185327]">
                    <span className="h-1.5 w-1.5 rounded-full bg-[#2e7d42] animate-pulse" />
                    About Evergreen
                  </div>
                  <h2 className="text-2xl font-extrabold leading-tight text-[#0a1c0e] sm:text-[1.85rem]">
                    Packaging Today for<br />a Greener Tomorrow
                  </h2>
                  <p className="mt-4 text-[14px] leading-[1.75] text-[#2d4a35]">
                    At Evergreen Industry, we are committed to providing sustainable packaging solutions that help reduce plastic waste and protect our environment. Our biodegradable carry bags are designed for strength, reliability, and a cleaner, greener future.
                  </p>
                  <div className="mt-5 flex flex-wrap gap-2">
                    {["100% Compostable", "CPCB & ISO Certified", "Custom Sizes & Prints"].map((tag) => (
                      <span key={tag} className="inline-flex items-center gap-1.5 rounded-lg border border-[#c6e6cb] bg-[#f0f9f1] px-2.5 py-1 text-[11.5px] font-semibold text-[#185327]">
                        <CheckCircle2 className="size-3.5 text-emerald-600" /> {tag}
                      </span>
                    ))}
                  </div>
                </div>
                <Link
                  to="/about"
                  className="mt-8 inline-flex items-center gap-2 self-start rounded-full bg-[#1e5c2e] px-6 py-2.5 text-[13px] font-bold text-white shadow-sm transition-all duration-200 hover:bg-[#154622] hover:shadow-md hover:-translate-y-px"
                >
                  Know More <ArrowRight className="size-4" />
                </Link>
              </div>
              <div className="relative min-h-[260px] overflow-hidden rounded-2xl m-3 border border-[#e0eee2]">
                <img
                  src={aboutImg}
                  alt="Hands holding a sprout — sustainable future"
                  width={600}
                  height={600}
                  className="h-full w-full object-cover cinematic-zoom"
                />
                <div className="absolute bottom-4 right-4 rounded-xl border border-[#cce5d1] bg-white/95 px-3.5 py-2 text-center backdrop-blur-md shadow-sm">
                  <p className="text-[10.5px] font-extrabold uppercase tracking-wider text-[#1e5c2e]">A Greener</p>
                  <p className="text-[10.5px] font-extrabold uppercase tracking-wider text-[#154622]">Tomorrow</p>
                </div>
              </div>
            </div>
          </Reveal>

          {/* Why Choose Us panel */}
          <Reveal delay={0.1} className="overflow-hidden rounded-3xl border border-emerald-800/30 bg-gradient-to-br from-[#1a5c27] via-[#154d22] to-[#0d3a18] text-white shadow-[0_8px_32px_rgba(14,60,28,0.28)]">
            <div className="flex h-full flex-col p-8 sm:p-10">
              <div className="mb-1 inline-flex w-fit items-center gap-2 rounded-full border border-white/25 bg-white/12 px-3.5 py-1 text-[10.5px] font-bold uppercase tracking-wider text-emerald-200 backdrop-blur-md">
                <span className="relative flex h-1.5 w-1.5">
                  <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75" />
                  <span className="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400" />
                </span>
                Why Choose Us?
              </div>
              <h2 className="mt-3 text-2xl font-extrabold leading-tight text-white sm:text-[1.85rem]">
                The Evergreen Difference
              </h2>
              <p className="mt-2 text-[13px] leading-relaxed text-emerald-100/70 mb-7">
                Engineered for real-world bulk demands with zero plastic compromise.
              </p>

              <div className="grid flex-1 grid-cols-2 gap-4">
                {WHY_CARDS.map((card, i) => (
                  <motion.div
                    key={card.title}
                    initial={{ opacity: 0, y: 16 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.45, delay: i * 0.1 }}
                    className="group flex flex-col gap-3 rounded-2xl border border-white/18 bg-white/[0.12] p-5 backdrop-blur-sm transition-all duration-250 hover:bg-white/[0.2] hover:border-emerald-300/40"
                  >
                    <div className="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-emerald-300 to-emerald-500 text-[#0a2912] shadow-sm transition-transform duration-200 group-hover:scale-105">
                      <card.icon className="size-5" />
                    </div>
                    <div>
                      <p className="text-[14px] font-bold text-white">{card.title}</p>
                      <p className="mt-1 text-[12px] leading-relaxed text-emerald-100/75">{card.desc}</p>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          </Reveal>
        </div>
      </section>

      {/* ══ §6 STATISTICS ════════════════════════════════════════════════════
          Prominent numbers · elegant sub-labels · premium hover
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="border-y border-[#e4ede4] bg-white px-6 py-14 lg:px-12">
        <div className="mx-auto max-w-[1420px]">
          <Reveal className="mb-8 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <Eyebrow>Our Impact in Numbers</Eyebrow>
              <p className="text-sm text-[#5a7060]">Together, we can make a big difference.</p>
            </div>
          </Reveal>

          <div className="grid grid-cols-2 gap-5 lg:grid-cols-4">
            {[
              { icon: Leaf,    value: "100%", label: "Eco Friendly",   sub: "Bio-derived raw material" },
              { icon: Award,   value: "500+", label: "Happy Clients",  sub: "Across industries" },
              { icon: Package, value: "1M+",  label: "Bags Supplied",  sub: "And growing" },
              { icon: Zap,     value: "5+",   label: "Years of Trust", sub: "In sustainable packaging" },
            ].map((stat, i) => (
              <Reveal key={stat.label} delay={i * 0.09}>
                <div className="group flex flex-col items-center rounded-2xl border border-[#e4ede4] bg-[#f8faf8] px-6 py-8 text-center transition-all duration-300 hover:border-[#9eceaa] hover:bg-white hover:shadow-[0_8px_28px_rgba(20,80,35,0.10)] hover:-translate-y-1">
                  <div className="mb-4 grid size-12 place-items-center rounded-full bg-[#e8f5e9] transition-transform duration-200 group-hover:scale-105">
                    <stat.icon className="size-5 text-[#1e5c2e]" />
                  </div>
                  <p className="text-[2.8rem] font-extrabold leading-none tracking-tight text-[#1e5c2e] sm:text-[3.2rem]">{stat.value}</p>
                  <p className="mt-2.5 text-[13px] font-bold text-[#0a1c0e]">{stat.label}</p>
                  <p className="mt-1 text-[11px] text-[#6a8a72]">{stat.sub}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ══ §7 MAKE THE SWITCH ════════════════════════════════════════════════
          Image left / content right · equal visual weight · clean CTAs
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-14 lg:px-12 lg:py-20">
        <div className="mx-auto max-w-[1420px] overflow-hidden rounded-3xl border border-[#d6ebd8] bg-white shadow-[0_4px_24px_rgba(15,40,20,0.07)] lg:grid lg:grid-cols-2">

          {/* Image side */}
          <div className="relative min-h-[300px] overflow-hidden lg:min-h-[500px]">
            <img
              src={ctaBagsImg}
              alt="Evergreen biodegradable bags — reduce, reuse, recycle"
              width={900}
              height={700}
              className="h-full w-full object-cover cinematic-zoom"
            />
            <div className="absolute inset-0 bg-gradient-to-r from-[#0a1c0e]/45 to-transparent" />
            <div className="absolute bottom-6 left-6 flex flex-col gap-2">
              {["Reduce", "Reuse", "Recycle"].map((word) => (
                <span key={word} className="self-start rounded-lg border border-white/25 bg-white/15 px-3 py-1 text-[13px] font-bold text-white backdrop-blur-sm">
                  {word}
                </span>
              ))}
            </div>
          </div>

          {/* Content side */}
          <div className="flex flex-col justify-center px-8 py-10 lg:px-12 lg:py-14">
            <Reveal>
              <Eyebrow>Make the Switch</Eyebrow>
              <h2 className="text-[1.9rem] font-extrabold leading-[1.15] text-[#0a1c0e] sm:text-[2.4rem]">
                Make the Switch to<br />
                <span className="text-[#1e5c2e]">Sustainable Packaging</span>
              </h2>
              <p className="mt-4 text-[13.5px] leading-[1.75] text-[#4a6654]">
                Choose Evergreen biodegradable bags and be part of the solution for a cleaner, greener planet.
              </p>

              <div className="mt-7 flex flex-col gap-3.5">
                {[
                  { icon: Leaf,    text: "Better for the Environment" },
                  { icon: Sprout,  text: "Safer for Future Generations" },
                  { icon: Recycle, text: "Perfect for Your Business" },
                ].map(({ icon: Icon, text }) => (
                  <div key={text} className="flex items-center gap-3">
                    <div className="grid size-8 shrink-0 place-items-center rounded-full bg-[#e8f5e9]">
                      <Icon className="size-4 text-[#1e5c2e]" />
                    </div>
                    <span className="text-[13.5px] font-medium text-[#2d4a35]">{text}</span>
                  </div>
                ))}
              </div>

              <div className="mt-8 flex flex-wrap items-center gap-3">
                <Link
                  to="/contact"
                  className="inline-flex items-center gap-2 rounded-full bg-[#1e5c2e] px-7 py-3 text-[13px] font-semibold text-white shadow-sm transition-all duration-200 hover:bg-[#174d26] hover:-translate-y-px hover:shadow-md"
                >
                  Get a Quote <ArrowRight className="size-4" />
                </Link>
                <Link
                  to="/contact"
                  className="inline-flex items-center gap-2 rounded-full border-2 border-[#1e5c2e] px-7 py-3 text-[13px] font-semibold text-[#1e5c2e] transition-all duration-200 hover:bg-[#1e5c2e] hover:text-white"
                >
                  Contact Us
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* ══ §8 CERTIFICATIONS ════════════════════════════════════════════════
          Equal-height cards · centered icons · premium trust strip
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="border-y border-[#e4ede4] bg-white px-6 py-14 lg:px-12">
        <div className="mx-auto max-w-[1420px]">
          <Reveal className="mb-8 text-center">
            <Eyebrow>Certifications &amp; Standards</Eyebrow>
            <h2 className="text-2xl font-extrabold text-[#0a1c0e] sm:text-3xl">
              Trusted by industry. Verified by standards.
            </h2>
          </Reveal>
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            {TRUST_ITEMS.map((item, i) => (
              <Reveal key={item.label} delay={i * 0.07}>
                <div className="flex h-full flex-col items-center justify-center gap-3 rounded-2xl border border-[#e4ede4] bg-[#f8faf8] px-4 py-6 text-center transition-all duration-250 hover:border-[#9eceaa] hover:bg-white hover:shadow-[0_4px_16px_rgba(20,80,35,0.08)]">
                  <item.icon className="size-7 text-[#1e5c2e]" />
                  <p className="text-[11.5px] font-semibold leading-snug text-[#0a1c0e]">{item.label}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ══ §9 INDUSTRIES WE SERVE ═══════════════════════════════════════════
          Equal-height cards · icon + title + desc vertical rhythm · hover lift
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-14 lg:px-12 lg:py-20">
        <div className="mx-auto max-w-[1420px]">
          <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <Reveal>
              <Eyebrow>Industries We Serve</Eyebrow>
              <h2 className="text-[1.9rem] font-extrabold leading-[1.15] text-[#0a1c0e] sm:text-[2.4rem]">
                Designed for every business need.
              </h2>
            </Reveal>
            <Reveal delay={0.1}>
              <Link
                to="/segment"
                className="inline-flex items-center gap-2 self-start rounded-full border border-[#1e5c2e] px-5 py-2.5 text-[13px] font-semibold text-[#1e5c2e] transition-all duration-200 hover:bg-[#1e5c2e] hover:text-white whitespace-nowrap"
              >
                View All Solutions <ArrowRight className="size-3.5" />
              </Link>
            </Reveal>
          </div>

          <div className="grid items-stretch gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {[
              { icon: Package, title: "Retail & Commerce",  desc: "D-cut and loop-handle carry bags for boutique, grocery and fashion retail.",        color: "#edf6ee" },
              { icon: Cpu,     title: "Food Service",       desc: "Takeaway bags, food-contact pouches and breathable bakery wraps.",                   color: "#f1f8e9" },
              { icon: Sprout,  title: "Agriculture",        desc: "Biodegradable mulch films and nursery bags for sustainable farming.",                 color: "#edf6ee" },
              { icon: Recycle, title: "Waste Management",   desc: "Heavy-duty compostable liners for residential and commercial waste.",                 color: "#f1f8e9" },
            ].map((item, i) => (
              <Reveal key={item.title} delay={i * 0.08} className="h-full">
                <Link to="/segment" className="group block h-full">
                  <div
                    className="flex h-full flex-col rounded-2xl border border-[#d8ead9] p-7 transition-all duration-300 hover:-translate-y-2 hover:border-[#9eceaa] hover:shadow-[0_12px_36px_rgba(20,80,35,0.12)]"
                    style={{ backgroundColor: item.color }}
                  >
                    <div className="mb-4 grid size-11 place-items-center rounded-xl bg-white shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
                      <item.icon className="size-5 text-[#1e5c2e]" />
                    </div>
                    <h3 className="text-[14.5px] font-bold text-[#0a1c0e]">{item.title}</h3>
                    <p className="mt-2.5 flex-1 text-[13px] leading-[1.7] text-[#4a6654]">{item.desc}</p>
                    <div className="mt-5 flex items-center gap-1.5 text-[12.5px] font-semibold text-[#1e5c2e]">
                      Learn more
                      <ArrowRight className="size-3.5 transition-transform duration-200 group-hover:translate-x-1" />
                    </div>
                  </div>
                </Link>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ══ §10 MANUFACTURING ════════════════════════════════════════════════
          Two-column · image height matches content · clean bullet list
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="bg-white px-6 py-14 lg:px-12 lg:py-20">
        <div className="mx-auto grid max-w-[1420px] items-center gap-12 lg:grid-cols-2">
          <Reveal>
            <Eyebrow>Our Facility</Eyebrow>
            <h2 className="text-[1.9rem] font-extrabold leading-[1.15] text-[#0a1c0e] sm:text-[2.4rem]">
              Precision manufacturing.<br />
              <span className="text-[#1e5c2e]">A lighter footprint.</span>
            </h2>
            <p className="mt-4 max-w-xl text-[13.5px] leading-[1.8] text-[#4a6654]">
              Our state-of-the-art Tamil Nadu blown-film extrusion facility combines material innovation, process discipline and quality control — so sustainable choices remain commercially practical.
            </p>

            <div className="mt-7 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
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
                  <span className="text-[13px] text-[#2d4a35]">{feat}</span>
                </div>
              ))}
            </div>

            <Link
              to="/about"
              className="mt-8 inline-flex items-center gap-2 rounded-full bg-[#1e5c2e] px-7 py-3 text-[13px] font-semibold text-white shadow-sm transition-all duration-200 hover:bg-[#174d26] hover:-translate-y-px hover:shadow-md"
            >
              About Our Facility <ArrowRight className="size-4" />
            </Link>
          </Reveal>

          <Reveal delay={0.14} className="overflow-hidden rounded-3xl shadow-[0_16px_52px_rgba(12,32,18,0.12)]">
            <img
              src={manufacturingImg}
              alt="Evergreen Industry manufacturing facility"
              width={800}
              height={640}
              className="aspect-[4/3] h-full w-full object-cover transition-transform duration-700 hover:scale-[1.03]"
            />
          </Reveal>
        </div>
      </section>

      {/* ══ §11 VALUES ═══════════════════════════════════════════════════════
          6-card grid · equal height · breathing room · reduced clutter
      ════════════════════════════════════════════════════════════════════════ */}
      <section className="bg-[#f4faf4] px-6 py-14 lg:px-12 lg:py-20">
        <div className="mx-auto max-w-[1420px]">
          <Reveal className="mb-10 text-center">
            <Eyebrow>Why Businesses Choose Us</Eyebrow>
            <h2 className="text-[1.9rem] font-extrabold leading-[1.15] text-[#0a1c0e] sm:text-[2.4rem]">
              The values that drive every bag we make.
            </h2>
          </Reveal>

          <div className="grid items-stretch gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {[
              { icon: Leaf,       headline: "Plant-Derived Materials",     body: "Every product starts with corn and cassava starch-based biopolymer resin — zero petroleum, zero microplastics." },
              { icon: ShieldCheck,headline: "Internationally Certified",   body: "ASTM D6400 and EN 13432 certified, CIPET audited, and manufactured to ISO quality standards." },
              { icon: Settings2,  headline: "Fully Customisable",          body: "Size, thickness, print, and formulation — tailored to your brand's exact requirements at scale." },
              { icon: Truck,      headline: "Reliable Bulk Supply",        body: "A dedicated blown-film extrusion facility ensures consistent quality and on-time delivery for large orders." },
              { icon: Recycle,    headline: "Circular by Design",          body: "Our bags are designed to degrade back into biomass — no landfill, no plastic residue." },
              { icon: Star,       headline: "Proven Across Industries",    body: "Trusted across retail, food service, agriculture, horticulture, healthcare and waste management sectors." },
            ].map((card, i) => (
              <Reveal key={card.headline} delay={i * 0.07} className="h-full">
                <div className="flex h-full flex-col gap-4 rounded-2xl border border-[#d6ebd8] bg-white p-7 shadow-[0_1px_8px_rgba(15,40,20,0.05)] transition-all duration-300 hover:shadow-[0_8px_28px_rgba(15,40,20,0.10)] hover:-translate-y-1">
                  <div className="grid size-11 place-items-center rounded-xl bg-[#e8f5e9]">
                    <card.icon className="size-5 text-[#1e5c2e]" />
                  </div>
                  <div>
                    <h3 className="text-[14.5px] font-bold text-[#0a1c0e]">{card.headline}</h3>
                    <p className="mt-2 text-[13px] leading-[1.7] text-[#4a6654]">{card.body}</p>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* ══ §12 FINAL CTA ═════════════════════════════════════════════════════
          Dark green pill · strong heading · clean aligned buttons
      ════════════════════════════════════════════════════════════════════════ */}
      <div className="bg-white px-4 pb-10 pt-5 sm:px-8 lg:px-12 lg:pb-14">
        <section
          className="relative overflow-hidden rounded-[2rem] px-8 py-16 text-white sm:rounded-[2.5rem] lg:rounded-[3rem] lg:px-14 lg:py-20"
          style={{ background: "linear-gradient(135deg, #0d2416 0%, #1b5a2c 55%, #2a7840 100%)" }}
        >
          {/* Subtle decorative blobs */}
          <div className="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/[0.04] blur-3xl" />
          <div className="pointer-events-none absolute -left-16 bottom-0 h-56 w-56 rounded-full bg-[#4caf50]/10 blur-2xl" />

          <div className="relative mx-auto flex max-w-[1420px] flex-col items-start justify-between gap-10 lg:flex-row lg:items-center">
            <Reveal className="max-w-2xl">
              <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[10.5px] font-bold uppercase tracking-widest text-white/80 backdrop-blur-sm">
                <Sprout className="size-3" />
                Ready to go green?
              </div>
              <h2 className="text-[2rem] font-extrabold leading-[1.12] text-white sm:text-[2.8rem] lg:text-[3.2rem]">
                Start your sustainable<br />packaging journey today.
              </h2>
              <p className="mt-4 max-w-lg text-[13.5px] leading-[1.8] text-white/60">
                Join hundreds of businesses already making a positive environmental impact with Evergreen Industry.
              </p>
            </Reveal>

            <Reveal delay={0.14} className="flex shrink-0 flex-col gap-3 sm:flex-row">
              <Link
                to="/contact"
                className="inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-3.5 text-[13.5px] font-bold text-[#1e5c2e] shadow-lg transition-all duration-200 hover:bg-white/92 hover:-translate-y-px hover:shadow-xl"
              >
                Get a Quote <ArrowRight className="size-4" />
              </Link>
              <Link
                to="/products"
                className="inline-flex items-center justify-center gap-2 rounded-full border-2 border-white/35 px-8 py-3.5 text-[13.5px] font-semibold text-white transition-all duration-200 hover:border-white/70 hover:bg-white/10"
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

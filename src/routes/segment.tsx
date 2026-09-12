import { createFileRoute, Link } from "@tanstack/react-router";
import {
  ArrowRight,
  BriefcaseMedical,
  CheckCircle2,
  ChevronRight,
  Filter,
  Leaf,
  Package,
  ShieldCheck,
  ShoppingBag,
  Sparkles,
  Sprout,
  Trash2,
  Utensils,
  UtensilsCrossed,
} from "lucide-react";
import { motion, AnimatePresence } from "motion/react";
import React, { useState } from "react";

export const Route = createFileRoute("/segment")({
  head: () => ({
    meta: [
      { title: "Our Segments | EVERGREENINDUSTRY" },
      {
        name: "description",
        content:
          "Explore our specialized market segments: Packaging, Food Service, Agriculture, Horticulture, Medical, Waste Management, and Disposable Cutlery.",
      },
      { property: "og:title", content: "Our Segments | EVERGREENINDUSTRY" },
      {
        property: "og:description",
        content:
          "Biodegradable and compostable biopolymer solutions tailored across 7 core industry sectors.",
      },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
  }),
  component: SegmentPage,
});

interface SegmentItem {
  id: string;
  category: "all" | "packaging" | "food" | "agri" | "healthcare";
  tag: string;
  title: string;
  description: string;
  icon: React.ElementType;
  applications: string[];
  keyBenefit: string;
}

const segments: SegmentItem[] = [
  {
    id: "packaging",
    category: "packaging",
    tag: "Retail & Commerce",
    title: "Packaging",
    description:
      "Biodegradable bags offer eco-friendly packaging solutions, reducing plastic waste and promoting sustainability.",
    icon: Package,
    applications: [
      "D-Cut shopping & boutique carry bags",
      "E-commerce parcel mailers & wrappers",
      "Garment & textile protective liners",
    ],
    keyBenefit: "Carries up to 15 kg without tearing; 100% microplastic-free.",
  },
  {
    id: "food-service",
    category: "food",
    tag: "Dining & Catering",
    title: "Food Service",
    description:
      "Biodegradable cutlery and food packaging bags & films provide a sustainable alternative to single-use plastics in restaurants and catering services.",
    icon: UtensilsCrossed,
    applications: [
      "Takeaway carry bags & container liners",
      "Breathable produce & bakery bags",
      "Grease-resistant food wrapping films",
    ],
    keyBenefit: "Food-contact safe, zero chemical leaching, and odorless.",
  },
  {
    id: "agriculture",
    category: "agri",
    tag: "Farming & Crops",
    title: "Agriculture",
    description:
      "Biodegradable mulch films help control weed growth, conserve soil moisture, and improve crop yields, while minimizing environmental impact.",
    icon: Sprout,
    applications: [
      "In-soil biodegradable mulch films",
      "Seedling protection & row covers",
      "Moisture conservation ground barriers",
    ],
    keyBenefit: "Tilled directly into soil after harvest; enriches organic biomass.",
  },
  {
    id: "horticulture",
    category: "agri",
    tag: "Gardening & Plant Care",
    title: "Horticulture",
    description:
      "Biodegradable nursery bags promote sustainable gardening practices, reducing plastic waste and supporting plant growth.",
    icon: Leaf,
    applications: [
      "Direct-planting nursery sapling polybags",
      "Greenhouse germination pouches",
      "Gardening & soil potting liners",
    ],
    keyBenefit: "Roots freely penetrate bag walls; eliminates plant transplant shock.",
  },
  {
    id: "medical",
    category: "healthcare",
    tag: "Clinics & Care Centers",
    title: "Medical",
    description:
      "Biodegradable autoclave biohazard bags are an eco-friendly alternative to traditional plastic, reducing waste and contributing to a sustainable future.",
    icon: BriefcaseMedical,
    applications: [
      "Autoclave-compatible biohazard liners",
      "Patient hygiene & disposal bags",
      "Clinical linen & sanitation covers",
    ],
    keyBenefit: "High puncture resistance, leak-tight base seals, and safe incineration.",
  },
  {
    id: "waste-management",
    category: "packaging",
    tag: "Municipal & Operations",
    title: "Waste Management",
    description:
      "Biodegradable waste bags and compostable bins help facilitate proper waste disposal, encouraging composting and reducing landfill waste.",
    icon: Trash2,
    applications: [
      "Wet waste organic kitchen collection bags",
      "Municipal compost collection roll bags",
      "Heavy-duty industrial bin liners",
    ],
    keyBenefit: "Decomposes synchronously with organic waste in industrial composters.",
  },
  {
    id: "disposable-cutlery",
    category: "food",
    tag: "Events & Quick Serve",
    title: "Disposable Cutlery",
    description:
      "Biodegradable forks, spoons, and knives offer eco-friendly alternatives to single-use plastic utensils.",
    icon: Utensils,
    applications: [
      "Heavy-duty compostable spoons, forks & knives",
      "Individually wrapped hygiene dining packs",
      "Beverage stirrers & dessert spoons",
    ],
    keyBenefit: "Rigid ergonomic grip, hot-food resistant, and fully compostable.",
  },
];

const filterTabs = [
  { key: "all", label: "All Segments" },
  { key: "packaging", label: "Packaging & Retail" },
  { key: "food", label: "Food Service & Cutlery" },
  { key: "agri", label: "Agriculture & Horticulture" },
  { key: "healthcare", label: "Medical & Healthcare" },
];

function SegmentPage() {
  const [activeFilter, setActiveFilter] = useState("all");

  const filteredSegments =
    activeFilter === "all"
      ? segments
      : segments.filter((s) => s.category === activeFilter);

  return (
    <div className="bg-[#eef4ee] min-h-screen text-[#162a1c] selection:bg-[#7da482] selection:text-[#2d5016]">
      {/* ==================================================================== */}
      {/* 1. HERO HEADER                                                       */}
      {/* ==================================================================== */}
      <section className="relative pt-32 pb-16 lg:pt-40 lg:pb-24 px-6 sm:px-10 lg:px-16 border-b border-[#c8dac8]/60 overflow-hidden">
        {/* Ambient subtle glow */}
        <div className="absolute -top-24 right-0 w-[500px] h-[500px] rounded-full bg-[#7da482]/15 blur-3xl pointer-events-none" />
        <div className="absolute bottom-0 left-1/4 w-[450px] h-[350px] rounded-full bg-[#2d5016]/8 blur-3xl pointer-events-none" />

        <div className="relative mx-auto max-w-[1450px]">
          <div className="text-center max-w-3xl mx-auto space-y-5">
            {/* Eyebrow */}
            <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/90 border border-[#b8d4b8] shadow-sm">
              <span className="size-2 rounded-full bg-[#355a3f] animate-pulse" />
              <span className="text-[11px] font-mono font-bold uppercase tracking-[0.24em] text-[#24452e]">
                Tailored Industry Deployments
              </span>
            </div>

            {/* Title matching reference */}
            <h1 className="font-display text-4xl sm:text-6xl lg:text-7xl text-[#0e2617] tracking-tight">
              Our Segments
            </h1>

            {/* Subtitle */}
            <p className="text-base sm:text-lg text-[#526456] font-light leading-relaxed max-w-2xl mx-auto">
              Purpose-engineered biopolymers formulated to replace non-degradable single-use plastics across
              commercial, agricultural, food service, healthcare, and industrial sectors.
            </p>

            {/* Trust Pills */}
            <div className="flex flex-wrap justify-center gap-3 pt-2">
              <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <ShieldCheck className="size-3.5 text-[#355a3f]" />
                <span>CPCB Govt. Approved</span>
              </span>
              <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <Leaf className="size-3.5 text-[#355a3f]" />
                <span>100% Home & Soil Compostable</span>
              </span>
              <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <Sparkles className="size-3.5 text-[#355a3f]" />
                <span>Zero Microplastics</span>
              </span>
            </div>
          </div>

          {/* Filter Tabs */}
          <div className="mt-12 flex flex-wrap items-center justify-center gap-2">
            {filterTabs.map((tab) => {
              const active = activeFilter === tab.key;
              return (
                <button
                  key={tab.key}
                  onClick={() => setActiveFilter(tab.key)}
                  className={`px-4 py-2 rounded-full text-xs font-semibold tracking-wider transition-all duration-250 cursor-pointer ${
                    active
                      ? "bg-[#2d5016] text-[#fbf8f3] shadow-md"
                      : "bg-white/80 text-[#526456] border border-[#c8dac8] hover:bg-white hover:text-[#0e2617]"
                  }`}
                >
                  {tab.label}
                </button>
              );
            })}
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 2. SEGMENT CARDS GRID (Reference 3-column Layout)                   */}
      {/* ==================================================================== */}
      <section className="py-20 lg:py-28 px-6 sm:px-10 lg:px-16">
        <div className="mx-auto max-w-[1350px]">
          <div className="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
            <AnimatePresence mode="popLayout">
              {filteredSegments.map((seg, idx) => {
                const IconComponent = seg.icon;
                return (
                  <motion.div
                    key={seg.id}
                    layout
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    transition={{ duration: 0.4, delay: idx * 0.05 }}
                    whileHover={{ y: -6 }}
                    className="group relative flex flex-col justify-between p-8 sm:p-9 rounded-[2rem] bg-white border border-[#c8dac8] shadow-sm hover:border-[#7da482] hover:shadow-[0_20px_50px_-15px_rgba(20,50,25,0.18)] transition-all duration-350"
                  >
                    <div>
                      {/* Top Row: Icon Container matching reference design */}
                      <div className="mb-6 flex items-center justify-between">
                        <div className="size-14 rounded-2xl bg-[#e5f0e5] border border-[#c2d8c2] grid place-items-center text-[#2d5016] transition-transform duration-300 group-hover:scale-110 shadow-2xs">
                          <IconComponent className="size-6 text-[#2d5016]" />
                        </div>
                        <span className="text-[10px] font-mono uppercase tracking-widest text-[#7da482] bg-[#f4f8f4] px-3 py-1 rounded-full border border-[#e2ece2]">
                          {seg.tag}
                        </span>
                      </div>

                      {/* Title */}
                      <h2 className="font-display text-2xl text-[#0e2617] mb-3 tracking-tight group-hover:text-[#2d5016] transition-colors">
                        {seg.title}
                      </h2>

                      {/* Description from reference */}
                      <p className="text-sm text-[#526456] leading-relaxed font-light mb-6">
                        {seg.description}
                      </p>

                      {/* Applications List */}
                      <div className="pt-4 border-t border-[#f0f5f0] space-y-2 mb-6">
                        <span className="text-[10px] font-mono uppercase tracking-wider text-[#7da482] block">
                          Key Solutions
                        </span>
                        <ul className="space-y-1.5">
                          {seg.applications.map((app, i) => (
                            <li
                              key={i}
                              className="text-xs text-[#24452e] flex items-center gap-2"
                            >
                              <span className="size-1.5 rounded-full bg-[#4a7354] shrink-0" />
                              <span>{app}</span>
                            </li>
                          ))}
                        </ul>
                      </div>
                    </div>

                    {/* Card Footer: Benefit & Action */}
                    <div className="pt-4 border-t border-[#eef4ee] flex items-center justify-between">
                      <div className="flex items-center gap-1.5 text-[11px] font-mono text-[#355a3f]">
                        <CheckCircle2 className="size-3.5" />
                        <span className="truncate max-w-[180px] sm:max-w-[200px]">
                          {seg.keyBenefit}
                        </span>
                      </div>

                      <Link
                        to="/contact"
                        className="inline-flex items-center gap-1 text-xs font-semibold text-[#2d5016] hover:text-[#162a1c] transition-colors group/link"
                      >
                        <span>Enquire</span>
                        <ArrowRight className="size-3.5 transition-transform group-hover/link:translate-x-1" />
                      </Link>
                    </div>
                  </motion.div>
                );
              })}
            </AnimatePresence>
          </div>
        </div>
      </section>
    </div>
  );
}

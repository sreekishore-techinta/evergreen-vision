import { createFileRoute, Link } from "@tanstack/react-router";
import { ArrowUpRight, CheckCircle2, Leaf, ShieldCheck, Sparkles } from "lucide-react";
import { motion } from "motion/react";
import { useEffect, useState } from "react";
import { Reveal, productsImage } from "@/components/site";

// ── Bundled product images (Vite resolves these to hashed URLs) ─
import imgCarryBags    from "@/assets/compostable-bags-blank.jpg";
import imgWasteBags   from "@/assets/compostable-waste-bags.jpg";
import imgProduce     from "@/assets/breathable-produce-pouches.jpg";
import imgGranules    from "@/assets/biopolymer-granules.jpg";
import imgLifestyle   from "@/assets/eco-lifestyle-bag.jpg";
import imgCollection  from "@/assets/product-collection.jpg";

/** Maps the filename stored in DB image_url → Vite-bundled asset URL */
const ASSET_MAP: Record<string, string> = {
  "compostable-bags-blank.jpg":    imgCarryBags,
  "compostable-waste-bags.jpg":    imgWasteBags,
  "breathable-produce-pouches.jpg": imgProduce,
  "biopolymer-granules.jpg":       imgGranules,
  "eco-lifestyle-bag.jpg":         imgLifestyle,
  "product-collection.jpg":        imgCollection,
};

export const Route = createFileRoute("/products")({
  head: () => ({
    meta: [
      { title: "Products | EVERGREENINDUSTRY" },
      { name: "description", content: "Explore biodegradable bags and compostable packaging solutions engineered for modern commercial operations." },
      { property: "og:title",       content: "Sustainable Products | EVERGREENINDUSTRY" },
      { property: "og:description", content: "Packaging solutions for modern responsible businesses." },
      { property: "og:type",        content: "website" },
      { name: "twitter:card",       content: "summary_large_image" },
    ],
  }),
  component: Products,
});

// ── API base ───────────────────────────────────────────────────
// In dev: Vite proxies /backend → http://localhost/evergreen-vision/backend (see vite.config.ts)
// In prod (XAMPP static build): Apache serves everything from the same origin, so /backend/api
//   resolves correctly relative to the domain root. For subfolder installs on shared hosting
//   use the smarter detection below which strips known client-side route segments.
function resolveApiBase(): string {
  if (typeof window === "undefined") return "/backend/api";

  // In production, strip any known client-side route from the path to find the site root.
  // e.g. http://localhost/evergreen-vision/products → root = /evergreen-vision
  const knownRoutes = [
    "/products", "/about", "/contact", "/sustainability",
    "/segment", "/admin", "/applications", "/certificate",
    "/why-evergreen",
  ];
  let base = window.location.pathname;
  for (const seg of knownRoutes) {
    const idx = base.indexOf(seg);
    if (idx !== -1) { base = base.slice(0, idx); break; }
  }
  base = base.replace(/\/$/, ""); // strip trailing slash
  return `${window.location.origin}${base}/backend/api`;
}

const API_BASE = resolveApiBase();

interface Product {
  id: number;
  name: string;
  slug: string;
  category: string;
  category_id: number | null;
  description: string;
  features: string;
  applications: string;
  image_path: string;
  image_url: string;
  certifications: string;
  is_featured: number;
  is_active: number;
  sort_order: number;
}

interface Category {
  id: number;
  name: string;
  slug: string;
  product_count: number;
}

// Fallback placeholder image (base64 tiny green square)
const PLACEHOLDER =
  "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='500'%3E%3Crect width='800' height='500' fill='%23d6f0d8'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' font-size='32' fill='%23355a3f' font-family='sans-serif'%3ENo Image%3C/text%3E%3C/svg%3E";

function getImgSrc(p: Product, apiBase: string): string {
  // 1. Uploaded file (admin-uploaded image stored in /uploads/products/)
  if (p.image_path) {
    const siteRoot = apiBase.split("/backend/api")[0];
    return siteRoot + p.image_path;
  }
  // 2. DB image_url is a known asset filename → resolve via Vite bundle map
  if (p.image_url && ASSET_MAP[p.image_url]) {
    return ASSET_MAP[p.image_url]!;
  }
  // 3. DB image_url is an absolute URL or external link
  if (p.image_url && (p.image_url.startsWith("http") || p.image_url.startsWith("/"))) {
    return p.image_url;
  }
  return PLACEHOLDER;
}

function Products() {
  const [products,   setProducts]   = useState<Product[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [activecat,  setActiveCat]  = useState<number | null>(null);
  const [loading,    setLoading]    = useState(true);
  const [error,      setError]      = useState("");

  useEffect(() => {
    Promise.all([
      fetch(`${API_BASE}/products.php?action=list`).then(r => r.json()),
      fetch(`${API_BASE}/categories.php?action=list`).then(r => r.json()),
    ])
      .then(([pRes, cRes]) => {
        if (pRes.success) setProducts(pRes.data ?? []);
        else setError("Could not load products.");
        if (cRes.success) setCategories(cRes.data ?? []);
      })
      .catch(() => setError("Network error loading products."))
      .finally(() => setLoading(false));
  }, []);

  const filtered = activecat
    ? products.filter(p => p.category_id === activecat)
    : products;

  const featured  = products.filter(p => p.is_featured);

  return (
    <div className="bg-[#eef4ee] min-h-screen">
      {/* ── HERO ──────────────────────────────────────────────── */}
      <section className="relative pt-24 pb-10 lg:pt-28 lg:pb-14 px-6 sm:px-10 lg:px-16 border-b border-[#c8dac8]/60 overflow-hidden">
        <div className="absolute -top-24 right-0 w-[500px] h-[500px] rounded-full bg-[#7da482]/15 blur-3xl pointer-events-none" />
        <div className="relative mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-8 lg:gap-10 items-center">

            {/* Left */}
            <div className="lg:col-span-6 space-y-4">
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-[#b8d4b8] text-[11px] font-mono uppercase tracking-[0.22em] text-[#24452e] shadow-sm">
                <span className="size-2 rounded-full bg-[#4a7354] animate-pulse" />
                <span>Certified Bio-Packaging Collection</span>
              </div>
              <h1 className="font-display text-4xl sm:text-6xl lg:text-[4.25rem] text-[#0e2617] leading-[1.02] tracking-tight">
                Performance, packed with{" "}
                <span className="font-serif italic font-normal text-[#355a3f]">purpose.</span>
              </h1>
              <p className="text-base sm:text-lg text-[#526456] font-light leading-relaxed max-w-xl">
                A versatile collection of sustainable packaging solutions engineered to match commercial plastic durability without persistent environmental residue.
              </p>
              <div className="flex flex-wrap gap-2.5 pt-2">
                {[
                  [Leaf,         "100% Plant-Derived"],
                  [ShieldCheck,  "ASTM D6400 & EN 13432"],
                  [CheckCircle2, "Zero Microplastics"],
                ].map(([Icon, label]) => (
                  <div key={label as string} className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                    {/* @ts-ignore */}
                    <Icon className="size-3.5 text-[#4a7354]" />
                    <span>{label as string}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Right — hero image */}
            <div className="lg:col-span-6 relative">
              <motion.div
                initial={{ opacity: 0, scale: 0.96 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, ease: "easeOut" }}
                whileHover={{ y: -6, scale: 1.01 }}
                className="group relative rounded-[2.5rem] overflow-hidden bg-white border border-white/80 shadow-[0_25px_60px_-15px_rgba(20,50,25,0.18)] aspect-[16/11]"
              >
                <div className="absolute inset-x-0 top-0 h-px z-10 bg-gradient-to-r from-transparent via-white/80 to-transparent pointer-events-none" />
                <img
                  src={
                    featured[0]
                      ? getImgSrc(featured[0], API_BASE)
                      : productsImage
                  }
                  alt="Evergreen sustainable product collection"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                />
                <div className="absolute bottom-4 left-4 sm:bottom-6 sm:left-6 z-10 px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white/60 shadow-lg text-xs font-mono text-[#0e2617] flex items-center gap-2.5">
                  <span className="size-2 rounded-full bg-[#4a7354]" />
                  <span>Commercial Grade Formulations</span>
                </div>
              </motion.div>
            </div>

          </div>
        </div>
      </section>

      {/* ── CATALOG ────────────────────────────────────────────── */}
      <section className="px-6 sm:px-10 lg:px-16 py-10 lg:py-14">
        <div className="mx-auto max-w-[1450px] space-y-7">

          {/* Section header */}
          <div className="flex flex-wrap items-end justify-between gap-4">
            <div className="space-y-1.5">
              <span className="text-xs uppercase tracking-[0.25em] font-bold text-[#355a3f]">Packaging Series</span>
              <h2 className="font-display text-3xl sm:text-5xl text-[#0e2617]">Engineered for everyday utility.</h2>
            </div>

            {/* Category filter pills */}
            {categories.length > 0 && (
              <div className="flex flex-wrap gap-2">
                <button
                  onClick={() => setActiveCat(null)}
                  className={`px-4 py-1.5 rounded-full text-xs font-semibold border transition-all duration-200 ${
                    activecat === null
                      ? "bg-[#0e2617] text-white border-[#0e2617]"
                      : "bg-white text-[#355a3f] border-[#c8dac8] hover:border-[#7da482]"
                  }`}
                >
                  All ({products.length})
                </button>
                {categories.map(cat => (
                  <button
                    key={cat.id}
                    onClick={() => setActiveCat(cat.id)}
                    className={`px-4 py-1.5 rounded-full text-xs font-semibold border transition-all duration-200 ${
                      activecat === cat.id
                        ? "bg-[#0e2617] text-white border-[#0e2617]"
                        : "bg-white text-[#355a3f] border-[#c8dac8] hover:border-[#7da482]"
                    }`}
                  >
                    {cat.name} ({cat.product_count})
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Loading */}
          {loading && (
            <div className="grid md:grid-cols-2 gap-7">
              {[1,2,3,4].map(i => (
                <div key={i} className="rounded-[2rem] bg-white border border-[#c8dac8]/80 overflow-hidden animate-pulse">
                  <div className="aspect-[16/10] bg-[#d6f0d8]" />
                  <div className="p-7 space-y-3">
                    <div className="h-5 bg-[#d6f0d8] rounded w-3/4" />
                    <div className="h-3 bg-[#e5f0e5] rounded w-1/2" />
                    <div className="h-3 bg-[#e5f0e5] rounded w-2/3" />
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Error */}
          {!loading && error && (
            <div className="rounded-2xl bg-red-50 border border-red-200 px-6 py-5 text-sm text-red-700">
              {error}
            </div>
          )}

          {/* Empty */}
          {!loading && !error && filtered.length === 0 && (
            <div className="text-center py-20 text-[#526456]">
              <Sparkles className="mx-auto mb-4 size-10 opacity-30" />
              <p className="text-lg font-semibold">No products in this category yet.</p>
              <p className="text-sm mt-1 opacity-70">Check back soon or browse all products.</p>
              {activecat && (
                <button onClick={() => setActiveCat(null)} className="mt-4 text-sm text-[#355a3f] underline underline-offset-4">
                  Show all products
                </button>
              )}
            </div>
          )}

          {/* Product grid */}
          {!loading && !error && filtered.length > 0 && (
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 items-stretch">
              {filtered.map((product, i) => (
                <Reveal
                  key={product.id}
                  delay={(i % 3) * 0.07}
                  className="h-full"
                >
                  <Link
                    to="/contact"
                    className="group flex h-full flex-col overflow-hidden rounded-2xl bg-white border border-[#dceedd] shadow-[0_2px_12px_rgba(15,40,20,0.07)] hover:shadow-[0_16px_44px_rgba(15,40,20,0.14)] hover:border-[#8dc89a] transition-all duration-300 hover:-translate-y-1"
                  >
                    {/* ── Image — fixed height so all cards align ── */}
                    <div className="relative h-[220px] w-full shrink-0 overflow-hidden bg-[#e8f5ea]">
                      <img
                        src={getImgSrc(product, API_BASE)}
                        loading="lazy"
                        alt={product.name}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        onError={e => { (e.target as HTMLImageElement).src = PLACEHOLDER; }}
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                      {!!product.is_featured && (
                        <span className="absolute top-3 left-3 flex items-center gap-1 rounded-full bg-[#0e2617] px-2.5 py-1 text-[9.5px] font-bold uppercase tracking-widest text-white shadow">
                          <Sparkles className="size-2.5" /> Featured
                        </span>
                      )}
                      {product.category && (
                        <span className="absolute top-3 right-3 rounded-full bg-white/90 backdrop-blur-sm px-2.5 py-1 text-[9.5px] font-bold uppercase tracking-wider text-[#355a3f] border border-[#c8dac8]">
                          {product.category}
                        </span>
                      )}
                    </div>

                    {/* ── Body — flex-col fill, content locked to fixed zones ── */}
                    <div className="flex flex-1 flex-col p-5">

                      {/* Zone 1: Title — fixed 2 lines */}
                      <h3 className="line-clamp-2 min-h-[2.5rem] text-[14.5px] font-bold leading-snug text-[#0e2617] group-hover:text-[#1e5c2e] transition-colors">
                        {product.name}
                      </h3>

                      {/* Zone 2: Description — fixed 2 lines */}
                      <p className="mt-2 line-clamp-2 min-h-[2.5rem] text-[12px] leading-relaxed text-[#5a7060]">
                        {product.description || "\u00A0"}
                      </p>

                      {/* Zone 3: Feature bullets — always 2 rows */}
                      <ul className="mt-3 space-y-1.5 min-h-[3.2rem]">
                        {product.features
                          ? product.features.split("\n").filter(Boolean).slice(0, 2).map((f, fi) => (
                              <li key={fi} className="flex items-center gap-2 text-[11.5px] text-[#3a5c42]">
                                <span className="size-1.5 shrink-0 rounded-full bg-emerald-500" />
                                <span className="line-clamp-1">{f.replace(/^[•\-\s]+/, "")}</span>
                              </li>
                            ))
                          : [0, 1].map(k => <li key={k} className="h-4" />)
                        }
                      </ul>

                      {/* Spacer pushes footer to bottom */}
                      <div className="flex-1" />

                      {/* Zone 4: Footer — always same height */}
                      <div className="mt-4 flex items-center justify-between border-t border-[#edf5ed] pt-3.5">
                        <div className="flex items-center gap-1.5 text-[10.5px] font-semibold text-[#4a7354] min-w-0">
                          {product.certifications ? (
                            <>
                              <ShieldCheck className="size-3.5 shrink-0" />
                              <span className="line-clamp-1">{product.certifications.split(",")[0]?.trim() ?? ""}</span>
                            </>
                          ) : (
                            <span className="opacity-0">—</span>
                          )}
                        </div>
                        <span className="ml-3 grid size-8 shrink-0 place-items-center rounded-full bg-[#0e2617] text-white shadow-sm transition-all duration-300 group-hover:bg-emerald-600 group-hover:scale-110">
                          <ArrowUpRight className="size-3.5" />
                        </span>
                      </div>
                    </div>
                  </Link>
                </Reveal>
              ))}
            </div>
          )}

          {/* CTA */}
          {!loading && filtered.length > 0 && (
            <div className="flex justify-center pt-4">
              <Link
                to="/contact"
                className="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-[#0e2617] text-white text-sm font-semibold hover:bg-[#1a3d20] transition-colors shadow-lg"
              >
                Request a custom quote <ArrowUpRight className="size-4" />
              </Link>
            </div>
          )}

        </div>
      </section>
    </div>
  );
}

import { createFileRoute, Link } from "@tanstack/react-router";
import { ArrowUpRight, CheckCircle2, Leaf, ShieldCheck, Sparkles, Tag } from "lucide-react";
import { motion } from "motion/react";
import { useEffect, useState } from "react";
import { Reveal, productsImage } from "@/components/site";

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

// ── API base — auto-detects project subfolder ─────────────────
const API_BASE =
  typeof window !== "undefined"
    ? (() => {
        const parts = window.location.pathname.split("/").filter(Boolean);
        // If running under /evergreen-vision, include it; otherwise root
        const idx = parts.indexOf("evergreen-vision");
        const root = idx >= 0 ? `/evergreen-vision` : "";
        return `${window.location.origin}${root}/backend/api`;
      })()
    : "/backend/api";

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
  if (p.image_path) {
    // image_path is like /uploads/products/xxx.jpg  — make it absolute
    const origin = typeof window !== "undefined" ? window.location.origin : "";
    const parts  = apiBase.split("/backend/api")[0];
    return parts + p.image_path;
  }
  if (p.image_url) return p.image_url;
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
      <section className="relative pt-32 pb-20 lg:pt-40 lg:pb-28 px-6 sm:px-10 lg:px-16 border-b border-[#c8dac8]/60 overflow-hidden">
        <div className="absolute -top-24 right-0 w-[500px] h-[500px] rounded-full bg-[#7da482]/15 blur-3xl pointer-events-none" />
        <div className="relative mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            {/* Left */}
            <div className="lg:col-span-6 space-y-6">
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
      <section className="px-6 sm:px-10 lg:px-16 py-24 lg:py-32">
        <div className="mx-auto max-w-[1450px] space-y-12">

          {/* Section header */}
          <div className="flex flex-wrap items-end justify-between gap-6">
            <div className="space-y-3">
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
            <div className="grid max-w-[1450px] gap-7 md:grid-cols-2">
              {filtered.map((product, i) => (
                <Reveal
                  key={product.id}
                  delay={(i % 2) * 0.08}
                  className="group overflow-hidden rounded-[2rem] bg-white border border-[#c8dac8]/80 shadow-[0_4px_20px_rgba(20,50,25,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(20,50,25,0.18)] hover:border-[#7da482] transition-all duration-500 flex flex-col justify-between"
                >
                  {/* Image */}
                  <div className="aspect-[16/10] overflow-hidden bg-[#e5f0e5] relative">
                    <img
                      src={getImgSrc(product, API_BASE)}
                      loading="lazy"
                      alt={product.name}
                      className="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"
                      onError={e => { (e.target as HTMLImageElement).src = PLACEHOLDER; }}
                    />
                    {/* Featured badge */}
                    {!!product.is_featured && (
                      <span className="absolute top-4 left-4 bg-[#0e2617] text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full">
                        Featured
                      </span>
                    )}
                    {/* Category pill */}
                    {product.category && (
                      <span className="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-[#355a3f] text-[10px] font-mono uppercase tracking-wider px-3 py-1 rounded-full border border-[#c8dac8] flex items-center gap-1">
                        <Tag className="size-2.5" />{product.category}
                      </span>
                    )}
                  </div>

                  {/* Content */}
                  <div className="grid grid-cols-[minmax(0,1fr)_auto] gap-4 p-7 sm:p-9 items-start">
                    <div className="min-w-0 space-y-2">
                      <h3 className="font-display text-2xl sm:text-3xl text-[#0e2617]">{product.name}</h3>

                      {product.applications && (
                        <p className="text-xs font-mono uppercase tracking-wider text-[#355a3f]">
                          Application · {product.applications.split("\n")[0].replace(/^[•\-\s]+/, "")}
                        </p>
                      )}

                      {product.description && (
                        <p className="text-sm leading-relaxed text-[#526456] pt-2">{product.description}</p>
                      )}

                      {product.certifications && (
                        <p className="text-xs text-[#4a7354] font-semibold pt-1 flex items-center gap-1">
                          <ShieldCheck className="size-3.5" />
                          {product.certifications}
                        </p>
                      )}

                      {/* Feature bullets */}
                      {product.features && (
                        <ul className="pt-2 space-y-1">
                          {product.features
                            .split("\n")
                            .filter(Boolean)
                            .slice(0, 3)
                            .map((f, fi) => (
                              <li key={fi} className="text-xs text-[#526456] flex items-start gap-1.5">
                                <CheckCircle2 className="size-3 mt-0.5 text-[#4a7354] shrink-0" />
                                <span>{f.replace(/^[•\-\s]+/, "")}</span>
                              </li>
                            ))}
                        </ul>
                      )}
                    </div>

                    <Link
                      to="/contact"
                      className="grid size-12 shrink-0 place-items-center rounded-full bg-[#0e2617] text-[#fbf8f3] transition-all duration-300 group-hover:bg-[#7da482] group-hover:text-[#0e2617] group-hover:scale-105 shadow-md"
                      aria-label={`Inquire about ${product.name}`}
                    >
                      <ArrowUpRight className="size-5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                    </Link>
                  </div>
                </Reveal>
              ))}
            </div>
          )}

          {/* CTA */}
          {!loading && filtered.length > 0 && (
            <div className="flex justify-center pt-8">
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

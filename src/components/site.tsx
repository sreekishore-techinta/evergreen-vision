import { Link, useRouterState } from "@tanstack/react-router";
import { AnimatePresence, motion, useScroll, useTransform } from "motion/react";
import {
  ArrowRight,
  ChevronDown,
  Facebook,
  Instagram,
  Linkedin,
  Mail,
  MapPin,
  Menu,
  Package,
  Phone,
  X,
  Youtube,
} from "lucide-react";
import { useEffect, useRef, useState, type ReactNode } from "react";
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

/* ─── API base resolver (same logic as products.tsx) ───────────────────── */
function resolveApiBase(): string {
  if (typeof window === "undefined") return "/backend/api";
  const knownRoutes = ["/products","/about","/contact","/sustainability","/segment","/admin","/applications","/certificate","/why-evergreen"];
  let base = window.location.pathname;
  for (const seg of knownRoutes) {
    const idx = base.indexOf(seg);
    if (idx !== -1) { base = base.slice(0, idx); break; }
  }
  return `${window.location.origin}${base.replace(/\/$/, "")}/backend/api`;
}

/* ─── Bundled fallback images ───────────────────────────────────────────── */
import imgCarryBags  from "@/assets/compostable-bags-blank.jpg";
import imgWasteBags  from "@/assets/compostable-waste-bags.jpg";
import imgProduce    from "@/assets/breathable-produce-pouches.jpg";
import imgGranules   from "@/assets/biopolymer-granules.jpg";
import imgLifestyle  from "@/assets/eco-lifestyle-bag.jpg";
import imgCollection from "@/assets/product-collection.jpg";
import imgShopping   from "@/assets/product-shopping-bag.jpg";

const ASSET_MAP: Record<string, string> = {
  "compostable-bags-blank.jpg":     imgCarryBags,
  "compostable-waste-bags.jpg":     imgWasteBags,
  "breathable-produce-pouches.jpg": imgProduce,
  "biopolymer-granules.jpg":        imgGranules,
  "eco-lifestyle-bag.jpg":          imgLifestyle,
  "product-collection.jpg":         imgCollection,
  "product-shopping-bag.jpg":       imgShopping,
};

const PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect width='400' height='300' fill='%23d6f0d8'/%3E%3C/svg%3E";

interface DropProduct  { id: number; name: string; slug: string; category: string; description: string; image_path: string; image_url: string; }
interface DropCategory { id: number; name: string; slug: string; product_count: number; }

function getDropImgSrc(p: DropProduct, apiBase: string): string {
  if (p.image_path) return apiBase.split("/backend/api")[0] + p.image_path;
  if (p.image_url && ASSET_MAP[p.image_url]) return ASSET_MAP[p.image_url]!;
  if (p.image_url && (p.image_url.startsWith("http") || p.image_url.startsWith("/"))) return p.image_url;
  return PLACEHOLDER;
}

/* ─── Products Mega-Dropdown ────────────────────────────────────────────── */
function ProductsDropdown({ onClose }: { onClose?: () => void }) {
  const [categories, setCategories] = useState<DropCategory[]>([]);
  const [products,   setProducts]   = useState<DropProduct[]>([]);
  const [activeTab,  setActiveTab]  = useState<string>("__all__");
  const [open,       setOpen]       = useState(false);
  const [loading,    setLoading]    = useState(false);
  const ref = useRef<HTMLDivElement>(null);
  const apiBase = resolveApiBase();

  useEffect(() => {
    if (!open || products.length > 0) return;
    setLoading(true);
    Promise.all([
      fetch(`${apiBase}/products.php?action=list`).then(r => r.json()).catch(() => ({ data: [] })),
      fetch(`${apiBase}/categories.php?action=list`).then(r => r.json()).catch(() => ({ data: [] })),
    ]).then(([pRes, cRes]) => {
      const prods = Array.isArray(pRes?.data) ? pRes.data : Array.isArray(pRes) ? pRes : [];
      const cats  = Array.isArray(cRes?.data) ? cRes.data : Array.isArray(cRes) ? cRes : [];
      setProducts(prods);
      setCategories(cats);
    }).finally(() => setLoading(false));
  }, [open]);

  useEffect(() => {
    function handler(e: MouseEvent) {
      if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
    }
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, []);

  // Category colour map for sidebar accents
  const catColors: Record<string, string> = {
    "Carry Bags":        "from-emerald-500 to-green-600",
    "Waste Bags":        "from-teal-500 to-emerald-600",
    "Produce Packaging": "from-lime-500 to-green-500",
    "Raw Materials":     "from-green-600 to-emerald-700",
    "Lifestyle":         "from-emerald-400 to-teal-500",
  };

  const displayed = activeTab === "__all__"
    ? products.slice(0, 5)
    : products.filter(p =>
        p.category === activeTab ||
        String(p.category) === activeTab ||
        categories.find(c => String(c.id) === activeTab)?.name === p.category
      ).slice(0, 5);

  const activeLabel = activeTab === "__all__"
    ? "All Products"
    : categories.find(c => String(c.id) === activeTab)?.name ?? "";

  return (
    <div ref={ref} className="relative">
      {/* ── Trigger: "Products" link navigates, chevron toggles dropdown ── */}
      <div
        className="flex items-center"
        onMouseEnter={() => setOpen(true)}
      >
        <Link
          to="/products"
          className={`px-3.5 py-2 text-[13px] font-medium transition-colors duration-150
            ${open ? "text-[#1e5c2e] font-semibold" : "text-[#3a5c42] hover:text-[#1e5c2e]"}`}
          activeProps={{ className: "px-3.5 py-2 text-[13px] font-semibold text-[#1e5c2e]" }}
          onClick={() => setOpen(false)}
        >
          Products
        </Link>
        <button
          onClick={() => setOpen(v => !v)}
          className={`-ml-1 flex items-center px-1 py-2 text-[13px] transition-colors duration-150
            ${open ? "text-[#1e5c2e]" : "text-[#3a5c42] hover:text-[#1e5c2e]"}`}
          aria-label="Open products menu"
        >
          <motion.span animate={{ rotate: open ? 180 : 0 }} transition={{ duration: 0.2 }}>
            <ChevronDown className="size-3.5" />
          </motion.span>
        </button>
      </div>

      {/* ── Mega panel ── */}
      <AnimatePresence>
        {open && (
          <motion.div
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 8 }}
            transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
            onMouseLeave={() => setOpen(false)}
            className="absolute left-1/2 top-full z-50 mt-4 w-[920px] -translate-x-1/2 overflow-hidden rounded-3xl shadow-[0_32px_80px_rgba(10,25,15,0.28)]"
            style={{ border: "1px solid rgba(255,255,255,0.08)" }}
          >
            <div className="flex h-full">

              {/* ──────────────── LEFT: dark sidebar ──────────────── */}
              <div className="w-[220px] shrink-0 bg-gradient-to-b from-[#0d2617] to-[#0a1f10] flex flex-col">

                {/* Sidebar header */}
                <div className="px-5 pt-5 pb-4 border-b border-white/[0.07]">
                  <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-400/70">Browse by</p>
                  <p className="text-[15px] font-extrabold text-white mt-0.5">Categories</p>
                </div>

                {/* Category list */}
                <div className="flex-1 py-3 overflow-y-auto">
                  {/* All Products */}
                  <button
                    onClick={() => setActiveTab("__all__")}
                    className={`group relative flex w-full items-center gap-3 px-4 py-3 text-left transition-all duration-200
                      ${activeTab === "__all__"
                        ? "bg-white/10 text-white"
                        : "text-white/60 hover:bg-white/[0.06] hover:text-white/90"}`}
                  >
                    {activeTab === "__all__" && (
                      <motion.span
                        layoutId="cat-indicator"
                        className="absolute left-0 top-1/2 h-8 w-[3px] -translate-y-1/2 rounded-r-full bg-emerald-400"
                      />
                    )}
                    <div className={`grid size-8 shrink-0 place-items-center rounded-xl transition-all
                      ${activeTab === "__all__"
                        ? "bg-gradient-to-br from-emerald-400 to-green-500 shadow-[0_0_12px_rgba(52,211,153,0.4)]"
                        : "bg-white/[0.08] group-hover:bg-white/[0.14]"}`}>
                      <Package className="size-3.5 text-white" />
                    </div>
                    <div>
                      <p className="text-[12.5px] font-semibold leading-tight">All Products</p>
                      <p className="text-[10px] text-white/40 mt-0.5">{products.length} items</p>
                    </div>
                  </button>

                  {categories.map(cat => {
                    const isActive = activeTab === String(cat.id);
                    const grad = catColors[cat.name] ?? "from-emerald-500 to-green-600";
                    return (
                      <button
                        key={cat.id}
                        onClick={() => setActiveTab(String(cat.id))}
                        className={`group relative flex w-full items-center gap-3 px-4 py-3 text-left transition-all duration-200
                          ${isActive
                            ? "bg-white/10 text-white"
                            : "text-white/60 hover:bg-white/[0.06] hover:text-white/90"}`}
                      >
                        {isActive && (
                          <motion.span
                            layoutId="cat-indicator"
                            className="absolute left-0 top-1/2 h-8 w-[3px] -translate-y-1/2 rounded-r-full bg-emerald-400"
                          />
                        )}
                        <div className={`grid size-8 shrink-0 place-items-center rounded-xl transition-all
                          ${isActive
                            ? `bg-gradient-to-br ${grad} shadow-[0_0_14px_rgba(52,211,153,0.35)]`
                            : "bg-white/[0.08] group-hover:bg-white/[0.14]"}`}>
                          <span className="text-[11px] font-extrabold text-white">
                            {cat.name.charAt(0)}
                          </span>
                        </div>
                        <div className="min-w-0">
                          <p className="truncate text-[12.5px] font-semibold leading-tight">{cat.name}</p>
                          <p className="text-[10px] text-white/40 mt-0.5">
                            {cat.product_count} product{cat.product_count !== 1 ? "s" : ""}
                          </p>
                        </div>
                      </button>
                    );
                  })}
                </div>

                {/* Sidebar footer CTA */}
                <div className="p-4 border-t border-white/[0.07]">
                  <Link
                    to="/contact"
                    onClick={() => setOpen(false)}
                    className="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 py-2.5 text-[12px] font-bold text-white shadow-[0_4px_16px_rgba(52,211,153,0.3)] transition hover:bg-emerald-400"
                  >
                    Get a Quote <ArrowRight className="size-3.5" />
                  </Link>
                </div>
              </div>

              {/* ──────────────── RIGHT: product area ──────────────── */}
              <div className="flex-1 bg-white">

                {/* Top bar */}
                <div className="flex items-center justify-between border-b border-[#f0f7f0] px-6 py-3.5">
                  <div className="flex items-center gap-2">
                    <span className="text-[11px] font-bold uppercase tracking-widest text-[#5a7060]">Showing</span>
                    <span className="rounded-full bg-[#e8f5e9] px-2.5 py-0.5 text-[11px] font-bold text-[#1e5c2e]">
                      {activeLabel}
                    </span>
                  </div>
                  <Link
                    to="/products"
                    onClick={() => setOpen(false)}
                    className="flex items-center gap-1 text-[12px] font-semibold text-[#1e5c2e] hover:text-[#174d26] transition-colors"
                  >
                    View all products <ArrowRight className="size-3" />
                  </Link>
                </div>

                {/* Product grid */}
                <div className="p-5">
                  {loading ? (
                    <div className="flex h-52 items-center justify-center gap-3">
                      <div className="size-6 animate-spin rounded-full border-2 border-[#1e5c2e] border-t-transparent" />
                      <span className="text-sm text-[#5a7060]">Loading products…</span>
                    </div>
                  ) : displayed.length === 0 ? (
                    <div className="flex h-52 flex-col items-center justify-center gap-3 text-[#b0c8b4]">
                      <Package className="size-10 opacity-30" />
                      <p className="text-sm font-medium">No products in this category yet</p>
                    </div>
                  ) : (
                    <div className="grid grid-cols-5 gap-3.5">
                      {displayed.map((p, i) => (
                        <motion.div
                          key={p.id}
                          initial={{ opacity: 0, y: 12, scale: 0.96 }}
                          animate={{ opacity: 1, y: 0, scale: 1 }}
                          transition={{ duration: 0.22, delay: i * 0.05 }}
                        >
                          <Link
                            to="/products"
                            onClick={() => setOpen(false)}
                            className="group flex flex-col overflow-hidden rounded-2xl border border-[#e8f0e8] bg-[#f8fdf9] transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_12px_36px_rgba(15,39,24,0.16)] hover:border-transparent"
                          >
                            {/* Image */}
                            <div className="relative aspect-[4/3] overflow-hidden bg-[#edf7ee]">
                              <img
                                src={getDropImgSrc(p, apiBase)}
                                alt={p.name}
                                className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                              />
                              {/* Dark gradient on hover */}
                              <div className="absolute inset-0 bg-gradient-to-t from-[#0d1f0f]/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                              {/* Category tag */}
                              <div className="absolute bottom-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span className="rounded-full bg-emerald-500/90 px-2 py-0.5 text-[9px] font-bold text-white backdrop-blur-sm">
                                  {p.category}
                                </span>
                              </div>
                            </div>

                            {/* Card body */}
                            <div className="p-3">
                              <p className="line-clamp-2 text-[11.5px] font-bold leading-snug text-[#0d1f0f] group-hover:text-[#1e5c2e] transition-colors duration-200">
                                {p.name}
                              </p>
                              <p className="mt-1 line-clamp-1 text-[10px] leading-relaxed text-[#6b9070]">
                                {p.description?.split(".")[0] ?? p.category}
                              </p>
                              <div className="mt-2 flex items-center gap-0.5 text-[10.5px] font-bold text-[#1e5c2e] opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-200">
                                Explore <ArrowRight className="size-3" />
                              </div>
                            </div>
                          </Link>
                        </motion.div>
                      ))}
                    </div>
                  )}
                </div>

                {/* Bottom strip */}
                <div className="mx-5 mb-5 flex items-center justify-between rounded-2xl border border-[#e0f0e3] bg-gradient-to-r from-[#f0faf2] to-[#e8f7ec] px-5 py-3">
                  <div>
                    <p className="text-[12.5px] font-bold text-[#0d1f0f]">Can't find what you need?</p>
                    <p className="text-[11px] text-[#5a7060]">We offer custom sizes, prints & formulations.</p>
                  </div>
                  <Link
                    to="/contact"
                    onClick={() => setOpen(false)}
                    className="inline-flex items-center gap-1.5 rounded-full bg-[#1e5c2e] px-4 py-2 text-[11.5px] font-bold text-white shadow-sm transition hover:bg-[#174d26] hover:shadow-md"
                  >
                    Request Custom <ArrowRight className="size-3" />
                  </Link>
                </div>
              </div>

            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

/* ─── SITE HEADER — full-width flat navbar ─────────────────────────────── */
export function SiteHeader() {
  const [open, setOpen] = useState(false);
  const { scrollY } = useScroll();
  const navBg = useTransform(
    scrollY,
    [0, 80],
    ["rgba(255,255,255,0.94)", "rgba(255,255,255,1)"]
  );
  const navShadow = useTransform(
    scrollY,
    [0, 80],
    [
      "0 1px 0px rgba(20,60,30,0.07)",
      "0 2px 24px rgba(20,60,30,0.10)",
    ]
  );

  return (
    <header className="fixed inset-x-0 top-0 z-50">
      {/* Desktop */}
      <motion.div
        style={{ backgroundColor: navBg, boxShadow: navShadow }}
        className="hidden lg:flex h-[72px] w-full items-center justify-between px-8 xl:px-16 backdrop-blur-md border-b border-[#dde8dd]"
      >
        <Link to="/" className="flex items-center gap-2.5 shrink-0">
          <img src={logoImg} alt="Evergreen Industries" className="h-[46px] w-auto object-contain" style={{ maxWidth: "46px" }} />
          <div className="leading-[1.2]">
            <p className="text-[14.5px] font-extrabold tracking-[0.07em] text-[#0a1c0e]">EVERGREEN</p>
            <p className="text-[10.5px] font-semibold tracking-[0.2em] text-[#2e7d42]">INDUSTRIES</p>
          </div>
        </Link>

        <NavLinks />

        <Link
          to="/contact"
          className="shrink-0 inline-flex items-center gap-2 rounded-full bg-[#1e5c2e] px-6 py-2.5 text-[13px] font-semibold text-white transition-all duration-200 hover:bg-[#174d26] hover:shadow-[0_4px_16px_rgba(20,80,35,0.35)] hover:-translate-y-px"
        >
          Get a Quote <ArrowRight className="size-3.5" />
        </Link>
      </motion.div>

      {/* Mobile */}
      <div className="lg:hidden">
        <motion.div
          style={{ backgroundColor: navBg, boxShadow: navShadow }}
          className="flex h-[60px] w-full items-center justify-between px-4 backdrop-blur-md border-b border-[#dde8dd]"
        >
          <Link to="/" className="flex items-center gap-2 shrink-0" onClick={() => setOpen(false)}>
            <img src={logoImg} alt="Evergreen Industries" className="h-[38px] w-auto object-contain" style={{ maxWidth: "38px" }} />
            <div className="leading-[1.2]">
              <p className="text-[13px] font-extrabold tracking-[0.07em] text-[#0a1c0e]">EVERGREEN</p>
              <p className="text-[9.5px] font-semibold tracking-[0.17em] text-[#2e7d42]">INDUSTRIES</p>
            </div>
          </Link>
          <button
            className="grid size-9 place-items-center rounded-xl border border-[#d4e4d4] text-[#1a3d22] transition hover:bg-[#f0f7f0]"
            onClick={() => setOpen((v) => !v)}
            aria-label="Toggle navigation"
          >
            {open ? <X className="size-4.5" /> : <Menu className="size-4.5" />}
          </button>
        </motion.div>

        <AnimatePresence>
          {open && (
            <motion.nav
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: "auto", opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              transition={{ duration: 0.22 }}
              className="overflow-hidden bg-white border-b border-[#d4e4d4] shadow-lg"
            >
              <div className="flex flex-col gap-0.5 px-4 py-3">
                {nav.map(([to, label]) => {
                  if (label === "Products") {
                    return (
                      <div key="mobile-products">
                        <Link
                          to="/products"
                          onClick={() => setOpen(false)}
                          className="flex items-center justify-between rounded-xl px-4 py-3 text-[13.5px] font-medium text-[#1a3d22] transition hover:bg-[#f0f7f0]"
                          activeProps={{ className: "flex items-center justify-between rounded-xl px-4 py-3 text-[13.5px] font-semibold text-[#1e5c2e] bg-[#f0f7f0]" }}
                        >
                          Products
                          <ArrowRight className="size-3.5 text-[#1e5c2e]" />
                        </Link>
                      </div>
                    );
                  }
                  return (
                    <Link
                      key={`${to}-${label}`}
                      to={to}
                      onClick={() => setOpen(false)}
                      className="rounded-xl px-4 py-3 text-[13.5px] font-medium text-[#1a3d22] transition hover:bg-[#f0f7f0]"
                      activeProps={{ className: "rounded-xl px-4 py-3 text-[13.5px] font-semibold text-[#1e5c2e] bg-[#f0f7f0]" }}
                    >
                      {label}
                    </Link>
                  );
                })}
                <Link
                  to="/contact"
                  onClick={() => setOpen(false)}
                  className="mt-2 rounded-full bg-[#1e5c2e] px-5 py-3 text-center text-[13.5px] font-semibold text-white transition hover:bg-[#174d26]"
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
    <nav className="flex items-center gap-0.5">
      {nav.map(([to, label]) => {
        if (label === "Products") {
          return <ProductsDropdown key="products-dropdown" />;
        }
        return (
          <Link
            key={`${to}-${label}`}
            to={to}
            className="relative px-3.5 py-2 text-[13px] font-medium text-[#3a5c42] transition-colors duration-150 hover:text-[#1e5c2e]"
            activeProps={{
              className: "relative px-3.5 py-2 text-[13px] font-semibold text-[#1e5c2e] after:absolute after:bottom-0 after:left-3.5 after:right-3.5 after:h-[2px] after:rounded-full after:bg-[#1e5c2e]",
            }}
          >
            {label}
          </Link>
        );
      })}
    </nav>
  );
}

/* ─── SITE FOOTER — reference-style 4-col dark footer ──────────────────── */
export function SiteFooter() {
  return (
    <footer className="bg-[#0a1c0e] text-white">
      <div className="mx-auto max-w-[1420px] px-6 pt-16 pb-10 lg:px-12">
        <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-[1.7fr_1fr_1.2fr_1fr]">

          {/* Col 1 — Brand */}
          <div>
            <div className="mb-5">
              <img src={logoImg} alt="Evergreen Industries" className="h-12 w-auto object-contain brightness-0 invert" />
            </div>
            <p className="text-[13px] leading-[1.8] text-white/50 max-w-[250px]">
              Sustainable packaging for a better tomorrow. Premium biodegradable &amp; compostable solutions for responsible businesses.
            </p>
            <div className="mt-6 flex items-center gap-2.5">
              {[
                { icon: Facebook,  href: "#", label: "Facebook" },
                { icon: Instagram, href: "#", label: "Instagram" },
                { icon: Linkedin,  href: "#", label: "LinkedIn" },
                { icon: Youtube,   href: "#", label: "YouTube" },
              ].map(({ icon: Icon, href, label }) => (
                <a
                  key={label}
                  href={href}
                  aria-label={label}
                  className="grid size-8 place-items-center rounded-full border border-white/12 text-white/45 transition-all duration-200 hover:border-[#4a9a5a] hover:text-[#4a9a5a]"
                >
                  <Icon className="size-3.5" />
                </a>
              ))}
            </div>
          </div>

          {/* Col 2 — Quick Links */}
          <div>
            <p className="mb-5 text-[10.5px] font-bold uppercase tracking-[0.22em] text-[#6ab87a]">Quick Links</p>
            <div className="flex flex-col gap-3">
              {([
                ["/",             "Home"],
                ["/about",        "About Us"],
                ["/products",     "Products"],
                ["/segment",      "Solutions"],
                ["/contact",      "Contact"],
              ] as const).map(([to, label]) => (
                <Link key={to + label} to={to} className="text-[13px] text-white/55 transition-colors duration-150 hover:text-white">
                  {label}
                </Link>
              ))}
            </div>
          </div>

          {/* Col 3 — Our Products */}
          <div>
            <p className="mb-5 text-[10.5px] font-bold uppercase tracking-[0.22em] text-[#6ab87a]">Our Products</p>
            <div className="flex flex-col gap-3">
              {["Bio Carry Bags", "Shopping Bags", "T-Shirt Bags", "Compostable Waste Bags", "Produce & Breathable Pouches", "Biopolymer Granules"].map((name) => (
                <Link key={name} to="/products" className="text-[13px] text-white/55 transition-colors duration-150 hover:text-white">
                  {name}
                </Link>
              ))}
            </div>
          </div>

          {/* Col 4 — Contact */}
          <div>
            <p className="mb-5 text-[10.5px] font-bold uppercase tracking-[0.22em] text-[#6ab87a]">Contact Us</p>
            <div className="flex flex-col gap-4">
              <a href="tel:+919003062093" className="flex items-start gap-2.5 text-[13px] text-white/55 transition-colors duration-150 hover:text-white">
                <Phone className="mt-0.5 size-4 shrink-0 text-[#4a9a5a]" />
                +91 90030 62093
              </a>
              <a href="mailto:info@evergreenindustry.com" className="flex items-start gap-2.5 text-[13px] text-white/55 transition-colors duration-150 hover:text-white">
                <Mail className="mt-0.5 size-4 shrink-0 text-[#4a9a5a]" />
                info@evergreenindustry.com
              </a>
              <div className="flex items-start gap-2.5 text-[13px] text-white/55">
                <MapPin className="mt-0.5 size-4 shrink-0 text-[#4a9a5a]" />
                Coimbatore, Tamil Nadu, India
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div className="border-t border-white/[0.08]">
        <div className="mx-auto flex max-w-[1420px] flex-wrap items-center justify-between gap-3 px-6 py-5 lg:px-12">
          <p className="text-[12px] text-white/30">© 2026 Evergreen Industry. All Rights Reserved.</p>
          <div className="flex items-center gap-6">
            <a href="#" className="text-[12px] text-white/30 transition-colors hover:text-white/60">Privacy Policy</a>
            <a href="#" className="text-[12px] text-white/30 transition-colors hover:text-white/60">Terms &amp; Conditions</a>
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
      className={`mb-3 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] ${
        light ? "text-emerald-300" : "text-[#1e5c2e]"
      }`}
    >
      <span className={`inline-block h-2 w-2 rounded-full ${light ? "bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]" : "bg-[#1e5c2e]"}`} />
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

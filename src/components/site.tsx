import { Link, useRouterState } from "@tanstack/react-router";
import { AnimatePresence, motion, useScroll, useTransform } from "motion/react";
import { ArrowRight, ArrowUpRight, CheckCircle2, Leaf, Mail, MapPin, Menu, Phone, ShieldCheck, Sparkles, X } from "lucide-react";
import { useState, type ReactNode } from "react";
import { Button } from "@/components/ui/button";
import heroImage from "@/assets/evergreen-hero.jpg";
import productsImage from "@/assets/product-collection.jpg";
import manufacturingImage from "@/assets/manufacturing.jpg";
import materialsImage from "@/assets/material-journey.jpg";

export { heroImage, productsImage, manufacturingImage, materialsImage };

const nav = [
  ["/", "Home"], ["/about", "About Us"], ["/products", "Products"],
  ["/certificate", "Certificate"], ["/segment", "Segment"],
  ["/sustainability", "Sustainability"], ["/contact", "Contact Us"],
] as const;

export function SiteHeader() {
  const [open, setOpen] = useState(false);
  return <header className="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-[#2d5016]/90 text-cream backdrop-blur-xl">
    <div className="mx-auto flex h-20 max-w-[1500px] items-center justify-between px-5 lg:px-10">
      <Link to="/" className="flex min-w-0 items-center gap-3" onClick={() => setOpen(false)}>
        <span className="grid size-10 shrink-0 place-items-center rounded-full border border-sage/40 bg-cream/10"><Leaf className="size-5" /></span>
        <span className="truncate text-sm font-bold tracking-[0.12em]">EVERGREENINDUSTRY</span>
      </Link>
      <nav className="hidden items-center gap-6 xl:flex">
        {nav.map(([to,label]) => (
          <Link
            key={to}
            to={to}
            className="text-xs font-semibold text-cream/75 transition-colors hover:text-cream"
            activeProps={{className:"text-cream font-bold underline decoration-[#9cb99e] decoration-2 underline-offset-8"}}
          >
            {label}
          </Link>
        ))}
      </nav>
      <div className="hidden xl:flex items-center gap-4">
        <Button
          asChild
          size="sm"
          className="rounded-full px-5 h-9 bg-white text-[#1a3321] hover:bg-[#e4ece4] text-xs font-semibold tracking-wider transition-all shadow-sm"
        >
          <Link to="/contact">Enquire Now</Link>
        </Button>
      </div>
      <Button variant="ghost" size="icon" className="text-cream xl:hidden" onClick={() => setOpen(v => !v)} aria-label="Toggle navigation">{open ? <X/> : <Menu/>}</Button>
    </div>
    <AnimatePresence>{open && <motion.nav initial={{height:0,opacity:0}} animate={{height:"auto",opacity:1}} exit={{height:0,opacity:0}} className="overflow-hidden border-t border-cream/10 bg-[#2d5016] px-5 xl:hidden">
      <div className="flex flex-col py-4">
        {nav.map(([to,label]) => <Link key={to} to={to} onClick={() => setOpen(false)} className="border-b border-cream/10 py-3 text-sm">{label}</Link>)}
        <div className="pt-4">
          <Button asChild className="w-full rounded-full bg-white text-[#1a3321] text-xs font-semibold">
            <Link to="/contact" onClick={() => setOpen(false)}>Enquire Now</Link>
          </Button>
        </div>
      </div>
    </motion.nav>}</AnimatePresence>
  </header>;
}

export function SiteFooter() {
  return (
    <>
      {/* 1. STANDALONE PRE-FOOTER CTA SECTION — Clean background with generous breathing space */}
      <section className="relative py-20 sm:py-24 lg:py-32 px-6 sm:px-10 lg:px-16 bg-[#eef4ee] border-t border-[#c8dac8]/60 overflow-hidden">
        {/* Soft background ambient glow */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[500px] bg-[#7da482]/10 blur-3xl rounded-full pointer-events-none" />

        <div className="relative z-10 mx-auto max-w-[1500px]">
          {/* 3D PILLAR-CURVED CONTAINER — Signature Evergreen Green Theme (#2d5016 / #355f1c) */}
          <motion.div
            initial={{ opacity: 0, y: 35, rotateX: 6 }}
            whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7, ease: "easeOut" }}
            whileHover={{ y: -8, rotateX: -2, scale: 1.008 }}
            style={{ transformStyle: "preserve-3d", perspective: 1200 }}
            className="group relative rounded-[2.75rem] sm:rounded-[3.25rem] lg:rounded-[4rem] p-8 sm:p-12 lg:p-16 overflow-hidden
              bg-gradient-to-br from-[#355f1c] via-[#2d5016] to-[#203c10]
              border border-[#4c7a2c]/60
              shadow-[0_30px_90px_-15px_rgba(45,80,22,0.45),0_15px_40px_-10px_rgba(45,80,22,0.3),inset_0_1px_0_rgba(255,255,255,0.3),inset_0_-1px_0_rgba(0,0,0,0.4)]
              hover:shadow-[0_45px_110px_-15px_rgba(45,80,22,0.6),0_20px_50px_-10px_rgba(45,80,22,0.45),inset_0_1px_0_rgba(255,255,255,0.4)]
              hover:border-[#7da482]/80
              transition-all duration-500"
          >
            {/* Top Pillar Bevel Highlight Line */}
            <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent pointer-events-none" />
            {/* Bottom specular reflection */}
            <div className="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-[#7da482]/30 to-transparent pointer-events-none" />

            {/* 3D Ambient Glowing Light Orbs */}
            <div className="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-[#7da482]/25 blur-3xl pointer-events-none group-hover:bg-[#7da482]/35 group-hover:scale-110 transition-all duration-700" />
            <div className="absolute -bottom-24 -left-20 w-80 h-80 rounded-full bg-[#3a6420]/40 blur-3xl pointer-events-none" />
            {/* Subtle micro dot-grid depth texture */}
            <div
              className="absolute inset-0 opacity-[0.05] pointer-events-none"
              style={{ backgroundImage: "radial-gradient(circle, #9cb99e 1px, transparent 1px)", backgroundSize: "24px 24px" }}
            />

            <div className="relative z-10 grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">
              {/* Left Pillar Content */}
              <div className="lg:col-span-7 space-y-4">
                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 backdrop-blur-md text-[11px] font-semibold uppercase tracking-[0.2em] text-[#9cb99e] shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
                  <span className="w-2 h-2 rounded-full bg-[#7da482] animate-pulse" />
                  <span>B2B Procurement & Custom Supply</span>
                </div>

                <h3 className="font-display text-3xl sm:text-4xl lg:text-5xl text-[#fbf8f3] leading-[1.15] drop-shadow-sm">
                  Ready to transition away from{" "}
                  <span className="font-serif italic font-normal text-[#9cb99e]">
                    petroleum plastics?
                  </span>
                </h3>

                <p className="text-sm sm:text-base text-[#fbf8f3]/80 font-light max-w-2xl leading-relaxed">
                  Request testing swatches, schedule a volume quotation, or review our physical CIPET, ASTM D6400, and EN 13432 compostability dossiers.
                </p>

                {/* Pillar Badges along bottom of card */}
                <div className="flex flex-wrap items-center gap-2.5 pt-2">
                  <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[10px] font-mono text-[#9cb99e]">
                    <CheckCircle2 className="size-3 text-[#7da482]" />
                    <span>ASTM D6400 & EN 13432 Certified</span>
                  </div>
                  <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[10px] font-mono text-[#9cb99e]">
                    <ShieldCheck className="size-3 text-[#7da482]" />
                    <span>CIPET Tested Films</span>
                  </div>
                  <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[10px] font-mono text-[#9cb99e]">
                    <Leaf className="size-3 text-[#7da482]" />
                    <span>Direct Factory Supply</span>
                  </div>
                </div>
              </div>

              {/* Right Pillar Interactive 3D Buttons */}
              <div className="lg:col-span-5 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 lg:justify-end">
                <Button
                  asChild
                  className="h-14 px-8 rounded-full bg-gradient-to-r from-[#fbf8f3] via-white to-[#e8ede4] text-[#0c1e13] hover:bg-[#9cb99e] hover:text-[#0c1e13] font-semibold text-xs uppercase tracking-[0.16em] transition-all duration-300 shadow-[0_10px_30px_rgba(0,0,0,0.35),inset_0_1px_0_rgba(255,255,255,0.9)] hover:shadow-[0_16px_36px_rgba(125,164,130,0.4)] hover:-translate-y-0.5 group shrink-0"
                >
                  <Link to="/contact" className="flex items-center justify-center gap-2.5">
                    <span>Request Sample Kit</span>
                    <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" />
                  </Link>
                </Button>

                <Button
                  asChild
                  variant="outline"
                  className="h-14 px-7 rounded-full border-white/30 bg-white/10 backdrop-blur-md text-[#fbf8f3] hover:bg-white/20 hover:border-white/50 font-medium text-xs uppercase tracking-[0.16em] transition-all duration-300 hover:-translate-y-0.5 shadow-[0_4px_15px_rgba(0,0,0,0.2)] shrink-0"
                >
                  <Link to="/products">Browse Catalog</Link>
                </Button>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* 2. DEDICATED MAIN FOOTER SECTION */}
      <footer className="relative bg-[#2d5016] text-[#fbf8f3] border-t border-white/10 overflow-hidden pt-20 pb-12">
        {/* Subtle top ambient glow */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-28 bg-gradient-to-b from-[#7da482]/15 to-transparent pointer-events-none" />

        <div className="relative z-10 mx-auto max-w-[1500px] px-6 sm:px-10 lg:px-16">
          {/* MAIN 4-COLUMN BALANCED FOOTER GRID */}
          <div className="grid gap-12 lg:grid-cols-12 pb-16">
          {/* Col 1: Brand & Origin (4 cols) */}
          <div className="lg:col-span-4 space-y-6">
            <Link to="/" className="inline-flex items-center gap-3.5 group">
              <span className="grid size-11 place-items-center rounded-full border border-[#7da482]/40 bg-[#3a6420] text-[#9cb99e] shadow-md transition-transform duration-300 group-hover:scale-105">
                <Leaf className="size-5 text-[#9cb99e]" />
              </span>
              <div className="flex flex-col">
                <span className="font-display text-2xl tracking-wide text-[#fbf8f3]">
                  EVERGREEN<span className="italic font-normal text-[#9cb99e]">INDUSTRY</span>
                </span>
                <span className="text-[10px] font-mono tracking-[0.2em] text-[#9cb99e]/80 uppercase">
                  Biopolymers & Packaging
                </span>
              </div>
            </Link>

            <p className="text-sm text-[#fbf8f3]/70 font-light leading-relaxed max-w-sm">
              Crafted from renewable cassava and corn biopolymers. Engineered with commercial tensile durability to replace single-use petroleum plastics without microplastics.
            </p>

            <div className="space-y-2 pt-1">
              <div className="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs text-[#9cb99e]">
                <span className="size-2 rounded-full bg-[#7da482] animate-pulse" />
                <span className="font-mono text-[11px] tracking-wide">Tamil Nadu Blown Film Extrusion Facility</span>
              </div>
              <p className="text-xs text-[#fbf8f3]/50 pl-1">
                Zero fossil feedstocks • 100% organic soil assimilation
              </p>
            </div>
          </div>

          {/* Col 2: Navigation (2 cols) */}
          <div className="lg:col-span-2 sm:col-span-6 space-y-4">
            <p className="text-xs font-bold uppercase tracking-[0.22em] text-[#9cb99e]">
              Explore
            </p>
            <ul className="space-y-2.5 text-sm text-[#fbf8f3]/75 font-light">
              {nav.map(([to, label]) => (
                <li key={to}>
                  <Link
                    to={to}
                    className="inline-flex items-center gap-1.5 transition-colors duration-200 hover:text-[#fbf8f3] hover:translate-x-0.5"
                  >
                    <span>{label}</span>
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Col 3: Packaging Solutions (3 cols) */}
          <div className="lg:col-span-3 sm:col-span-6 space-y-4">
            <p className="text-xs font-bold uppercase tracking-[0.22em] text-[#9cb99e]">
              Packaging Series
            </p>
            <ul className="space-y-2.5 text-sm text-[#fbf8f3]/75 font-light">
              <li>
                <Link to="/products" className="transition-colors hover:text-[#fbf8f3] block">
                  D-Cut Compostable Carry Bags
                </Link>
              </li>
              <li>
                <Link to="/products" className="transition-colors hover:text-[#fbf8f3] block">
                  Soft-Loop Boutique Retail Totes
                </Link>
              </li>
              <li>
                <Link to="/products" className="transition-colors hover:text-[#fbf8f3] block">
                  Heavy-Duty Organic Waste Roll Bags
                </Link>
              </li>
              <li>
                <Link to="/products" className="transition-colors hover:text-[#fbf8f3] block">
                  Breathable Farm & Produce Pouches
                </Link>
              </li>
              <li>
                <Link to="/certificate" className="transition-colors hover:text-[#fbf8f3] block text-[#9cb99e] font-medium">
                  Govt. CPCB Certified Compliance
                </Link>
              </li>
              <li>
                <Link to="/sustainability" className="transition-colors hover:text-[#fbf8f3] block">
                  Bio-Resin Granules & Custom Blends
                </Link>
              </li>
            </ul>
          </div>

          {/* Col 4: Direct Inquiries & Logistics (3 cols) */}
          <div className="lg:col-span-3 space-y-4">
            <p className="text-xs font-bold uppercase tracking-[0.22em] text-[#9cb99e]">
              Direct Inquiries
            </p>
            <div className="space-y-3 text-sm text-[#fbf8f3]/80">
              <a
                href="mailto:hello@evergreenindustry.com"
                className="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:border-[#7da482]/50 transition-colors group"
              >
                <Mail className="size-4 text-[#7da482] mt-0.5 shrink-0 transition-transform group-hover:scale-110" />
                <div>
                  <span className="font-medium text-[#fbf8f3] block text-xs">hello@evergreenindustry.com</span>
                  <span className="text-[11px] text-[#fbf8f3]/50">Direct RFQ & Commercial Desk</span>
                </div>
              </a>

              <a
                href="tel:+919840012345"
                className="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:border-[#7da482]/50 transition-colors group"
              >
                <Phone className="size-4 text-[#7da482] mt-0.5 shrink-0 transition-transform group-hover:scale-110" />
                <div>
                  <span className="font-medium text-[#fbf8f3] block text-xs">+91 (0) 422 298 4500</span>
                  <span className="text-[11px] text-[#fbf8f3]/50">Mon – Sat, 9:00 AM – 6:30 PM IST</span>
                </div>
              </a>

              <div className="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                <MapPin className="size-4 text-[#7da482] mt-0.5 shrink-0" />
                <div className="text-xs text-[#fbf8f3]/65 leading-relaxed">
                  <span className="font-medium text-[#fbf8f3] block text-[11px] mb-0.5">Manufacturing & Logistics</span>
                  Industrial Estate, Coimbatore & Chennai logistics corridor, Tamil Nadu 641021, India.
                </div>
              </div>
            </div>
          </div>
        </div>



        {/* 4. BOTTOM SUBFOOTER */}
        <div className="pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#fbf8f3]/50">
          <p>© 2026 EVERGREENINDUSTRY Private Limited. All rights reserved.</p>
          <p className="italic font-serif text-sm text-[#9cb99e]">
            Designed for a circular tomorrow.
          </p>
          <div className="flex items-center gap-5 text-[11px] text-[#fbf8f3]/50">
            <Link to="/about" className="hover:text-[#fbf8f3] transition-colors">Privacy Policy</Link>
            <span>•</span>
            <Link to="/about" className="hover:text-[#fbf8f3] transition-colors">Terms of Supply</Link>
            <span>•</span>
            <Link to="/contact" className="hover:text-[#fbf8f3] transition-colors">Spec Sheets</Link>
          </div>
        </div>
      </div>
    </footer>
    </>
  );
}

export function PageTransition({ children }: { children: ReactNode }) {
  const pathname = useRouterState({select:s=>s.location.pathname});
  return <motion.main key={pathname} initial={{opacity:0}} animate={{opacity:1}} transition={{duration:.55}}>{children}</motion.main>;
}

export function Reveal({ children, className="", delay=0 }: {children:ReactNode;className?:string;delay?:number}) {
  return <motion.div className={className} initial={{opacity:0,y:32}} whileInView={{opacity:1,y:0}} viewport={{once:true,margin:"-80px"}} transition={{duration:.75,delay,ease:[.22,1,.36,1]}}>{children}</motion.div>;
}

export function Eyebrow({children}:{children:ReactNode}) { return <p className="mb-5 text-xs font-bold uppercase tracking-[0.2em] text-moss">{children}</p>; }

export function PageHero({eyebrow,title,copy,image=heroImage}:{eyebrow:string;title:string;copy:string;image?:string}) {
  return <section className="relative min-h-[72vh] overflow-hidden bg-[#2d5016] text-cream">
    <img src={image} alt="Sustainable packaging by Evergreen Industry" width={1920} height={1280} className="absolute inset-0 h-full w-full object-cover opacity-85 cinematic-zoom" />
    <div className="absolute inset-0 bg-gradient-to-r from-[#2d5016]/75 via-[#2d5016]/30 to-transparent" />
    <div className="relative mx-auto flex min-h-[72vh] max-w-[1450px] items-end px-5 pb-20 pt-36 lg:px-10 lg:pb-24"><Reveal className="max-w-4xl"><p className="mb-6 text-xs font-bold uppercase tracking-[0.22em] text-sage">{eyebrow}</p><h1 className="max-w-4xl text-5xl leading-[.98] sm:text-7xl lg:text-[6.5rem]">{title}</h1><p className="mt-7 max-w-2xl text-base leading-7 text-cream/75 sm:text-lg">{copy}</p></Reveal></div>
  </section>;
}

export function SectionTitle({eyebrow,title,copy}:{eyebrow:string;title:string;copy?:string}) { return <Reveal className="max-w-3xl"><Eyebrow>{eyebrow}</Eyebrow><h2 className="text-4xl leading-[1.05] text-forest sm:text-6xl">{title}</h2>{copy && <p className="mt-6 max-w-2xl leading-7 text-muted-foreground">{copy}</p>}</Reveal>; }

export function FinalCTA() {
  return null;
}

export function ImagePanel({src,alt,className=""}:{src:string;alt:string;className?:string}) { return <div className={`group overflow-hidden rounded-[2rem] ${className}`}><img src={src} alt={alt} loading="lazy" width={1600} height={1200} className="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"/></div>; }

export function Metric({value,label}:{value:string;label:string}) { return <Reveal><p className="font-display text-5xl text-forest sm:text-6xl">{value}</p><p className="mt-2 text-sm text-muted-foreground">{label}</p></Reveal>; }
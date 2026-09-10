import { Link, useRouterState } from "@tanstack/react-router";
import { AnimatePresence, motion, useScroll, useTransform } from "motion/react";
import { ArrowRight, Leaf, Menu, X } from "lucide-react";
import { useState, type ReactNode } from "react";
import { Button } from "@/components/ui/button";
import heroImage from "@/assets/evergreen-hero.jpg";
import productsImage from "@/assets/product-collection.jpg";
import manufacturingImage from "@/assets/manufacturing.jpg";
import materialsImage from "@/assets/material-journey.jpg";

export { heroImage, productsImage, manufacturingImage, materialsImage };

const nav = [
  ["/", "Home"], ["/about", "About"], ["/products", "Products"],
  ["/applications", "Solutions"], ["/sustainability", "Sustainability"],
  ["/why-evergreen", "Why Us"], ["/contact", "Contact"],
] as const;

export function SiteHeader() {
  const [open, setOpen] = useState(false);
  return <header className="fixed inset-x-0 top-0 z-50 border-b border-cream/15 bg-forest/80 text-cream backdrop-blur-xl">
    <div className="mx-auto grid h-20 max-w-[1500px] grid-cols-[minmax(0,1fr)_auto] items-center px-5 lg:px-10">
      <Link to="/" className="flex min-w-0 items-center gap-3" onClick={() => setOpen(false)}>
        <span className="grid size-10 shrink-0 place-items-center rounded-full border border-sage/40 bg-cream/10"><Leaf className="size-5" /></span>
        <span className="truncate text-sm font-bold tracking-[0.12em]">EVERGREENINDUSTRY</span>
      </Link>
      <nav className="hidden items-center gap-6 xl:flex">
        {nav.map(([to,label]) => <Link key={to} to={to} className="text-xs font-semibold text-cream/75 transition-colors hover:text-cream" activeProps={{className:"text-cream"}}>{label}</Link>)}
      </nav>
      <Button variant="ghost" size="icon" className="text-cream xl:hidden" onClick={() => setOpen(v => !v)} aria-label="Toggle navigation">{open ? <X/> : <Menu/>}</Button>
    </div>
    <AnimatePresence>{open && <motion.nav initial={{height:0,opacity:0}} animate={{height:"auto",opacity:1}} exit={{height:0,opacity:0}} className="overflow-hidden border-t border-cream/10 bg-forest px-5 xl:hidden">
      <div className="flex flex-col py-4">{nav.map(([to,label]) => <Link key={to} to={to} onClick={() => setOpen(false)} className="border-b border-cream/10 py-3 text-sm">{label}</Link>)}</div>
    </motion.nav>}</AnimatePresence>
  </header>;
}

export function SiteFooter() {
  return <footer className="bg-ink px-5 py-16 text-cream lg:px-10"><div className="mx-auto grid max-w-[1450px] gap-12 lg:grid-cols-[1.4fr_1fr_1fr]">
    <div><div className="mb-5 flex items-center gap-3"><Leaf/><strong className="tracking-[0.12em]">EVERGREENINDUSTRY</strong></div><p className="max-w-md text-sm leading-7 text-cream/60">Thoughtful materials. Dependable performance. A more responsible future for everyday packaging.</p></div>
    <div><p className="mb-4 text-xs font-bold uppercase tracking-[0.16em] text-sage">Navigate</p><div className="grid grid-cols-2 gap-3 text-sm text-cream/70">{nav.slice(1).map(([to,label]) => <Link key={to} to={to} className="hover:text-cream">{label}</Link>)}</div></div>
    <div><p className="mb-4 text-xs font-bold uppercase tracking-[0.16em] text-sage">Start a conversation</p><p className="text-sm text-cream/70">hello@evergreenindustry.com</p><p className="mt-2 text-sm text-cream/70">+91 00000 00000</p></div>
  </div><div className="mx-auto mt-14 flex max-w-[1450px] flex-wrap justify-between gap-3 border-t border-cream/10 pt-6 text-xs text-cream/40"><span>© 2026 EVERGREENINDUSTRY</span><span>Designed for a greener tomorrow.</span></div></footer>;
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
  return <section className="relative min-h-[72vh] overflow-hidden bg-forest text-cream">
    <img src={image} alt="Sustainable packaging by Evergreen Industry" width={1920} height={1280} className="absolute inset-0 h-full w-full object-cover opacity-55 cinematic-zoom" />
    <div className="absolute inset-0 bg-gradient-to-r from-forest via-forest/65 to-transparent" />
    <div className="relative mx-auto flex min-h-[72vh] max-w-[1450px] items-end px-5 pb-20 pt-36 lg:px-10 lg:pb-24"><Reveal className="max-w-4xl"><p className="mb-6 text-xs font-bold uppercase tracking-[0.22em] text-sage">{eyebrow}</p><h1 className="max-w-4xl text-5xl leading-[.98] sm:text-7xl lg:text-[6.5rem]">{title}</h1><p className="mt-7 max-w-2xl text-base leading-7 text-cream/75 sm:text-lg">{copy}</p></Reveal></div>
  </section>;
}

export function SectionTitle({eyebrow,title,copy}:{eyebrow:string;title:string;copy?:string}) { return <Reveal className="max-w-3xl"><Eyebrow>{eyebrow}</Eyebrow><h2 className="text-4xl leading-[1.05] text-forest sm:text-6xl">{title}</h2>{copy && <p className="mt-6 max-w-2xl leading-7 text-muted-foreground">{copy}</p>}</Reveal>; }

export function FinalCTA() { return <section className="bg-moss px-5 py-24 text-cream lg:px-10"><Reveal className="mx-auto flex max-w-[1250px] flex-col items-start justify-between gap-8 lg:flex-row lg:items-end"><div><p className="mb-5 text-xs font-bold uppercase tracking-[.2em] text-sage">Move forward, responsibly</p><h2 className="max-w-3xl text-5xl leading-none sm:text-7xl">Make your next bag a better one.</h2></div><Button asChild variant="hero" size="lg"><Link to="/contact">Talk to our team <ArrowRight/></Link></Button></Reveal></section>; }

export function ImagePanel({src,alt,className=""}:{src:string;alt:string;className?:string}) { return <div className={`group overflow-hidden rounded-[2rem] ${className}`}><img src={src} alt={alt} loading="lazy" width={1600} height={1200} className="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"/></div>; }

export function Metric({value,label}:{value:string;label:string}) { return <Reveal><p className="font-display text-5xl text-forest sm:text-6xl">{value}</p><p className="mt-2 text-sm text-muted-foreground">{label}</p></Reveal>; }
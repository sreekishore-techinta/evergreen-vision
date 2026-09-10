import { createFileRoute } from "@tanstack/react-router";
import { Link } from "@tanstack/react-router";
import { motion, useScroll, useTransform } from "motion/react";
import { ArrowDown, ArrowRight, Check, Leaf, PackageOpen, Recycle } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Eyebrow, FinalCTA, ImagePanel, Metric, Reveal, SectionTitle, heroImage, manufacturingImage, productsImage } from "@/components/site";

// No head() here: the home route inherits title/description/og/twitter from
// __root.tsx, and ships no og:image so serve-time hosting can inject the
// project's social preview (explicit og:image or latest screenshot).
export const Route = createFileRoute("/")({
  head: () => ({ meta: [
    { title: "EVERGREENINDUSTRY | Better Packaging" },
    { name: "description", content: "Premium biodegradable and compostable carry bags for responsible modern businesses." },
    { property: "og:title", content: "EVERGREENINDUSTRY | Better Packaging" },
    { property: "og:description", content: "Sustainable alternatives without compromising everyday performance." },
    { property: "og:type", content: "website" }, { name: "twitter:card", content: "summary_large_image" },
  ]}),
  component: Index,
});

// IMPORTANT: Replace this placeholder. See ./README.md for routing conventions.
function Index() {
  const { scrollY } = useScroll();
  const heroY = useTransform(scrollY, [0, 900], [0, 150]);
  return (
    <main>
      <section className="relative min-h-[92vh] overflow-hidden bg-forest text-cream">
        <motion.img style={{y:heroY}} src={heroImage} width={1920} height={1280} alt="Biodegradable carry bag in a thriving forest" className="absolute inset-0 h-[110%] w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-forest via-forest/50 to-transparent" />
        <div className="absolute left-[8%] top-[23%] size-2 rounded-full bg-sage/60 organic-drift"/><div className="absolute left-[42%] top-[35%] size-1 rounded-full bg-cream/70 organic-drift"/>
        <div className="relative mx-auto flex min-h-[92vh] max-w-[1500px] items-end px-5 pb-20 pt-32 lg:px-10 lg:pb-24">
          <motion.div initial={{opacity:0,y:40}} animate={{opacity:1,y:0}} transition={{duration:1,ease:[.22,1,.36,1]}} className="max-w-4xl">
            <p className="mb-6 text-xs font-bold uppercase tracking-[.22em] text-sage">Packaging, reimagined by nature</p>
            <h1 className="text-6xl leading-[.88] sm:text-8xl lg:text-[7.5rem]">Better Packaging.<br/><span className="italic text-sage">A Greener Tomorrow.</span></h1>
            <p className="mt-8 max-w-xl text-base leading-7 text-cream/75 sm:text-lg">Sustainable alternatives to conventional plastic—engineered for usability, durability and the realities of modern business.</p>
            <div className="mt-8 flex flex-wrap gap-3"><Button asChild variant="hero" size="lg"><Link to="/products">Explore products <ArrowRight/></Link></Button><Button asChild variant="glass" size="lg"><Link to="/about">Discover our story</Link></Button></div>
          </motion.div>
          <ArrowDown className="absolute bottom-8 right-8 hidden animate-bounce text-cream/60 lg:block"/>
        </div>
      </section>

      <section className="px-5 py-24 lg:px-10 lg:py-36"><div className="mx-auto grid max-w-[1350px] gap-14 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
        <SectionTitle eyebrow="Our point of view" title="Made for today. Mindful of what comes next."/>
        <Reveal><p className="max-w-2xl text-xl leading-9 text-muted-foreground sm:text-2xl">EVERGREENINDUSTRY develops practical packaging that serves businesses now while moving material choices in a more responsible direction.</p></Reveal>
      </div></section>

      <section className="bg-forest px-5 py-24 text-cream lg:px-10 lg:py-32"><div className="mx-auto grid max-w-[1450px] gap-14 lg:grid-cols-2 lg:items-center">
        <ImagePanel src={productsImage} alt="Evergreen sustainable packaging collection" className="aspect-[4/3]"/>
        <Reveal><p className="mb-5 text-xs font-bold uppercase tracking-[.2em] text-sage">Featured collection</p><h2 className="text-5xl leading-none sm:text-7xl">One material shift.<br/>Many possibilities.</h2><p className="mt-7 max-w-xl leading-7 text-cream/65">Carry, shopping, waste and food packaging solutions designed around the moments businesses rely on every day.</p><Button asChild variant="glass" size="lg" className="mt-8"><Link to="/products">Explore the range <ArrowRight/></Link></Button></Reveal>
      </div></section>

      <section className="px-5 py-24 lg:px-10 lg:py-36"><div className="mx-auto max-w-[1350px]"><SectionTitle eyebrow="Why Evergreen" title="Responsibility that performs." copy="We balance environmental intent with the quality, consistency and practical durability your operations demand."/><div className="mt-16 grid gap-px overflow-hidden rounded-[2rem] bg-border md:grid-cols-3">
        {[ [Leaf,"Material intelligence","Thoughtful material choices for reduced conventional plastic dependency."],[Check,"Reliable quality","Consistent production standards built for daily commercial use."],[Recycle,"Circular thinking","A lifecycle-led approach from raw material to responsible disposal."]].map(([Icon,title,copy],i)=><Reveal key={String(title)} delay={i*.1} className="bg-background p-9"><Icon className="mb-10 size-7 text-moss"/><h3 className="text-3xl text-forest">{String(title)}</h3><p className="mt-4 leading-7 text-muted-foreground">{String(copy)}</p></Reveal>)}
      </div></div></section>

      <section className="bg-secondary px-5 py-24 lg:px-10 lg:py-32"><div className="mx-auto grid max-w-[1450px] gap-14 lg:grid-cols-[1.1fr_.9fr] lg:items-center"><Reveal><Eyebrow>Responsible manufacturing</Eyebrow><h2 className="text-5xl leading-none text-forest sm:text-7xl">Precision, with a lighter footprint.</h2><p className="mt-7 max-w-xl leading-7 text-muted-foreground">Our manufacturing mindset connects material innovation, process discipline and quality control—so sustainable choices remain commercially practical.</p><div className="mt-10 grid grid-cols-3 gap-6"><Metric value="01" label="Material first"/><Metric value="02" label="Quality always"/><Metric value="03" label="Impact aware"/></div></Reveal><ImagePanel src={manufacturingImage} alt="Responsible packaging manufacturing" className="aspect-[4/5]"/></div></section>

      <section className="px-5 py-24 lg:px-10"><div className="mx-auto max-w-[1350px]"><SectionTitle eyebrow="Across industries" title="Designed around real-world use."/><div className="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">{["Retail & grocery","Food & restaurants","Hospitality & events","Waste management"].map((x,i)=><Reveal key={x} delay={i*.08} className="group min-h-64 rounded-[2rem] border border-border bg-card p-7 transition-all duration-500 hover:-translate-y-2 hover:shadow-elevated"><PackageOpen className="mb-20 text-moss"/><h3 className="text-2xl text-forest">{x}</h3><ArrowRight className="mt-4 transition-transform group-hover:translate-x-2"/></Reveal>)}</div></div></section>
      <FinalCTA/>
    </main>
  );
}

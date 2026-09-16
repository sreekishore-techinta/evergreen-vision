import { createFileRoute, Link } from "@tanstack/react-router";
import { CheckCircle2, Factory, Layers, Leaf, ShieldCheck, Sparkles } from "lucide-react";
import { motion } from "motion/react";
import { ImagePanel, Metric, Reveal, SectionTitle, materialsImage } from "@/components/site";

export const Route = createFileRoute("/about")({
  head: () => ({
    meta: [
      { title: "About Us | EVERGREENINDUSTRY" },
      {
        name: "description",
        content: "Our story of sustainable innovation, circular materials, and responsible packaging manufacturing.",
      },
      { property: "og:title", content: "About EVERGREENINDUSTRY" },
      { property: "og:description", content: "Purpose, process and progress in sustainable packaging." },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
  }),
  component: About,
});

function About() {
  return (
    <div className="bg-[#eef4ee] min-h-screen">
      {/* ==================================================================== */}
      {/* 1. ABOUT HERO — VIBRANT & BRIGHT (ZERO DARK SHADES)                  */}
      {/* ==================================================================== */}
      <section className="relative pt-32 pb-20 lg:pt-40 lg:pb-28 px-6 sm:px-10 lg:px-16 border-b border-[#c8dac8]/60 overflow-hidden">
        {/* Subtle background ambient light */}
        <div className="absolute -top-24 right-0 w-[500px] h-[500px] rounded-full bg-[#7da482]/15 blur-3xl pointer-events-none" />

        <div className="relative mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            {/* Left Narrative */}
            <div className="lg:col-span-6 space-y-6">
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-[#b8d4b8] text-[11px] font-mono uppercase tracking-[0.22em] text-[#24452e] shadow-sm">
                <span className="w-2 h-2 rounded-full bg-[#4a7354] animate-pulse" />
                <span>Our Heritage & Purpose</span>
              </div>

              <h1 className="font-display text-4xl sm:text-6xl lg:text-[4.25rem] text-[#0e2617] leading-[1.02] tracking-tight">
                Industry can grow{" "}
                <span className="font-serif italic font-normal text-[#355a3f]">
                  differently.
                </span>
              </h1>

              <p className="text-base sm:text-lg text-[#526456] font-light leading-relaxed max-w-xl">
                We believe better material decisions can support both commercial business performance and the living world beyond it. Built to provide an honest, scalable path away from petroleum polymers.
              </p>

              {/* Quick Trust Pills */}
              <div className="flex flex-wrap gap-2.5 pt-2">
                <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                  <Factory className="size-3.5 text-[#4a7354]" />
                  <span>State-of-the-art Facility</span>
                </div>
                <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                  <Leaf className="size-3.5 text-[#4a7354]" />
                  <span>100% Bio-Derived Starch</span>
                </div>
                <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                  <ShieldCheck className="size-3.5 text-[#4a7354]" />
                  <span>ISO & ASTM Certified</span>
                </div>
              </div>
            </div>

            {/* Right Facility Video Showcase — Premium Video Hero Card */}
            <div className="lg:col-span-6 relative">
              <motion.div
                initial={{ opacity: 0, scale: 0.96 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, ease: "easeOut" }}
                whileHover={{ y: -6, scale: 1.01 }}
                style={{ transformStyle: "preserve-3d", perspective: 1000 }}
                className="group relative rounded-[2.5rem] overflow-hidden bg-black border border-white/80 shadow-[0_25px_60px_-15px_rgba(20,50,25,0.28),0_10px_25px_-10px_rgba(20,50,25,0.15)] aspect-[16/11]"
              >
                {/* Top specular highlight edge for 3D depth */}
                <div className="absolute inset-x-0 top-0 h-px z-10 bg-gradient-to-r from-transparent via-white/80 to-transparent pointer-events-none" />

                {/* Auto-play muted video */}
                <video
                  src="/about-hero.mp4"
                  autoPlay
                  muted
                  loop
                  playsInline
                  preload="auto"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                />

                {/* Subtle vignette for depth without darkening */}
                <div className="absolute inset-0 rounded-[2.5rem] shadow-[inset_0_0_60px_rgba(0,0,0,0.12)] pointer-events-none" />

                {/* Live indicator — top right */}
                <div className="absolute top-4 right-4 z-10 flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-black/40 backdrop-blur-md border border-white/20">
                  <span className="size-1.5 rounded-full bg-red-400 animate-pulse" />
                  <span className="text-[10px] font-mono uppercase tracking-widest text-white/80">Live</span>
                </div>

                {/* Floating glass pill badge */}
                <div className="absolute bottom-4 left-4 sm:bottom-6 sm:left-6 z-10 px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white/60 shadow-lg text-xs font-mono text-[#0e2617] flex items-center gap-2.5">
                  <span className="size-2 rounded-full bg-[#4a7354]" />
                  <span>Tamil Nadu Blown Film Extrusion</span>
                </div>
              </motion.div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 2. WHY WE EXIST SECTION                                              */}
      {/* ==================================================================== */}
      <section className="px-6 sm:px-10 lg:px-16 py-24 lg:py-36">
        <div className="mx-auto grid max-w-[1350px] gap-16 lg:grid-cols-2 lg:items-center">
          <SectionTitle
            eyebrow="Why we exist"
            title="A practical path beyond conventional plastic."
            copy="EVERGREENINDUSTRY was built around a clear idea: environmental responsibility should not come at the cost of everyday performance. We develop biodegradable and compostable packaging with real commercial needs in mind."
          />
          <ImagePanel src={materialsImage} alt="Biodegradable material research" className="aspect-square shadow-xl border border-[#c8dac8]" />
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 3. 3D ANIMATED METRICS GRID                                          */}
      {/* ==================================================================== */}
      <section className="relative py-20 lg:py-28 px-6 sm:px-10 lg:px-16 bg-[#e5f0e5] border-y border-[#c8dac8] overflow-hidden">
        {/* Soft background ambient glow */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-[#7da482]/10 blur-3xl rounded-full pointer-events-none" />

        <div className="relative z-10 mx-auto max-w-[1450px] space-y-10">
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div className="space-y-2 max-w-xl">
              <span className="text-xs font-mono uppercase tracking-[0.25em] font-bold text-[#355a3f]">
                Demonstrated Standards
              </span>
              <h2 className="font-display text-3xl sm:text-4xl text-[#0e2617] tracking-tight">
                Precision in numbers. Commitment in action.
              </h2>
            </div>
            <p className="text-xs sm:text-sm text-[#526456] max-w-md font-light leading-relaxed">
              Audited production metrics across our sustainable extrusion capacity and quality guarantees.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
            {/* Card 01 · 100% */}
            <motion.div
              initial={{ opacity: 0, y: 30, rotateX: 6 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0 }}
              whileHover={{ y: -8, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative h-full rounded-[2.25rem] p-6 sm:p-7 bg-white/95 border border-[#b8d4b8] shadow-[0_10px_30px_rgba(20,50,25,0.06),0_2px_8px_rgba(20,50,25,0.04),inset_0_1px_0_rgba(255,255,255,0.95)] hover:shadow-[0_24px_50px_-10px_rgba(20,50,25,0.22),inset_0_1px_0_rgba(255,255,255,1)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              {/* Top bevel highlight line */}
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent pointer-events-none" />

              <div>
                {/* Top bar with category tag and 3D icon badge */}
                <div className="flex items-center justify-between gap-2 mb-6">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #Circularity
                  </span>
                  <div className="size-9 rounded-xl bg-gradient-to-br from-[#7da482]/25 to-[#3a6420]/10 border border-[#7da482]/30 flex items-center justify-center text-[#24452e] shadow-sm group-hover:scale-110 transition-transform">
                    <Leaf className="size-4 text-[#355a3f]" />
                  </div>
                </div>

                {/* Big Stat Value */}
                <p className="font-display text-5xl sm:text-6xl text-[#0e2617] tracking-tight drop-shadow-sm">
                  100<span className="text-3xl text-[#355a3f] font-normal">%</span>
                </p>

                <h3 className="font-display text-lg text-[#0e2617] mt-3">
                  Responsible Alternatives
                </h3>
                <p className="text-xs text-[#526456] mt-1.5 leading-relaxed">
                  Focused entirely on bio-based polymer resins with zero fossil fuel inputs.
                </p>
              </div>

              {/* Bottom status indicator */}
              <div className="flex items-center justify-between pt-4 mt-6 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>Pure Bio-Derived</span>
                <CheckCircle2 className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

            {/* Card 02 · 06+ */}
            <motion.div
              initial={{ opacity: 0, y: 30, rotateX: 6 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0.1 }}
              whileHover={{ y: -8, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative h-full rounded-[2.25rem] p-6 sm:p-7 bg-white/95 border border-[#b8d4b8] shadow-[0_10px_30px_rgba(20,50,25,0.06),0_2px_8px_rgba(20,50,25,0.04),inset_0_1px_0_rgba(255,255,255,0.95)] hover:shadow-[0_24px_50px_-10px_rgba(20,50,25,0.22),inset_0_1px_0_rgba(255,255,255,1)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent pointer-events-none" />

              <div>
                <div className="flex items-center justify-between gap-2 mb-6">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #ProductRange
                  </span>
                  <div className="size-9 rounded-xl bg-gradient-to-br from-[#7da482]/25 to-[#3a6420]/10 border border-[#7da482]/30 flex items-center justify-center text-[#24452e] shadow-sm group-hover:scale-110 transition-transform">
                    <Layers className="size-4 text-[#355a3f]" />
                  </div>
                </div>

                <p className="font-display text-5xl sm:text-6xl text-[#0e2617] tracking-tight drop-shadow-sm">
                  06<span className="text-3xl text-[#355a3f] font-normal">+</span>
                </p>

                <h3 className="font-display text-lg text-[#0e2617] mt-3">
                  Packaging Categories
                </h3>
                <p className="text-xs text-[#526456] mt-1.5 leading-relaxed">
                  Versatile solutions from retail carry bags to industrial waste containment.
                </p>
              </div>

              <div className="flex items-center justify-between pt-4 mt-6 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>Commercial Scale</span>
                <CheckCircle2 className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

            {/* Card 03 · 03 */}
            <motion.div
              initial={{ opacity: 0, y: 30, rotateX: 6 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0.2 }}
              whileHover={{ y: -8, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative h-full rounded-[2.25rem] p-6 sm:p-7 bg-white/95 border border-[#b8d4b8] shadow-[0_10px_30px_rgba(20,50,25,0.06),0_2px_8px_rgba(20,50,25,0.04),inset_0_1px_0_rgba(255,255,255,0.95)] hover:shadow-[0_24px_50px_-10px_rgba(20,50,25,0.22),inset_0_1px_0_rgba(255,255,255,1)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent pointer-events-none" />

              <div>
                <div className="flex items-center justify-between gap-2 mb-6">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #Verification
                  </span>
                  <div className="size-9 rounded-xl bg-gradient-to-br from-[#7da482]/25 to-[#3a6420]/10 border border-[#7da482]/30 flex items-center justify-center text-[#24452e] shadow-sm group-hover:scale-110 transition-transform">
                    <ShieldCheck className="size-4 text-[#355a3f]" />
                  </div>
                </div>

                <p className="font-display text-5xl sm:text-6xl text-[#0e2617] tracking-tight drop-shadow-sm">
                  03
                </p>

                <h3 className="font-display text-lg text-[#0e2617] mt-3">
                  Quality Checkpoints
                </h3>
                <p className="text-xs text-[#526456] mt-1.5 leading-relaxed">
                  Real-time laser gauge calibration, seam stress testing, and food contact safety.
                </p>
              </div>

              <div className="flex items-center justify-between pt-4 mt-6 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>CIPET Audited</span>
                <CheckCircle2 className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>

            {/* Card 04 · 01 */}
            <motion.div
              initial={{ opacity: 0, y: 30, rotateX: 6 }}
              whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0.3 }}
              whileHover={{ y: -8, rotateX: -3, scale: 1.02 }}
              style={{ transformStyle: "preserve-3d", perspective: 1000 }}
              className="group relative h-full rounded-[2.25rem] p-6 sm:p-7 bg-white/95 border border-[#b8d4b8] shadow-[0_10px_30px_rgba(20,50,25,0.06),0_2px_8px_rgba(20,50,25,0.04),inset_0_1px_0_rgba(255,255,255,0.95)] hover:shadow-[0_24px_50px_-10px_rgba(20,50,25,0.22),inset_0_1px_0_rgba(255,255,255,1)] hover:border-[#7da482] transition-all duration-300 flex flex-col justify-between"
            >
              <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent pointer-events-none" />

              <div>
                <div className="flex items-center justify-between gap-2 mb-6">
                  <span className="text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#eaf4ea] text-[#24452e] border border-[#c2dec2]">
                    #MissionGoal
                  </span>
                  <div className="size-9 rounded-xl bg-gradient-to-br from-[#7da482]/25 to-[#3a6420]/10 border border-[#7da482]/30 flex items-center justify-center text-[#24452e] shadow-sm group-hover:scale-110 transition-transform">
                    <Sparkles className="size-4 text-[#355a3f]" />
                  </div>
                </div>

                <p className="font-display text-5xl sm:text-6xl text-[#0e2617] tracking-tight drop-shadow-sm">
                  01
                </p>

                <h3 className="font-display text-lg text-[#0e2617] mt-3">
                  Shared Greener Future
                </h3>
                <p className="text-xs text-[#526456] mt-1.5 leading-relaxed">
                  Eliminating single-use petroleum plastic waste without compromising business operations.
                </p>
              </div>

              <div className="flex items-center justify-between pt-4 mt-6 border-t border-[#c8dac8]/70 text-[10px] font-mono text-[#24452e]">
                <span>Mission Aligned</span>
                <CheckCircle2 className="size-3.5 text-[#4a7354]" />
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 4. OUR PRINCIPLES                                                    */}
      {/* ==================================================================== */}
      <section className="px-6 sm:px-10 lg:px-16 py-24 lg:py-36">
        <div className="mx-auto max-w-[1150px]">
          <SectionTitle eyebrow="Our principles" title="Purpose in every decision." />
          <div className="mt-16 grid gap-8 md:grid-cols-3">
            {["Material honesty", "Manufacturing discipline", "Long-term responsibility"].map((x, i) => (
              <Reveal key={x} delay={i * 0.1} className="border-t border-[#7da482]/40 pt-8 space-y-3">
                <span className="text-xs font-mono text-[#355a3f] font-bold">0{i + 1}</span>
                <h3 className="text-2xl sm:text-3xl text-[#0e2617] font-display">{x}</h3>
                <p className="mt-4 leading-relaxed text-[#526456] text-sm">
                  We make thoughtful choices, communicate clearly and pursue meaningful improvement rather than easy claims.
                </p>
              </Reveal>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
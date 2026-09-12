import { createFileRoute, Link } from "@tanstack/react-router";
import {
  Award,
  CheckCircle2,
  Download,
  ExternalLink,
  Eye,
  FileCheck2,
  FileText,
  Globe,
  Info,
  Layers,
  Leaf,
  MapPin,
  Maximize2,
  Printer,
  Scale,
  ShieldAlert,
  ShieldCheck,
  Sparkles,
  X,
} from "lucide-react";
import { motion, AnimatePresence } from "motion/react";
import React, { useState } from "react";
import { Button } from "@/components/ui/button";
import certificateImg from "@/assets/cpcb-certificate.png";

export const Route = createFileRoute("/certificate")({
  head: () => ({
    meta: [
      { title: "Official Certification & CPCB Compliance | EVERGREENINDUSTRY" },
      {
        name: "description",
        content:
          "Central Pollution Control Board (CPCB) official government certificate for marketing and selling 100% compostable carry bags compliant with IS/ISO:17088.",
      },
      {
        property: "og:title",
        content: "Official Certification & CPCB Compliance | EVERGREENINDUSTRY",
      },
      {
        property: "og:description",
        content:
          "Govt. of India CPCB approved compostable carry bags. Certified under PWM Rules 2018 and IS/ISO 17088.",
      },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
  }),
  component: CertificatePage,
});

const complianceClauses = [
  {
    num: "i",
    title: "PWM Rules 2018 Statutory Provisions",
    summary: "Mandatory compliance with sections '4(h)' & '11(c)' under the Environment (Protection) Act, 1986.",
    detail:
      "Every production run strictly meets statutory requirements prohibiting conventional single-use polyethylene, ensuring full traceability and zero fossil polymers.",
  },
  {
    num: "ii",
    title: "MoEF&CC & CPCB Ongoing Conformance",
    summary: "Direct adherence to all dynamic guidelines issued by the Ministry of Environment, Forest & Climate Change.",
    detail:
      "Continuous verification and compliance updates aligned with Central and State Pollution Control Board environmental protocols.",
  },
  {
    num: "iii",
    title: "Certified Manufacturer Procurement",
    summary: "Documented six-monthly procurement audits from registered compostable raw material manufacturers.",
    detail:
      "Only verified non-GMO cassava, corn starch, and certified compostable aliphatic-aromatic biopolymer granulates enter the extrusion lines.",
  },
  {
    num: "iv",
    title: "Sales & Quantity Transparency",
    summary: "Audited reporting of carry bag sales quantities to regulatory agencies.",
    detail:
      "Complete chain of custody with serial tracking ensuring every metric ton deployed into commerce is logged for circular accountability.",
  },
  {
    num: "v",
    title: "IS/ISO:17088 Bilingual Marking",
    summary: "Mandatory bilingual 'COMPOSTABLE IS/ISO:17088' marking, manufacturer code, and seller certificate number.",
    detail:
      "Each carry bag clearly displays the national compostable insignia, certificate reference number, and language labeling preventing counterfeit substitution.",
  },
  {
    num: "vi",
    title: "Regulatory Oversight & Accountability",
    summary: "Strict government validity conditions ensuring permanent integrity of information.",
    detail:
      "Full transparency with environmental regulators, maintaining uncompromised quality assurance for enterprise and municipal partners.",
  },
];

const credentials = [
  {
    label: "Certificate Number",
    value: "ENVISOL BIOPLAST PVT LTD/6369159374/22",
    icon: FileCheck2,
  },
  {
    label: "Issuing Authority",
    value: "Central Pollution Control Board (CPCB), Govt. of India",
    icon: ShieldCheck,
  },
  {
    label: "Ministry",
    value: "Ministry of Environment, Forest & Climate Change",
    icon: Scale,
  },
  {
    label: "Application Reference",
    value: "No. 1669284 (PWM Rules, 2018)",
    icon: FileText,
  },
  {
    label: "Date of Issue",
    value: "23rd December 2022",
    icon: Award,
  },
  {
    label: "Registered Manufacturing Facility",
    value: "Shed No. 2, SIDCO Industrial Estate, N.K. Road, Thanjavur - 613006, Tamil Nadu",
    icon: MapPin,
  },
  {
    label: "Authorized Scope",
    value: "Marketing and Selling of Compostable Carry Bags in Indian Market as 'SELLER'",
    icon: Globe,
  },
  {
    label: "Standard Compliance",
    value: "IS/ISO:17088 (Compostable Plastics & Biopolymers)",
    icon: Leaf,
  },
];

function CertificatePage() {
  const [zoomOpen, setZoomOpen] = useState(false);

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="bg-[#eef4ee] min-h-screen text-[#162a1c] selection:bg-[#7da482] selection:text-[#2d5016]">
      {/* ==================================================================== */}
      {/* 1. HERO HEADER                                                       */}
      {/* ==================================================================== */}
      <section className="relative pt-32 pb-16 lg:pt-40 lg:pb-24 px-6 sm:px-10 lg:px-16 border-b border-[#c8dac8]/60 overflow-hidden">
        {/* Soft atmospheric glow */}
        <div className="absolute -top-24 right-0 w-[550px] h-[550px] rounded-full bg-[#7da482]/15 blur-3xl pointer-events-none" />
        <div className="absolute top-1/2 left-0 w-[450px] h-[450px] rounded-full bg-[#2d5016]/8 blur-3xl pointer-events-none" />

        <div className="relative mx-auto max-w-[1450px]">
          <div className="max-w-3xl space-y-6">
            {/* Eyebrow badge */}
            <div className="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/90 border border-[#b8d4b8] shadow-sm">
              <span className="relative flex h-2.5 w-2.5">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#355a3f] opacity-75" />
                <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#2d5016]" />
              </span>
              <span className="text-[11px] font-mono font-bold uppercase tracking-[0.24em] text-[#24452e]">
                Official Government Regulatory Compliance
              </span>
            </div>

            {/* Headline */}
            <h1 className="font-display text-4xl sm:text-6xl lg:text-[4.25rem] text-[#0e2617] leading-[1.02] tracking-tight">
              Govt. of India Certified.{" "}
              <span className="font-serif italic font-normal text-[#355a3f] block sm:inline">
                Compostable Without Compromise.
              </span>
            </h1>

            {/* Subtitle */}
            <p className="text-base sm:text-lg text-[#526456] font-light leading-relaxed max-w-2xl">
              Fully approved and certified by the Central Pollution Control Board (CPCB), Ministry of
              Environment, Forest & Climate Change, Government of India. Manufactured in strict conformance
              with Plastic Waste Management Rules, 2018 and IS/ISO:17088 compostability standards.
            </p>

            {/* Trust Pills */}
            <div className="flex flex-wrap gap-2.5 pt-2">
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <ShieldCheck className="size-3.5 text-[#355a3f]" />
                <span>CPCB Registered Seller</span>
              </div>
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <Leaf className="size-3.5 text-[#355a3f]" />
                <span>IS/ISO:17088 Specification</span>
              </div>
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <CheckCircle2 className="size-3.5 text-[#355a3f]" />
                <span>PWM Rules 2018 Certified</span>
              </div>
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#c8dac8] text-xs font-mono text-[#24452e] shadow-sm">
                <Sparkles className="size-3.5 text-[#355a3f]" />
                <span>100% Home & Soil Compostable</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 2. CERTIFICATE DOCUMENT SHOWCASE + CREDENTIALS SIDEBAR               */}
      {/* ==================================================================== */}
      <section className="py-20 lg:py-28 px-6 sm:px-10 lg:px-16">
        <div className="mx-auto max-w-[1450px]">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-14 items-start">
            {/* ─── LEFT: Official Certificate Document Frame (7 Cols) ─── */}
            <div className="lg:col-span-7 flex flex-col items-center">
              {/* Document Actions Bar */}
              <div className="w-full max-w-[620px] mb-5 flex items-center justify-between gap-3 px-4 py-3 rounded-2xl bg-white/80 backdrop-blur-md border border-[#c8dac8] shadow-sm">
                <div className="flex items-center gap-2.5">
                  <span className="size-2.5 rounded-full bg-emerald-500 animate-pulse" />
                  <span className="text-xs font-mono font-semibold uppercase tracking-wider text-[#24452e]">
                    Verified Official Copy
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setZoomOpen(true)}
                    className="h-8 px-3 rounded-full text-xs font-medium border-[#c8dac8] hover:bg-[#eef4ee] text-[#162a1c] gap-1.5"
                  >
                    <Maximize2 className="size-3.5" />
                    <span>Zoom</span>
                  </Button>
                  <Button
                    variant="outline"
                    size="sm"
                    asChild
                    className="h-8 px-3 rounded-full text-xs font-medium border-[#c8dac8] hover:bg-[#eef4ee] text-[#162a1c] gap-1.5"
                  >
                    <a href={certificateImg} download="CPCB-Compostable-Certificate.png" target="_blank" rel="noopener noreferrer">
                      <Download className="size-3.5" />
                      <span>Download</span>
                    </a>
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={handlePrint}
                    className="h-8 px-3 rounded-full text-xs font-medium hover:bg-[#eef4ee] text-[#162a1c] gap-1.5 hidden sm:inline-flex"
                  >
                    <Printer className="size-3.5" />
                    <span>Print</span>
                  </Button>
                </div>
              </div>

              {/* Physical Document Representation */}
              <motion.div
                initial={{ opacity: 0, y: 25 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, ease: "easeOut" }}
                className="relative group w-full max-w-[620px] rounded-[1.75rem] p-3 sm:p-5 bg-white border border-[#d2dfd2] shadow-[0_25px_60px_-15px_rgba(20,50,25,0.22),0_10px_25px_-10px_rgba(20,50,25,0.12)] transition-all duration-500 hover:shadow-[0_35px_80px_-15px_rgba(20,50,25,0.28)]"
              >
                {/* Gold-green official seal tag on top-right */}
                <div className="absolute -top-3.5 -right-3.5 z-20 px-3.5 py-1.5 rounded-full bg-[#2d5016] text-[#fbf8f3] border-2 border-white shadow-lg text-[10px] font-mono font-bold uppercase tracking-wider flex items-center gap-1.5">
                  <Award className="size-3.5 text-amber-300" />
                  <span>Govt. Approved</span>
                </div>

                {/* Clickable Image Container */}
                <div
                  onClick={() => setZoomOpen(true)}
                  className="relative cursor-zoom-in overflow-hidden rounded-xl bg-white border border-[#e8eee8]"
                >
                  <img
                    src={certificateImg}
                    alt="CPCB Certificate to seller for Marketing and Selling of Compostable Carry Bags"
                    className="w-full h-auto object-contain transition-transform duration-700 group-hover:scale-[1.015]"
                  />

                  {/* Hover Overlay Hint */}
                  <div className="absolute inset-0 bg-[#0e2617]/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                    <span className="px-4 py-2 rounded-full bg-white/95 text-[#0e2617] text-xs font-semibold uppercase tracking-wider shadow-lg flex items-center gap-2 backdrop-blur-sm">
                      <Eye className="size-4 text-[#355a3f]" />
                      Click to Enlarge Document
                    </span>
                  </div>
                </div>

                {/* Document Caption */}
                <div className="mt-4 pt-3 border-t border-[#eef4ee] flex items-center justify-between text-xs text-[#526456]">
                  <span className="font-mono">Central Pollution Control Board (CPCB)</span>
                  <span className="font-mono text-[#355a3f] font-semibold">Ref: PWM/2018/1669284</span>
                </div>
              </motion.div>
            </div>

            {/* ─── RIGHT: Official Credential Details & Specifications (5 Cols) ─── */}
            <div className="lg:col-span-5 space-y-6">
              <div className="p-8 sm:p-10 rounded-[2.5rem] bg-white border border-[#c8dac8] shadow-sm space-y-6">
                <div>
                  <div className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.25em] font-bold text-[#355a3f] mb-2">
                    <CheckCircle2 className="size-4" />
                    <span>Official Credential Details</span>
                  </div>
                  <h2 className="font-display text-2xl sm:text-3xl text-[#0e2617]">
                    Central Pollution Control Board Authorization
                  </h2>
                  <p className="text-sm text-[#526456] mt-2 font-light leading-relaxed">
                    Formal authorization issued under the provisions of the Plastic Waste Management Rules,
                    confirming our biopolymer carry bags undergo natural biological decomposition.
                  </p>
                </div>

                <div className="space-y-4 pt-2">
                  {credentials.map((item, idx) => {
                    const Icon = item.icon;
                    return (
                      <div
                        key={idx}
                        className="p-3.5 rounded-2xl bg-[#f8faf8] border border-[#e2ece2] transition-colors hover:border-[#b8d4b8]"
                      >
                        <div className="flex items-start gap-3">
                          <span className="p-2 rounded-xl bg-white border border-[#d8e6d8] text-[#355a3f] shrink-0 mt-0.5 shadow-2xs">
                            <Icon className="size-4" />
                          </span>
                          <div className="min-w-0">
                            <span className="text-[10px] font-mono uppercase tracking-wider text-[#526456] block">
                              {item.label}
                            </span>
                            <span className="text-xs sm:text-sm font-semibold text-[#0e2617] block break-words mt-0.5">
                              {item.value}
                            </span>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>

                {/* Primary Action Button */}
                <div className="pt-2">
                  <Button
                    asChild
                    className="w-full h-12 rounded-full font-semibold text-xs uppercase tracking-[0.16em] transition-all duration-300 shadow-md"
                    style={{
                      background: "#2d5016",
                      color: "#fbf8f3",
                    }}
                  >
                    <Link to="/contact" className="flex items-center justify-center gap-2">
                      <span>Request Batch Test Certificate</span>
                      <ExternalLink className="size-3.5" />
                    </Link>
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 3. SIX COMPLIANCE CLAUSES BREAKDOWN                                  */}
      {/* ==================================================================== */}
      <section className="py-24 lg:py-32 px-6 sm:px-10 lg:px-16 bg-[#e4ece4]/60 border-y border-[#c8dac8]/60">
        <div className="mx-auto max-w-[1450px]">
          <div className="max-w-3xl mb-16 space-y-4">
            <span className="text-xs uppercase tracking-[0.24em] font-bold text-[#355a3f]">
              Conditions of Permission
            </span>
            <h2 className="font-display text-3xl sm:text-5xl text-[#0e2617] leading-tight">
              Regulatory compliance clauses & statutory criteria.
            </h2>
            <p className="text-base text-[#526456] font-light leading-relaxed">
              Every production batch adheres strictly to the six condition benchmarks mandated by the Central
              Pollution Control Board for marketing compostable biopolymers in India.
            </p>
          </div>

          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {complianceClauses.map((clause) => (
              <motion.div
                key={clause.num}
                whileHover={{ y: -4 }}
                transition={{ duration: 0.3 }}
                className="p-8 rounded-[2rem] bg-white border border-[#c8dac8] shadow-sm hover:border-[#7da482] hover:shadow-md transition-all flex flex-col justify-between"
              >
                <div>
                  <div className="flex items-center justify-between mb-5">
                    <span className="size-10 rounded-2xl bg-[#eef4ee] border border-[#c8dac8] grid place-items-center font-mono font-bold text-sm text-[#2d5016]">
                      Clause {clause.num}
                    </span>
                    <span className="text-[10px] font-mono uppercase tracking-widest text-[#7da482]">
                      Mandatory Condition
                    </span>
                  </div>

                  <h3 className="font-display text-xl text-[#0e2617] mb-2 leading-snug">
                    {clause.title}
                  </h3>

                  <p className="text-xs font-semibold text-[#355a3f] mb-3 leading-relaxed">
                    {clause.summary}
                  </p>

                  <p className="text-xs text-[#526456] leading-relaxed font-light">
                    {clause.detail}
                  </p>
                </div>

                <div className="mt-6 pt-4 border-t border-[#f0f5f0] flex items-center gap-1.5 text-[11px] font-mono text-[#2d5016]">
                  <CheckCircle2 className="size-3.5 text-[#355a3f]" />
                  <span>100% Conformance Verified</span>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 4. IS/ISO 17088 STANDARD DEEP DIVE                                   */}
      {/* ==================================================================== */}
      <section className="py-24 lg:py-32 px-6 sm:px-10 lg:px-16">
        <div className="mx-auto max-w-[1350px]">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            <div className="lg:col-span-6 space-y-6">
              <span className="text-xs uppercase tracking-[0.24em] font-bold text-[#355a3f]">
                Standardization Benchmark
              </span>
              <h2 className="font-display text-3xl sm:text-5xl text-[#0e2617] leading-tight">
                What makes IS/ISO:17088 the gold standard?
              </h2>
              <p className="text-base text-[#526456] font-light leading-relaxed">
                Conventional "oxo-biodegradable" plastics merely fragment into toxic microplastics that persist
                in groundwater and soil for centuries. Under Indian Standard IS/ISO 17088, true compostable
                plastics must satisfy four uncompromising scientific criteria:
              </p>

              <div className="space-y-4 pt-2">
                <div className="flex gap-4 p-4 rounded-2xl bg-white border border-[#c8dac8]">
                  <span className="size-9 rounded-xl bg-[#2d5016] text-[#fbf8f3] grid place-items-center shrink-0 font-mono text-xs font-bold">
                    01
                  </span>
                  <div>
                    <h4 className="text-sm font-semibold text-[#0e2617]">Total Biodegradability</h4>
                    <p className="text-xs text-[#526456] mt-1 font-light leading-relaxed">
                      At least 90% of the organic carbon converts to CO2, water, and biomass within 180 days in
                      soil composting environments.
                    </p>
                  </div>
                </div>

                <div className="flex gap-4 p-4 rounded-2xl bg-white border border-[#c8dac8]">
                  <span className="size-9 rounded-xl bg-[#2d5016] text-[#fbf8f3] grid place-items-center shrink-0 font-mono text-xs font-bold">
                    02
                  </span>
                  <div>
                    <h4 className="text-sm font-semibold text-[#0e2617]">Disintegration Rate</h4>
                    <p className="text-xs text-[#526456] mt-1 font-light leading-relaxed">
                      Physical breakdown into fragments smaller than 2 mm within 12 weeks with zero synthetic
                      clumping.
                    </p>
                  </div>
                </div>

                <div className="flex gap-4 p-4 rounded-2xl bg-white border border-[#c8dac8]">
                  <span className="size-9 rounded-xl bg-[#2d5016] text-[#fbf8f3] grid place-items-center shrink-0 font-mono text-xs font-bold">
                    03
                  </span>
                  <div>
                    <h4 className="text-sm font-semibold text-[#0e2617]">Heavy Metals & Ecotoxicity Thresholds</h4>
                    <p className="text-xs text-[#526456] mt-1 font-light leading-relaxed">
                      Residual compost shows zero toxic effect on seedling germination, plant growth, and soil
                      earthworm ecosystems.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Card: Enterprise Audit Assurance */}
            <div className="lg:col-span-6">
              <div className="p-8 sm:p-12 rounded-[3rem] bg-[#0e2617] text-[#fbf8f3] space-y-8 relative overflow-hidden shadow-2xl">
                <div className="absolute top-0 right-0 w-80 h-80 bg-[#355a3f]/30 rounded-full blur-3xl pointer-events-none" />
                <div className="relative z-10 space-y-6">
                  <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs text-[#9cb99e] font-mono">
                    <ShieldAlert className="size-3.5 text-amber-300" />
                    <span>Commercial Compliance Guarantee</span>
                  </div>

                  <h3 className="font-display text-2xl sm:text-4xl text-[#f0ebe0] leading-snug">
                    Zero Risk of Municipal Penalties for Your Brand.
                  </h3>

                  <p className="text-sm text-[#fbf8f3]/75 font-light leading-relaxed">
                    With nationwide single-use plastic bans rigorously enforced across Indian retail and dining
                    establishments, possessing authentic CPCB-certified carry bags guarantees full immunity from
                    confiscations and administrative fines.
                  </p>

                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                    <div className="flex items-center gap-2.5 text-xs text-[#c4d9c6] font-mono">
                      <CheckCircle2 className="size-4 text-[#7da482]" />
                      <span>Valid across all Indian States & Union Territories</span>
                    </div>
                    <div className="flex items-center gap-2.5 text-xs text-[#c4d9c6] font-mono">
                      <CheckCircle2 className="size-4 text-[#7da482]" />
                      <span>Includes manufacturer license and CPCB QR serial</span>
                    </div>
                    <div className="flex items-center gap-2.5 text-xs text-[#c4d9c6] font-mono">
                      <CheckCircle2 className="size-4 text-[#7da482]" />
                      <span>Custom brand printing with statutory certification footer</span>
                    </div>
                  </div>

                  <div className="pt-2">
                    <Button
                      asChild
                      className="h-12 px-7 rounded-full font-semibold text-xs uppercase tracking-[0.16em] bg-[#f0ebe0] text-[#1a3321] hover:bg-[#d4e8d5] hover:text-[#0e2617] transition-colors"
                    >
                      <Link to="/products" className="flex items-center gap-2">
                        <span>Explore Certified Products</span>
                      </Link>
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ==================================================================== */}
      {/* 5. FULLSCREEN MODAL ZOOM                                             */}
      {/* ==================================================================== */}
      <AnimatePresence>
        {zoomOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={() => setZoomOpen(false)}
            className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 sm:p-8"
          >
            <motion.div
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              onClick={(e) => e.stopPropagation()}
              className="relative max-w-4xl max-h-[90vh] bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col"
            >
              {/* Modal Header */}
              <div className="px-6 py-4 border-b border-[#eef4ee] flex items-center justify-between bg-[#f8faf8]">
                <div className="flex items-center gap-2.5">
                  <ShieldCheck className="size-4 text-[#355a3f]" />
                  <span className="text-xs font-mono font-bold uppercase tracking-wider text-[#0e2617]">
                    CPCB Government Certification Document
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    asChild
                    className="h-8 px-3 rounded-full text-xs font-medium border-[#c8dac8] gap-1.5"
                  >
                    <a href={certificateImg} download="CPCB-Compostable-Certificate.png">
                      <Download className="size-3.5" />
                      <span>Download</span>
                    </a>
                  </Button>
                  <Button
                    variant="ghost"
                    size="icon"
                    onClick={() => setZoomOpen(false)}
                    className="size-8 rounded-full hover:bg-[#e8eee8] text-[#162a1c]"
                  >
                    <X className="size-4" />
                  </Button>
                </div>
              </div>

              {/* Scrollable Image Viewport */}
              <div className="overflow-auto p-4 sm:p-8 max-h-[calc(90vh-70px)] flex justify-center bg-[#fdfdfd]">
                <img
                  src={certificateImg}
                  alt="Full CPCB Certificate Document"
                  className="max-w-full h-auto object-contain rounded-lg border border-[#e8eee8] shadow-sm"
                />
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

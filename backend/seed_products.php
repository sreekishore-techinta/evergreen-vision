<?php
// ============================================================
// PRODUCT SEEDER — run once via browser, then delete
// URL: http://localhost/evergreen-vision/backend/seed_products.php
// ============================================================
require_once __DIR__ . '/config/db.php';

$pdo = db();
$done   = [];
$errors = [];

// ── Step 1: Ensure schema has all needed columns ─────────────
$migrations = [
    "ALTER TABLE products ADD COLUMN IF NOT EXISTS category_id INT UNSIGNED NULL DEFAULT NULL",
    "ALTER TABLE products ADD COLUMN IF NOT EXISTS image_path  VARCHAR(255) NOT NULL DEFAULT ''",
];
foreach ($migrations as $sql) {
    try { $pdo->exec($sql); } catch (PDOException $e) { /* column already exists */ }
}

// ── Step 2: Ensure all 5 categories exist ────────────────────
$categoryDefs = [
    ['Carry Bags',        'carry-bags',         'Compostable carry bags for retail and daily use.',                  1],
    ['Waste Bags',        'waste-bags',         'Heavy-duty compostable liners for waste management.',              2],
    ['Produce Packaging', 'produce-packaging',  'Breathable pouches that keep fresh produce at its best.',          3],
    ['Raw Materials',     'raw-materials',       'Biopolymer granules and resins for eco-packaging manufacturers.',  4],
    ['Lifestyle',         'lifestyle',           'Premium eco-lifestyle bags and accessories.',                      5],
];
$inscat = $pdo->prepare(
    "INSERT IGNORE INTO categories (name, slug, description, sort_order, is_active) VALUES (?,?,?,?,1)"
);
foreach ($categoryDefs as $c) {
    try { $inscat->execute($c); } catch (PDOException $e) { $errors[] = "Cat: " . $e->getMessage(); }
}
$done[] = "✓ Categories ensured";

// Fetch category id map: name → id
$catMap = [];
foreach ($pdo->query("SELECT id, name FROM categories")->fetchAll() as $row) {
    $catMap[$row['name']] = (int)$row['id'];
}

// ── Step 3: Product data — 5 per category ────────────────────
// image_url values MUST match the keys in the frontend ASSET_MAP:
//   compostable-bags-blank.jpg | compostable-waste-bags.jpg
//   breathable-produce-pouches.jpg | biopolymer-granules.jpg
//   eco-lifestyle-bag.jpg | product-shopping-bag.jpg | product-collection.jpg
$products = [

    // ── CARRY BAGS (5) ────────────────────────────────────────
    [
        'name'           => 'Compostable D-Cut Carry Bags',
        'slug'           => 'compostable-d-cut-carry-bags',
        'category'       => 'Carry Bags',
        'description'    => 'Certified compostable D-cut carry bags made from PBAT & corn starch. Ideal for grocery, retail, and daily shopping. Zero microplastics.',
        'features'       => "• EN 13432 & ASTM D6400 certified\n• Available in GSM 20–50\n• Custom print-ready surface\n• Decomposes in 180 days\n• Food-contact safe",
        'applications'   => "• Grocery & supermarkets\n• Retail outlets\n• Pharmacies\n• Bakeries",
        'image_url'      => 'compostable-bags-blank.jpg',
        'certifications' => 'EN 13432, ASTM D6400, CPCB',
        'is_featured'    => 1,
        'sort_order'     => 1,
    ],
    [
        'name'           => 'Bio Carry Bags — Loop Handle',
        'slug'           => 'bio-carry-bags-loop-handle',
        'category'       => 'Carry Bags',
        'description'    => 'Premium loop-handle compostable bags designed for boutique and fashion retail. Strong enough for 5 kg loads while fully bio-degradable.',
        'features'       => "• Reinforced loop handle\n• 100% plant-derived starch\n• GSM 35–60 range\n• Satin-finish print surface\n• Breaks down in soil & compost",
        'applications'   => "• Fashion boutiques\n• Gift shops\n• Cosmetics retail\n• Apparel stores",
        'image_url'      => 'product-shopping-bag.jpg',
        'certifications' => 'EN 13432, ISO 14855',
        'is_featured'    => 1,
        'sort_order'     => 2,
    ],
    [
        'name'           => 'T-Shirt Compostable Bags',
        'slug'           => 't-shirt-compostable-bags',
        'category'       => 'Carry Bags',
        'description'    => 'Lightweight T-shirt style compostable bags perfect for high-volume takeaway, grocery checkout, and casual retail. Punched handle for easy dispensing.',
        'features'       => "• Punched T-shirt handles\n• Ultra-thin 15–25 GSM option\n• High-speed roll format available\n• BPI & OK Compost certified\n• Tear-resistant blend",
        'applications'   => "• Takeaway restaurants\n• Street food vendors\n• Grocery checkout\n• Wet markets",
        'image_url'      => 'compostable-bags-blank.jpg',
        'certifications' => 'BPI Certified, OK Compost',
        'is_featured'    => 0,
        'sort_order'     => 3,
    ],
    [
        'name'           => 'Flat-Bottom Gusset Carry Bags',
        'slug'           => 'flat-bottom-gusset-carry-bags',
        'category'       => 'Carry Bags',
        'description'    => 'Side-gusset flat-bottom compostable bags for heavier loads. Stable base keeps items upright — ideal for supermarkets and bulk food retail.',
        'features'       => "• Side-gusset & flat-bottom design\n• Load capacity up to 10 kg\n• 40–80 GSM thickness range\n• Moisture-resistant outer layer\n• Full-colour flexo printing",
        'applications'   => "• Supermarkets\n• Bulk food stores\n• Hardware retail\n• Agricultural produce",
        'image_url'      => 'product-shopping-bag.jpg',
        'certifications' => 'EN 13432, CIPET Audited',
        'is_featured'    => 0,
        'sort_order'     => 4,
    ],
    [
        'name'           => 'Oxo-Biodegradable Carry Bags',
        'slug'           => 'oxo-biodegradable-carry-bags',
        'category'       => 'Carry Bags',
        'description'    => 'Oxo-biodegradable carry bags with d2w additive for environments where industrial composting is unavailable. Degrades under UV and heat exposure.',
        'features'       => "• d2w pro-oxidant additive\n• Breaks down in 2–5 years outdoors\n• High tensile strength\n• Cost-effective transition option\n• Available in biodegradable prints",
        'applications'   => "• Rural retail markets\n• Export packaging\n• Logistics & fulfilment",
        'image_url'      => 'compostable-bags-blank.jpg',
        'certifications' => 'ASTM D6954, CPCB Approved',
        'is_featured'    => 0,
        'sort_order'     => 5,
    ],

    // ── WASTE BAGS (5) ────────────────────────────────────────
    [
        'name'           => 'Compostable Household Waste Liners',
        'slug'           => 'compostable-household-waste-liners',
        'category'       => 'Waste Bags',
        'description'    => 'Heavy-duty compostable liners for kitchen bins, dry waste, and organic waste segregation at home. Leak-proof base seam for reliable containment.',
        'features'       => "• Leak-proof star-sealed bottom\n• 30 L / 60 L / 120 L sizes\n• Odour-neutral material\n• EN 13432 home-compost certified\n• Perforated roll format",
        'applications'   => "• Household kitchen bins\n• Dry & wet waste segregation\n• Apartment complexes\n• Smart city waste programs",
        'image_url'      => 'compostable-waste-bags.jpg',
        'certifications' => 'EN 13432, OK Compost HOME',
        'is_featured'    => 1,
        'sort_order'     => 6,
    ],
    [
        'name'           => 'Commercial Compostable Bin Liners',
        'slug'           => 'commercial-compostable-bin-liners',
        'category'       => 'Waste Bags',
        'description'    => 'Extra-large compostable bin liners for hotels, restaurants, corporate offices and municipal waste collection. High puncture resistance for heavy loads.',
        'features'       => "• 120 L – 240 L capacity\n• Puncture-resistant PBAT blend\n• High-heat stable up to 60 °C\n• Suitable for organic wet waste\n• Flat-pack or roll on core",
        'applications'   => "• Hotels & hospitality\n• Restaurants & food courts\n• Corporate campuses\n• Municipal SWM programs",
        'image_url'      => 'compostable-waste-bags.jpg',
        'certifications' => 'EN 13432, ASTM D6400, CIPET',
        'is_featured'    => 1,
        'sort_order'     => 7,
    ],
    [
        'name'           => 'Medical Waste Compostable Bags',
        'slug'           => 'medical-waste-compostable-bags',
        'category'       => 'Waste Bags',
        'description'    => 'Colour-coded compostable bags for segregated bio-medical and non-hazardous clinical waste. Compliant with BMWM Rules 2016.',
        'features'       => "• Yellow / Red / Green colour coding\n• Bio-medical waste compliant\n• Leak-proof double-seam bottom\n• Tie-top closure option\n• Autoclaving-safe grades available",
        'applications'   => "• Clinics & hospitals\n• Diagnostic labs\n• Dental practices\n• Veterinary centres",
        'image_url'      => 'compostable-waste-bags.jpg',
        'certifications' => 'BMWM Rules 2016, EN 13432',
        'is_featured'    => 0,
        'sort_order'     => 8,
    ],
    [
        'name'           => 'Garden & Yard Waste Bags',
        'slug'           => 'garden-yard-waste-bags',
        'category'       => 'Waste Bags',
        'description'    => 'Large paper-reinforced compostable bags for garden clippings, leaves, and yard waste. Can be placed directly in municipal green-waste collection.',
        'features'       => "• 80 L – 160 L capacity\n• Dual-layer paper + PLA liner\n• Water-resistant for 4 hours\n• Standing open-top design\n• Certified for municipal compost",
        'applications'   => "• Home gardens\n• Landscaping contractors\n• Municipal green-waste\n• Nurseries & parks",
        'image_url'      => 'compostable-waste-bags.jpg',
        'certifications' => 'EN 13432, BPI Certified',
        'is_featured'    => 0,
        'sort_order'     => 9,
    ],
    [
        'name'           => 'Dog Waste Compostable Poop Bags',
        'slug'           => 'dog-waste-compostable-poop-bags',
        'category'       => 'Waste Bags',
        'description'    => 'Ultra-thin but leak-proof compostable pet waste bags. Lavender-scented option available. Dispenser-roll format for easy carry.',
        'features'       => "• 15-micron extra-strong film\n• Lavender-scented variant\n• Roll of 15 bags per tube\n• Tie handles for no-touch disposal\n• Decomposes in 90 days",
        'applications'   => "• Pet owners & dog walkers\n• Pet supply stores\n• Veterinary clinics\n• Gated communities",
        'image_url'      => 'compostable-waste-bags.jpg',
        'certifications' => 'EN 13432, OK Compost',
        'is_featured'    => 0,
        'sort_order'     => 10,
    ],

    // ── PRODUCE PACKAGING (5) ─────────────────────────────────
    [
        'name'           => 'Micro-Perforated Breathable Pouches',
        'slug'           => 'micro-perforated-breathable-pouches',
        'category'       => 'Produce Packaging',
        'description'    => 'Laser micro-perforated compostable pouches that extend fresh fruit and vegetable shelf life by up to 40%. Optimises ethylene & CO₂ exchange.',
        'features'       => "• 200–400 micron laser perforations\n• Extends shelf life 35–40%\n• Reduces condensation moisture\n• Heat-sealable top\n• Compostable PLA/PBAT film",
        'applications'   => "• Strawberries & berries\n• Cherry tomatoes\n• Fresh herbs\n• Salad leaves\n• Baby carrots",
        'image_url'      => 'breathable-produce-pouches.jpg',
        'certifications' => 'EN 13432, ASTM D6400',
        'is_featured'    => 1,
        'sort_order'     => 11,
    ],
    [
        'name'           => 'Compostable Produce Net Bags',
        'slug'           => 'compostable-produce-net-bags',
        'category'       => 'Produce Packaging',
        'description'    => 'Open-weave compostable net bags for onions, garlic, citrus, and root vegetables. Fully breathable with superior ventilation for long freshness.',
        'features'       => "• Open-weave PLA netting\n• 500 g – 5 kg capacity\n• Heat-crimped top closure\n• Natural off-white colour\n• Home compostable certified",
        'applications'   => "• Onions & garlic\n• Citrus fruits\n• Potatoes & beets\n• Farm-market bundles",
        'image_url'      => 'breathable-produce-pouches.jpg',
        'certifications' => 'OK Compost HOME, EN 13432',
        'is_featured'    => 0,
        'sort_order'     => 12,
    ],
    [
        'name'           => 'Modified Atmosphere Produce Bags',
        'slug'           => 'modified-atmosphere-produce-bags',
        'category'       => 'Produce Packaging',
        'description'    => 'Multi-layer MAP (Modified Atmosphere Packaging) compostable pouches that dramatically slow respiration of cut produce and salad mixes.',
        'features'       => "• 3-layer PLA / barrier / PLA structure\n• Customisable O₂ transmission rate\n• Heat-seal & zip-lock variants\n• Compatible with gas-flush machines\n• FSSC 22000 facility",
        'applications'   => "• Pre-cut salad packs\n• Sliced fruits\n• Ready-to-eat vegetables\n• Fresh pasta & bakery",
        'image_url'      => 'breathable-produce-pouches.jpg',
        'certifications' => 'EN 13432, FSSC 22000',
        'is_featured'    => 0,
        'sort_order'     => 13,
    ],
    [
        'name'           => 'Compostable Flat Produce Pouches',
        'slug'           => 'compostable-flat-produce-pouches',
        'category'       => 'Produce Packaging',
        'description'    => 'Simple flat compostable pouches for leafy greens, herbs, and microgreens. Lightweight and printable for farm-to-shelf branded retail.',
        'features'       => "• Thin 18-micron PLA film\n• Full-surface print area\n• Perforated tear-notch\n• Retail hang-hole option\n• Transparent window",
        'applications'   => "• Leafy greens\n• Microgreens\n• Fresh herbs retail\n• Farmers market packs",
        'image_url'      => 'breathable-produce-pouches.jpg',
        'certifications' => 'EN 13432, ASTM D6400',
        'is_featured'    => 0,
        'sort_order'     => 14,
    ],
    [
        'name'           => 'Banana Ripening Compostable Sleeves',
        'slug'           => 'banana-ripening-compostable-sleeves',
        'category'       => 'Produce Packaging',
        'description'    => 'Specially engineered compostable sleeves that slow banana ripening in transit. Reduces post-harvest loss by up to 25% for exporters.',
        'features'       => "• Controlled ethylene permeability\n• UV-stable outer layer\n• Fits standard banana bunch\n• Reduces browning in transit\n• Fully compostable after use",
        'applications'   => "• Banana exporters\n• Cold-chain logistics\n• Wholesale fruit markets\n• Ripening facilities",
        'image_url'      => 'breathable-produce-pouches.jpg',
        'certifications' => 'EN 13432, Rainforest Alliance Compatible',
        'is_featured'    => 0,
        'sort_order'     => 15,
    ],

    // ── RAW MATERIALS (5) ─────────────────────────────────────
    [
        'name'           => 'PBAT / PLA Biopolymer Granules',
        'slug'           => 'pbat-pla-biopolymer-granules',
        'category'       => 'Raw Materials',
        'description'    => 'Industrial-grade PBAT/PLA blended granules — the core material behind Evergreen\'s full product range. Available in natural off-white and custom-pigmented grades.',
        'features'       => "• PBAT : PLA ratio 60:40 (standard) or custom\n• MFI 2–8 g/10 min\n• Processing temp 150–190 °C\n• Blown film & cast film compatible\n• 25 kg moisture-barrier bags",
        'applications'   => "• Blown-film carry bags\n• Cast stretch film\n• Thermoformed trays\n• Injection-moulded items",
        'image_url'      => 'biopolymer-granules.jpg',
        'certifications' => 'EN 13432, ASTM D6400, REACH',
        'is_featured'    => 1,
        'sort_order'     => 16,
    ],
    [
        'name'           => 'Thermoplastic Starch (TPS) Granules',
        'slug'           => 'thermoplastic-starch-tps-granules',
        'category'       => 'Raw Materials',
        'description'    => 'Corn and cassava starch-based thermoplastic granules for manufacturers seeking 100% bio-based content in their compostable packaging lines.',
        'features'       => "• 70–80% renewable bio-content\n• Low carbon footprint vs PLA\n• Suitable for injection moulding\n• Compatible with PLA blending\n• Available in food-contact grade",
        'applications'   => "• Loose-fill packaging peanuts\n• Single-use cutlery\n• Seedling pots\n• Food-service trays",
        'image_url'      => 'biopolymer-granules.jpg',
        'certifications' => 'EN 13432, DIN CERTCO, USDA BioPreferred',
        'is_featured'    => 0,
        'sort_order'     => 17,
    ],
    [
        'name'           => 'PLA (Polylactic Acid) Pellets',
        'slug'           => 'pla-polylactic-acid-pellets',
        'category'       => 'Raw Materials',
        'description'    => 'High-clarity PLA pellets derived from fermented plant sugars. Ideal for transparent packaging films, rigid thermoformed containers, and coatings.',
        'features'       => "• > 99% optical clarity\n• Rigid film & extrusion grade\n• Heat resistance up to 55 °C\n• Fast compostability in industrial facilities\n• Low moisture absorption",
        'applications'   => "• Clear clamshell containers\n• Window pouches\n• Cold beverage cups\n• Shrink-wrap labels",
        'image_url'      => 'biopolymer-granules.jpg',
        'certifications' => 'EN 13432, FDA Generally Recognized As Safe',
        'is_featured'    => 0,
        'sort_order'     => 18,
    ],
    [
        'name'           => 'Bio-Based Masterbatch Pigments',
        'slug'           => 'bio-based-masterbatch-pigments',
        'category'       => 'Raw Materials',
        'description'    => 'Concentrated pigment masterbatches formulated specifically for PBAT/PLA base resins. Achieve vivid brand colours without compromising compostability certification.',
        'features'       => "• 20–40% pigment loading\n• Compatible with EN 13432 films\n• Non-toxic, food-contact safe\n• Pellet or powder form\n• Pantone colour matching available",
        'applications'   => "• Coloured carry bags\n• Branded waste liners\n• Retail packaging\n• Agricultural films",
        'image_url'      => 'biopolymer-granules.jpg',
        'certifications' => 'REACH, RoHS, EN 13432 Compatible',
        'is_featured'    => 0,
        'sort_order'     => 19,
    ],
    [
        'name'           => 'Compostable Adhesive & Sealant Resin',
        'slug'           => 'compostable-adhesive-sealant-resin',
        'category'       => 'Raw Materials',
        'description'    => 'Hot-melt compostable adhesive resin for seaming and laminating multi-layer bio-packaging. Compatible with standard sealing equipment.',
        'features'       => "• 100% compostable formula\n• Sealing temp range 80–130 °C\n• Strong peel resistance\n• Water-based and solvent-free\n• Compatible with PLA & PBAT substrates",
        'applications'   => "• Pouch heat-sealing\n• Lamination adhesive\n• Label adhesive layer\n• Multi-layer film bonding",
        'image_url'      => 'biopolymer-granules.jpg',
        'certifications' => 'EN 13432, ASTM D6400, FDA',
        'is_featured'    => 0,
        'sort_order'     => 20,
    ],

    // ── LIFESTYLE (5) ─────────────────────────────────────────
    [
        'name'           => 'Eco Lifestyle Tote Bags',
        'slug'           => 'eco-lifestyle-tote-bags',
        'category'       => 'Lifestyle',
        'description'    => 'Premium reusable tote bags crafted from natural jute and compostable inner liner. A fashion-forward sustainable carry solution for everyday use.',
        'features'       => "• Natural jute outer\n• Compostable PLA inner lining\n• 15 kg carry capacity\n• 40 × 35 × 12 cm standard size\n• Custom print & embroidery",
        'applications'   => "• Grocery shopping\n• Farmers markets\n• Corporate gifting\n• Brand merchandise",
        'image_url'      => 'eco-lifestyle-bag.jpg',
        'certifications' => 'GOTS (Jute), EN 13432 (Liner)',
        'is_featured'    => 1,
        'sort_order'     => 21,
    ],
    [
        'name'           => 'Compostable Shopping Bags — Premium',
        'slug'           => 'compostable-shopping-bags-premium',
        'category'       => 'Lifestyle',
        'description'    => 'High-end compostable shopping bags with rope handles and matte finish. Designed for luxury retail, boutiques, and premium brand experiences.',
        'features'       => "• Matte laminate finish\n• Twisted rope cotton handles\n• Rigid bottom insert\n• Spot UV print option\n• Fully compostable construction",
        'applications'   => "• Luxury boutiques\n• Jewellery stores\n• Premium cosmetics\n• High-end gifting",
        'image_url'      => 'product-shopping-bag.jpg',
        'certifications' => 'EN 13432, ISO 14001',
        'is_featured'    => 1,
        'sort_order'     => 22,
    ],
    [
        'name'           => 'Seed Paper Gift Wrap',
        'slug'           => 'seed-paper-gift-wrap',
        'category'       => 'Lifestyle',
        'description'    => 'Plantable seed-embedded paper sheets for wrapping gifts. After unwrapping, recipients plant the paper to grow wildflowers or herbs.',
        'features'       => "• Embedded wildflower / herb seeds\n• A3 & A4 sheet sizes\n• Water-colour pattern surface\n• FSC-certified paper base\n• Plants in 7–14 days in soil",
        'applications'   => "• Corporate gifting\n• Retail gift wrapping\n• Wedding & event favours\n• Eco-conscious brands",
        'image_url'      => 'eco-lifestyle-bag.jpg',
        'certifications' => 'FSC Certified, 100% Plastic-Free',
        'is_featured'    => 0,
        'sort_order'     => 23,
    ],
    [
        'name'           => 'Bamboo & PLA Coffee Cups',
        'slug'           => 'bamboo-pla-coffee-cups',
        'category'       => 'Lifestyle',
        'description'    => 'Single-use compostable coffee cups made from bamboo fibre and PLA lining. Leak-proof, heat-stable to 90 °C — a direct replacement for polystyrene cups.',
        'features'       => "• 8 oz / 12 oz / 16 oz sizes\n• PLA leak-proof inner coating\n• Dishwasher-safe reusable grade available\n• Offset print, QR-code friendly\n• Compostable lid sold separately",
        'applications'   => "• Cafes & coffee shops\n• Airport lounges\n• Corporate canteens\n• Events & conferences",
        'image_url'      => 'eco-lifestyle-bag.jpg',
        'certifications' => 'EN 13432, FDA Food Contact',
        'is_featured'    => 0,
        'sort_order'     => 24,
    ],
    [
        'name'           => 'Compostable Cutlery Set',
        'slug'           => 'compostable-cutlery-set',
        'category'       => 'Lifestyle',
        'description'    => 'CPLA (Crystallised PLA) cutlery set — fork, knife, spoon — heat-stable to 85 °C. A premium biodegradable alternative to single-use plastic cutlery.',
        'features'       => "• Fork + Knife + Spoon set\n• CPLA — heat-stable to 85 °C\n• Compostable kraft wrap packaging\n• White & natural wood-look finish\n• Bulk packed or individually wrapped",
        'applications'   => "• Food delivery services\n• Catering & events\n• Airline meals\n• Quick-service restaurants",
        'image_url'      => 'eco-lifestyle-bag.jpg',
        'certifications' => 'EN 13432, ASTM D6400, FDA',
        'is_featured'    => 0,
        'sort_order'     => 25,
    ],
];

// ── Step 4: Insert products ───────────────────────────────────
$ins = $pdo->prepare(
    "INSERT INTO products
       (name, slug, category_id, category, description, features, applications,
        image_url, image_path, certifications, is_featured, is_active, sort_order)
     VALUES
       (:name, :slug, :category_id, :category, :description, :features, :applications,
        :image_url, '', :certifications, :is_featured, 1, :sort_order)
     ON DUPLICATE KEY UPDATE
       category_id   = VALUES(category_id),
       category      = VALUES(category),
       description   = VALUES(description),
       features      = VALUES(features),
       applications  = VALUES(applications),
       image_url     = VALUES(image_url),
       certifications= VALUES(certifications),
       is_featured   = VALUES(is_featured),
       sort_order    = VALUES(sort_order)"
);

$inserted = 0;
$updated  = 0;

foreach ($products as $p) {
    $catId = $catMap[$p['category']] ?? null;
    try {
        $before = $pdo->query("SELECT COUNT(*) FROM products WHERE slug = " . $pdo->quote($p['slug']))->fetchColumn();
        $ins->execute([
            ':name'          => $p['name'],
            ':slug'          => $p['slug'],
            ':category_id'   => $catId,
            ':category'      => $p['category'],
            ':description'   => $p['description'],
            ':features'      => $p['features'],
            ':applications'  => $p['applications'],
            ':image_url'     => $p['image_url'],
            ':certifications'=> $p['certifications'],
            ':is_featured'   => $p['is_featured'],
            ':sort_order'    => $p['sort_order'],
        ]);
        if ((int)$before === 0) $inserted++;
        else $updated++;
    } catch (PDOException $e) {
        $errors[] = "Product '{$p['name']}': " . $e->getMessage();
    }
}
$done[] = "✓ Products: {$inserted} inserted, {$updated} updated";

// ── Step 5: Re-link category_ids for any older rows ──────────
try {
    $pdo->exec("
        UPDATE products p
        JOIN categories c ON c.name = p.category
        SET p.category_id = c.id
        WHERE p.category_id IS NULL
    ");
    $done[] = "✓ category_id back-filled on existing rows";
} catch (PDOException $e) {
    $errors[] = "Back-fill: " . $e->getMessage();
}

// ── Step 6: Counts ────────────────────────────────────────────
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalCats     = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn();
$done[] = "✓ Total active products in DB: {$totalProducts}";
$done[] = "✓ Total active categories in DB: {$totalCats}";

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Seed Products — Evergreen</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:system-ui,sans-serif;background:#f0fdf4;color:#0e2617;padding:40px 20px}
  .wrap{max-width:720px;margin:0 auto}
  h1{font-size:1.6rem;color:#14532d;margin-bottom:6px}
  .sub{color:#4a7054;font-size:.9rem;margin-bottom:28px}
  .card{background:#fff;border:1px solid #bbf7d0;border-radius:14px;padding:24px;margin-bottom:18px}
  .card h2{font-size:1rem;color:#166534;margin-bottom:12px}
  ul{list-style:none;display:flex;flex-direction:column;gap:6px}
  li.ok::before{content:"✓ ";color:#16a34a;font-weight:700}
  li.err{color:#dc2626}li.err::before{content:"✗ ";font-weight:700}
  li{font-size:.875rem;line-height:1.5}
  .warn{background:#fef9c3;border:1px solid #fde047;border-radius:12px;padding:20px;margin-top:8px}
  .warn strong{color:#854d0e}
  code{background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:.82rem}
  .stats{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:18px}
  .stat{background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px;text-align:center}
  .stat-num{font-size:2rem;font-weight:800;color:#15803d}
  .stat-lbl{font-size:.78rem;color:#4a7054;margin-top:2px}
</style>
</head>
<body>
<div class="wrap">
  <h1>🌿 Evergreen — Product Seeder</h1>
  <p class="sub">Inserts 25 products (5 per category) into the <code>ever_bio</code> database.</p>

  <div class="stats">
    <div class="stat"><div class="stat-num"><?= $totalProducts ?></div><div class="stat-lbl">Active Products</div></div>
    <div class="stat"><div class="stat-num"><?= $totalCats ?></div><div class="stat-lbl">Categories</div></div>
  </div>

  <?php if ($errors): ?>
  <div class="card" style="border-color:#fecaca;margin-top:18px">
    <h2 style="color:#dc2626">⚠ Errors</h2>
    <ul><?php foreach($errors as $e): ?><li class="err"><?= htmlspecialchars($e) ?></li><?php endforeach ?></ul>
  </div>
  <?php endif; ?>

  <div class="card" style="margin-top:18px">
    <h2>Completed Steps</h2>
    <ul><?php foreach($done as $d): ?><li class="ok"><?= htmlspecialchars($d) ?></li><?php endforeach ?></ul>
  </div>

  <div class="warn">
    <strong>⚠ Delete this file after seeding!</strong><br><br>
    <code>backend/seed_products.php</code><br><br>
    Then open the Products dropdown in the navbar — all 25 products should now appear grouped by category.
  </div>
</div>
</body>
</html>

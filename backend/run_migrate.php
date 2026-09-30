<?php
// ============================================================
// ONE-CLICK FULL MIGRATION — creates all tables + seeds 25 products
// URL: https://ghostwhite-hawk-667350.hostingersite.com/backend/run_migrate.php
// DELETE this file immediately after running.
// ============================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ── DB credentials (live server) ─────────────────────────────
$DB_HOST    = 'localhost';
$DB_NAME    = 'u910074219_evergreen_bio';
$DB_USER    = 'u910074219_evergreen_bio';
$DB_PASS    = 'Techinta@2026';
$DB_CHARSET = 'utf8mb4';

$done   = [];
$errors = [];
$pdo    = null;

// ── Step 1: Connect to MySQL without specifying DB, create DB if needed ──
try {
    $dsn0 = "mysql:host={$DB_HOST};charset={$DB_CHARSET}";
    $pdo0 = new PDO($dsn0, $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo0->exec("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $done[] = "✓ Database '{$DB_NAME}' exists / created";
} catch (PDOException $e) {
    die(render(
        "✗ Cannot connect to MySQL: " . htmlspecialchars($e->getMessage()) .
        "<br><br><strong>Fix:</strong> Verify credentials in this file match your Hostinger cPanel → Databases.",
        [], ["✗ MySQL connection failed"]
    ));
}

// ── Step 2: Connect to the database ─────────────────────────
try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $done[] = "✓ Connected to '{$DB_NAME}'";
} catch (PDOException $e) {
    die(render("✗ DB select failed: " . htmlspecialchars($e->getMessage()), [], ["✗ DB connection failed"]));
}

$pdo->exec("SET NAMES utf8mb4");

// ── Step 3: Create all tables ─────────────────────────────────
$tables = [

"CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(60)  NOT NULL,
  `email`         VARCHAR(120) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name`     VARCHAR(120) NOT NULL DEFAULT '',
  `avatar`        VARCHAR(255) NOT NULL DEFAULT '',
  `role`          ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'admin',
  `last_login`    DATETIME DEFAULT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `contact_enquiries` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `company`    VARCHAR(120) NOT NULL DEFAULT '',
  `email`      VARCHAR(120) NOT NULL,
  `phone`      VARCHAR(30)  NOT NULL DEFAULT '',
  `message`    TEXT NOT NULL,
  `status`     ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `ip_address` VARCHAR(45)  NOT NULL DEFAULT '',
  `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(100) NOT NULL,
  `description` TEXT,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `products` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(200) NOT NULL,
  `slug`           VARCHAR(200) NOT NULL,
  `category_id`    INT UNSIGNED NULL DEFAULT NULL,
  `category`       VARCHAR(100) NOT NULL DEFAULT '',
  `description`    TEXT,
  `features`       TEXT,
  `applications`   TEXT,
  `image_url`      VARCHAR(255) NOT NULL DEFAULT '',
  `image_path`     VARCHAR(255) NOT NULL DEFAULT '',
  `certifications` VARCHAR(255) NOT NULL DEFAULT '',
  `is_featured`    TINYINT(1)  NOT NULL DEFAULT 0,
  `is_active`      TINYINT(1)  NOT NULL DEFAULT 1,
  `sort_order`     INT NOT NULL DEFAULT 0,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`),
  INDEX `idx_category_id` (`category_id`),
  INDEX `idx_is_active`   (`is_active`),
  INDEX `idx_is_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `site_settings` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_val` TEXT,
  `label`       VARCHAR(150) NOT NULL DEFAULT '',
  `group_name`  VARCHAR(60)  NOT NULL DEFAULT 'general',
  `sort_order`  INT NOT NULL DEFAULT 0,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id`    INT UNSIGNED NOT NULL,
  `action`      VARCHAR(100) NOT NULL,
  `description` TEXT,
  `ip_address`  VARCHAR(45)  NOT NULL DEFAULT '',
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

foreach ($tables as $sql) {
    try {
        $pdo->exec($sql);
        preg_match('/TABLE IF NOT EXISTS `(\w+)`/', $sql, $m);
        $done[] = "✓ Table ready: " . ($m[1] ?? '?');
    } catch (PDOException $e) { $errors[] = "Table: " . $e->getMessage(); }
}

// Safe ALTER — add any missing columns
foreach ([
    "ALTER TABLE products ADD COLUMN IF NOT EXISTS category_id INT UNSIGNED NULL DEFAULT NULL",
    "ALTER TABLE products ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) NOT NULL DEFAULT ''",
] as $sql) {
    try { $pdo->exec($sql); } catch (PDOException) {}
}
$done[] = "✓ Schema columns verified";

// ── Step 4: Seed admin user ──────────────────────────────────
$hash = password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);
try {
    $pdo->prepare("INSERT IGNORE INTO admin_users (username, email, password_hash, full_name, role)
        VALUES ('admin','admin@evergreenindustry.com',?,'Super Admin','superadmin')")->execute([$hash]);
    $done[] = "✓ Admin user ready  (admin / Admin@1234)";
} catch (PDOException $e) { $errors[] = "Admin: " . $e->getMessage(); }

// ── Step 5: Seed site settings ───────────────────────────────
$settings = [
    ['company_name','EVERGREENINDUSTRY','Company Name','general',1],
    ['tagline','Sustainable Packaging Solutions','Tagline','general',2],
    ['contact_email','info@evergreenindustry.com','Contact Email','contact',1],
    ['contact_phone','+91 73392 85437','Contact Phone','contact',2],
    ['contact_address','No. 2, Tholilpettai, SIDCO Industrial Estate, N.K. Road, Thanjavur (613006), Tamil Nadu','Address','contact',3],
    ['whatsapp_number','+91 73392 85437','WhatsApp Number','contact',4],
    ['meta_title','EVERGREENINDUSTRY | Sustainable Packaging','Meta Title','seo',1],
    ['meta_description','Biodegradable and compostable packaging solutions.','Meta Description','seo',2],
    ['enquiry_notify_email','info@evergreenindustry.com','Site Enquiry Notification Email','notifications',1],
];
$ins = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key,setting_val,label,group_name,sort_order) VALUES (?,?,?,?,?)");
foreach ($settings as $s) { try { $ins->execute($s); } catch (PDOException) {} }
$done[] = "✓ Site settings seeded";

// ── Step 6: Seed categories ──────────────────────────────────
$cats = [
    ['Carry Bags',        'carry-bags',         'Compostable carry bags for retail and daily use.', 1],
    ['Waste Bags',        'waste-bags',         'Heavy-duty compostable liners for waste management.', 2],
    ['Produce Packaging', 'produce-packaging',  'Breathable pouches that keep fresh produce at its best.', 3],
    ['Raw Materials',     'raw-materials',      'Biopolymer granules and resins for eco-packaging manufacturers.', 4],
    ['Lifestyle',         'lifestyle',          'Premium eco-lifestyle bags and accessories.', 5],
];
$insc = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, description, sort_order, is_active) VALUES (?,?,?,?,1)");
foreach ($cats as $c) { try { $insc->execute($c); } catch (PDOException $e) { $errors[] = "Cat: ".$e->getMessage(); } }
$done[] = "✓ Categories seeded (" . count($cats) . " categories)";

// Category id map
$catMap = [];
foreach ($pdo->query("SELECT id, name FROM categories")->fetchAll() as $r) {
    $catMap[$r['name']] = (int)$r['id'];
}

// ── Step 7: Seed 25 products ─────────────────────────────────
$products = [
    // CARRY BAGS
    ['Compostable D-Cut Carry Bags','compostable-d-cut-carry-bags','Carry Bags','Certified compostable D-cut carry bags made from PBAT & corn starch. Ideal for grocery, retail, and daily shopping. Zero microplastics.','• EN 13432 & ASTM D6400 certified\n• Available in GSM 20–50\n• Custom print-ready surface\n• Decomposes in 180 days\n• Food-contact safe','• Grocery & supermarkets\n• Retail outlets\n• Pharmacies','compostable-bags-blank.jpg','EN 13432, ASTM D6400, CPCB',1,1],
    ['Bio Carry Bags — Loop Handle','bio-carry-bags-loop-handle','Carry Bags','Premium loop-handle compostable bags for boutique and fashion retail. Strong enough for 5 kg loads.','• Reinforced loop handle\n• 100% plant-derived starch\n• GSM 35–60 range\n• Satin-finish print surface','• Fashion boutiques\n• Gift shops\n• Cosmetics retail','product-shopping-bag.jpg','EN 13432, ISO 14855',1,2],
    ['T-Shirt Compostable Bags','t-shirt-compostable-bags','Carry Bags','Lightweight T-shirt compostable bags for high-volume takeaway, grocery checkout and casual retail.','• Punched T-shirt handles\n• Ultra-thin 15–25 GSM option\n• High-speed roll format\n• BPI & OK Compost certified','• Takeaway restaurants\n• Street food vendors\n• Grocery checkout','compostable-bags-blank.jpg','BPI Certified, OK Compost',0,3],
    ['Flat-Bottom Gusset Carry Bags','flat-bottom-gusset-carry-bags','Carry Bags','Side-gusset flat-bottom compostable bags for heavier loads. Stable base keeps items upright.','• Side-gusset & flat-bottom\n• Load capacity up to 10 kg\n• 40–80 GSM thickness\n• Full-colour flexo printing','• Supermarkets\n• Bulk food stores\n• Hardware retail','product-shopping-bag.jpg','EN 13432, CIPET Audited',0,4],
    ['Oxo-Biodegradable Carry Bags','oxo-biodegradable-carry-bags','Carry Bags','Oxo-biodegradable carry bags with d2w additive for environments where industrial composting is unavailable.','• d2w pro-oxidant additive\n• Breaks down in 2–5 years outdoors\n• High tensile strength\n• Cost-effective transition option','• Rural retail markets\n• Export packaging\n• Logistics','compostable-bags-blank.jpg','ASTM D6954, CPCB Approved',0,5],
    // WASTE BAGS
    ['Compostable Household Waste Liners','compostable-household-waste-liners','Waste Bags','Heavy-duty compostable liners for kitchen bins, dry waste and organic waste segregation at home.','• Leak-proof star-sealed bottom\n• 30 L / 60 L / 120 L sizes\n• Odour-neutral material\n• EN 13432 home-compost certified','• Household kitchen bins\n• Dry & wet waste segregation\n• Apartment complexes','compostable-waste-bags.jpg','EN 13432, OK Compost HOME',1,6],
    ['Commercial Compostable Bin Liners','commercial-compostable-bin-liners','Waste Bags','Extra-large compostable bin liners for hotels, restaurants, corporate offices and municipal waste collection.','• 120 L – 240 L capacity\n• Puncture-resistant PBAT blend\n• High-heat stable up to 60°C\n• Suitable for organic wet waste','• Hotels & hospitality\n• Restaurants & food courts\n• Municipal SWM programs','compostable-waste-bags.jpg','EN 13432, ASTM D6400, CIPET',1,7],
    ['Medical Waste Compostable Bags','medical-waste-compostable-bags','Waste Bags','Colour-coded compostable bags for segregated bio-medical and non-hazardous clinical waste.','• Yellow / Red / Green colour coding\n• Bio-medical waste compliant\n• Leak-proof double-seam bottom\n• Tie-top closure option','• Clinics & hospitals\n• Diagnostic labs\n• Dental practices','compostable-waste-bags.jpg','BMWM Rules 2016, EN 13432',0,8],
    ['Garden & Yard Waste Bags','garden-yard-waste-bags','Waste Bags','Large paper-reinforced compostable bags for garden clippings, leaves, and yard waste.','• 80 L – 160 L capacity\n• Dual-layer paper + PLA liner\n• Water-resistant for 4 hours\n• Standing open-top design','• Home gardens\n• Landscaping contractors\n• Municipal green-waste','compostable-waste-bags.jpg','EN 13432, BPI Certified',0,9],
    ['Dog Waste Compostable Poop Bags','dog-waste-compostable-poop-bags','Waste Bags','Ultra-thin but leak-proof compostable pet waste bags. Lavender-scented option available.','• 15-micron extra-strong film\n• Lavender-scented variant\n• Roll of 15 bags per tube\n• Tie handles for no-touch disposal','• Pet owners & dog walkers\n• Pet supply stores\n• Veterinary clinics','compostable-waste-bags.jpg','EN 13432, OK Compost',0,10],
    // PRODUCE PACKAGING
    ['Micro-Perforated Breathable Pouches','micro-perforated-breathable-pouches','Produce Packaging','Laser micro-perforated compostable pouches that extend fresh fruit and vegetable shelf life by up to 40%.','• 200–400 micron laser perforations\n• Extends shelf life 35–40%\n• Reduces condensation moisture\n• Heat-sealable top','• Strawberries & berries\n• Cherry tomatoes\n• Fresh herbs','breathable-produce-pouches.jpg','EN 13432, ASTM D6400',1,11],
    ['Compostable Produce Net Bags','compostable-produce-net-bags','Produce Packaging','Open-weave compostable net bags for onions, garlic, citrus, and root vegetables.','• Open-weave PLA netting\n• 500 g – 5 kg capacity\n• Heat-crimped top closure\n• Natural off-white colour','• Onions & garlic\n• Citrus fruits\n• Potatoes & beets','breathable-produce-pouches.jpg','OK Compost HOME, EN 13432',0,12],
    ['Modified Atmosphere Produce Bags','modified-atmosphere-produce-bags','Produce Packaging','Multi-layer MAP compostable pouches that dramatically slow respiration of cut produce and salad mixes.','• 3-layer PLA/barrier/PLA structure\n• Customisable O2 transmission rate\n• Heat-seal & zip-lock variants\n• Gas-flush machine compatible','• Pre-cut salad packs\n• Sliced fruits\n• Ready-to-eat vegetables','breathable-produce-pouches.jpg','EN 13432, FSSC 22000',0,13],
    ['Compostable Flat Produce Pouches','compostable-flat-produce-pouches','Produce Packaging','Simple flat compostable pouches for leafy greens, herbs, and microgreens.','• Thin 18-micron PLA film\n• Full-surface print area\n• Perforated tear-notch\n• Retail hang-hole option','• Leafy greens\n• Microgreens\n• Fresh herbs retail','breathable-produce-pouches.jpg','EN 13432, ASTM D6400',0,14],
    ['Banana Ripening Compostable Sleeves','banana-ripening-compostable-sleeves','Produce Packaging','Specially engineered compostable sleeves that slow banana ripening in transit, reducing post-harvest loss.','• Controlled ethylene permeability\n• UV-stable outer layer\n• Fits standard banana bunch\n• Reduces browning in transit','• Banana exporters\n• Cold-chain logistics\n• Wholesale fruit markets','breathable-produce-pouches.jpg','EN 13432, Rainforest Alliance Compatible',0,15],
    // RAW MATERIALS
    ['PBAT / PLA Biopolymer Granules','pbat-pla-biopolymer-granules','Raw Materials','Industrial-grade PBAT/PLA blended granules — the core material behind Evergreen\'s full product range.','• PBAT:PLA ratio 60:40 (standard) or custom\n• MFI 2–8 g/10 min\n• Processing temp 150–190C\n• Blown film & cast film compatible','• Blown-film carry bags\n• Cast stretch film\n• Thermoformed trays','biopolymer-granules.jpg','EN 13432, ASTM D6400, REACH',1,16],
    ['Thermoplastic Starch (TPS) Granules','thermoplastic-starch-tps-granules','Raw Materials','Corn and cassava starch-based thermoplastic granules for 100% bio-based compostable packaging.','• 70–80% renewable bio-content\n• Low carbon footprint vs PLA\n• Suitable for injection moulding\n• Compatible with PLA blending','• Loose-fill packaging\n• Single-use cutlery\n• Seedling pots','biopolymer-granules.jpg','EN 13432, DIN CERTCO, USDA BioPreferred',0,17],
    ['PLA (Polylactic Acid) Pellets','pla-polylactic-acid-pellets','Raw Materials','High-clarity PLA pellets for transparent packaging films, rigid containers, and coatings.','• >99% optical clarity\n• Rigid film & extrusion grade\n• Heat resistance up to 55C\n• Fast compostability in industrial facilities','• Clear clamshell containers\n• Window pouches\n• Cold beverage cups','biopolymer-granules.jpg','EN 13432, FDA GRAS',0,18],
    ['Bio-Based Masterbatch Pigments','bio-based-masterbatch-pigments','Raw Materials','Concentrated pigment masterbatches for PBAT/PLA resins — vivid brand colours without losing compostability certification.','• 20–40% pigment loading\n• Compatible with EN 13432 films\n• Non-toxic, food-contact safe\n• Pantone colour matching','• Coloured carry bags\n• Branded waste liners\n• Retail packaging','biopolymer-granules.jpg','REACH, RoHS, EN 13432 Compatible',0,19],
    ['Compostable Adhesive & Sealant Resin','compostable-adhesive-sealant-resin','Raw Materials','Hot-melt compostable adhesive resin for seaming and laminating multi-layer bio-packaging.','• 100% compostable formula\n• Sealing temp range 80–130C\n• Strong peel resistance\n• Water-based and solvent-free','• Pouch heat-sealing\n• Lamination adhesive\n• Multi-layer film bonding','biopolymer-granules.jpg','EN 13432, ASTM D6400, FDA',0,20],
    // LIFESTYLE
    ['Eco Lifestyle Tote Bags','eco-lifestyle-tote-bags','Lifestyle','Premium reusable tote bags crafted from natural jute and compostable inner liner for everyday use.','• Natural jute outer\n• Compostable PLA inner lining\n• 15 kg carry capacity\n• Custom print & embroidery','• Grocery shopping\n• Farmers markets\n• Corporate gifting','eco-lifestyle-bag.jpg','GOTS (Jute), EN 13432 (Liner)',1,21],
    ['Compostable Shopping Bags — Premium','compostable-shopping-bags-premium','Lifestyle','High-end compostable shopping bags with rope handles and matte finish for luxury retail.','• Matte laminate finish\n• Twisted rope cotton handles\n• Rigid bottom insert\n• Spot UV print option','• Luxury boutiques\n• Jewellery stores\n• Premium cosmetics','product-shopping-bag.jpg','EN 13432, ISO 14001',1,22],
    ['Seed Paper Gift Wrap','seed-paper-gift-wrap','Lifestyle','Plantable seed-embedded paper for wrapping gifts. After unwrapping, plant it to grow wildflowers or herbs.','• Embedded wildflower/herb seeds\n• A3 & A4 sheet sizes\n• Water-colour pattern surface\n• FSC-certified paper base','• Corporate gifting\n• Retail gift wrapping\n• Wedding & event favours','eco-lifestyle-bag.jpg','FSC Certified, 100% Plastic-Free',0,23],
    ['Bamboo & PLA Coffee Cups','bamboo-pla-coffee-cups','Lifestyle','Single-use compostable coffee cups made from bamboo fibre and PLA lining. Heat-stable to 90C.','• 8 oz / 12 oz / 16 oz sizes\n• PLA leak-proof inner coating\n• Offset print, QR-code friendly\n• Compostable lid sold separately','• Cafes & coffee shops\n• Airport lounges\n• Corporate canteens','eco-lifestyle-bag.jpg','EN 13432, FDA Food Contact',0,24],
    ['Compostable Cutlery Set','compostable-cutlery-set','Lifestyle','CPLA cutlery set — fork, knife, spoon — heat-stable to 85C. Premium biodegradable alternative to plastic cutlery.','• Fork + Knife + Spoon set\n• CPLA heat-stable to 85C\n• Compostable kraft wrap\n• White & natural wood-look finish','• Food delivery services\n• Catering & events\n• Airline meals','eco-lifestyle-bag.jpg','EN 13432, ASTM D6400, FDA',0,25],
];

$ins = $pdo->prepare(
    "INSERT INTO products
       (name,slug,category_id,category,description,features,applications,image_url,image_path,certifications,is_featured,is_active,sort_order)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,1,?)
     ON DUPLICATE KEY UPDATE
       category_id=VALUES(category_id), category=VALUES(category),
       description=VALUES(description), features=VALUES(features),
       applications=VALUES(applications), image_url=VALUES(image_url),
       certifications=VALUES(certifications), is_featured=VALUES(is_featured),
       sort_order=VALUES(sort_order), is_active=1"
);

$inserted = $updated = 0;
foreach ($products as [$name,$slug,$cat,$desc,$feats,$apps,$imgUrl,$certs,$featured,$order]) {
    $catId = $catMap[$cat] ?? null;
    try {
        $before = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE slug=" . $pdo->quote($slug))->fetchColumn();
        $ins->execute([$name,$slug,$catId,$cat,$desc,$feats,$apps,$imgUrl,'',$certs,$featured,$order]);
        $before === 0 ? $inserted++ : $updated++;
    } catch (PDOException $e) { $errors[] = "$name: " . $e->getMessage(); }
}
$done[] = "✓ Products: {$inserted} inserted, {$updated} updated";

// Back-fill category_id on any older rows
try {
    $pdo->exec("UPDATE products p JOIN categories c ON c.name=p.category SET p.category_id=c.id WHERE p.category_id IS NULL");
    $done[] = "✓ category_id back-filled on existing rows";
} catch (PDOException $e) { $errors[] = "Back-fill: ".$e->getMessage(); }

// Final counts
$totalP = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalC = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn();
$done[] = "✓ Done — {$totalP} active products, {$totalC} categories in DB";

echo render(null, $done, $errors, (int)$totalP, (int)$totalC);

// ─── HTML renderer ────────────────────────────────────────────────────────
function render(?string $errMsg, array $done = [], array $errors = [], int $totalP = 0, int $totalC = 0): string {
    ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Evergreen — Full Migration</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,sans-serif;background:#f0fdf4;color:#0e2617;padding:32px 16px}
.wrap{max-width:680px;margin:0 auto}
h1{font-size:1.5rem;color:#14532d;margin-bottom:4px}
.sub{color:#4a7054;font-size:.875rem;margin-bottom:24px}
.card{background:#fff;border:1px solid #bbf7d0;border-radius:14px;padding:22px;margin-bottom:16px}
.err-card{border-color:#fecaca}
h2{font-size:.95rem;color:#166534;margin-bottom:10px}
h2.red{color:#dc2626}
li{font-size:.85rem;line-height:1.8;list-style:none}
li.ok::before{content:"✓ ";color:#16a34a;font-weight:700}
li.er{color:#dc2626}li.er::before{content:"✗ ";font-weight:700}
.stats{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px}
.stat{background:#fff;border:1px solid #86efac;border-radius:12px;padding:14px;text-align:center}
.stat-n{font-size:2.2rem;font-weight:800;color:#15803d}
.stat-l{font-size:.75rem;color:#4a7054;margin-top:2px}
.warn{background:#fef9c3;border:1px solid #fde047;border-radius:12px;padding:18px;font-size:.85rem;line-height:1.8}
code{background:#f3f4f6;padding:1px 5px;border-radius:4px;font-size:.8rem}
a.btn{display:inline-block;background:#166534;color:#fff;text-decoration:none;padding:8px 20px;border-radius:50px;font-size:.83rem;font-weight:600;margin-top:14px}
</style>
</head>
<body>
<div class="wrap">
  <h1>🌿 Evergreen — Full Migration</h1>
  <p class="sub">Creates all tables + seeds 5 categories + 25 products.</p>

  <?php if ($errMsg): ?>
    <div class="card err-card"><h2 class="red">Connection Failed</h2><p style="color:#dc2626;font-size:.875rem"><?= $errMsg ?></p></div>
  <?php else: ?>

  <div class="stats">
    <div class="stat"><div class="stat-n"><?= $totalP ?></div><div class="stat-l">Active Products</div></div>
    <div class="stat"><div class="stat-n"><?= $totalC ?></div><div class="stat-l">Categories</div></div>
  </div>

  <?php if ($errors): ?>
  <div class="card err-card">
    <h2 class="red">Errors</h2>
    <ul><?php foreach($errors as $e): ?><li class="er"><?= htmlspecialchars($e) ?></li><?php endforeach ?></ul>
  </div>
  <?php endif; ?>

  <div class="card">
    <h2>Completed Steps</h2>
    <ul><?php foreach($done as $d): ?><li class="ok"><?= htmlspecialchars($d) ?></li><?php endforeach ?></ul>
  </div>

  <div class="warn">
    <strong>⚠ Delete these files from your server immediately!</strong><br>
    <code>backend/run_migrate.php</code> &nbsp;|&nbsp; <code>backend/live_diag.php</code><br><br>
    Admin login: <a href="/admin/login.php"><strong>/admin/login.php</strong></a><br>
    Username: <code>admin</code> &nbsp;&nbsp; Password: <code>Admin@1234</code>
  </div>

  <a class="btn" href="/admin/login.php">Go to Admin Panel →</a>

  <?php endif; ?>
</div>
</body>
</html>
<?php
    return ob_get_clean();
}

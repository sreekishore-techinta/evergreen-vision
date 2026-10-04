<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo = db();

// ── Ensure hero_slides table exists with page_key and page_name ──
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `hero_slides` (
      `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
      `page_key`    VARCHAR(50)   NOT NULL DEFAULT 'home',
      `page_name`   VARCHAR(100)  NOT NULL DEFAULT 'Home Page',
      `title`       VARCHAR(200)  NOT NULL DEFAULT '',
      `subtitle`    VARCHAR(300)  NOT NULL DEFAULT '',
      `description` TEXT,
      `button_text` VARCHAR(100)  NOT NULL DEFAULT '',
      `button_url`  VARCHAR(255)  NOT NULL DEFAULT '',
      `image_url`   VARCHAR(255)  NOT NULL DEFAULT '',
      `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
      `sort_order`  INT           NOT NULL DEFAULT 0,
      `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_page` (`page_key`),
      INDEX `idx_sort` (`sort_order`),
      INDEX `idx_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if page_key and page_name columns exist
$cols = $pdo->query("DESCRIBE hero_slides")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('page_key', $cols)) {
    $pdo->exec("ALTER TABLE `hero_slides` ADD COLUMN `page_key` VARCHAR(50) NOT NULL DEFAULT 'home' AFTER `id`");
    $pdo->exec("ALTER TABLE `hero_slides` ADD INDEX `idx_page` (`page_key`)");
}
if (!in_array('page_name', $cols)) {
    $pdo->exec("ALTER TABLE `hero_slides` ADD COLUMN `page_name` VARCHAR(100) NOT NULL DEFAULT 'Home Page' AFTER `page_key`");
}

// ── Website Pages Configuration ──────────────────────────────
$PAGES = [
    'home' => [
        'name'        => 'Home Page',
        'route'       => '/',
        'badge_color' => '#15803d',
        'badge_bg'    => '#dcfce7',
        'desc'        => 'Main landing hero slider with auto-advancing banners.',
    ],
    'about' => [
        'name'        => 'About Us',
        'route'       => '/about',
        'badge_color' => '#0369a1',
        'badge_bg'    => '#e0f2fe',
        'desc'        => 'Company heritage, manufacturing plant, and mission hero.',
    ],
    'products' => [
        'name'        => 'Products',
        'route'       => '/products',
        'badge_color' => '#854d0e',
        'badge_bg'    => '#fef9c3',
        'desc'        => 'Commercial catalog hero header and category showcase.',
    ],
    'segment' => [
        'name'        => 'Solutions / Segments',
        'route'       => '/segment',
        'badge_color' => '#6d28d9',
        'badge_bg'    => '#f3e8ff',
        'desc'        => 'Seven industry sectors & custom biopolymer solutions hero.',
    ],
    'sustainability' => [
        'name'        => 'Sustainability',
        'route'       => '/sustainability',
        'badge_color' => '#047857',
        'badge_bg'    => '#d1fae5',
        'desc'        => 'Circular material lifecycle and compostability narrative hero.',
    ],
    'certificate' => [
        'name'        => 'Certifications',
        'route'       => '/certificate',
        'badge_color' => '#b45309',
        'badge_bg'    => '#fef3c7',
        'desc'        => 'Govt. CPCB approval & IS/ISO 17088 compliance showcase.',
    ],
    'contact' => [
        'name'        => 'Contact Us',
        'route'       => '/contact',
        'badge_color' => '#374151',
        'badge_bg'    => '#f3f4f6',
        'desc'        => 'Enterprise inquiries, sample requests, and factory address hero.',
    ],
];

// ── Ensure upload directories exist and seed from any available source ──
(function() {
    // admin/ is 1 level deep, so dirname(__DIR__) = project/web root
    $root         = dirname(__DIR__);
    $slides_dir   = $root . '/uploads/slides/';
    $products_dir = $root . '/uploads/products/';

    if (!is_dir($slides_dir))   @mkdir($slides_dir, 0755, true);
    if (!is_dir($products_dir)) @mkdir($products_dir, 0755, true);

    // Source candidates in priority order:
    // 1. src/assets/ (local dev)
    // 2. public/uploads/slides/ (Vite copies public/ → root on build/deploy)
    $asset_sources = [
        $root . '/src/assets/',
        $root . '/public/uploads/slides/',
    ];

    $assets = [
        'home.png', 'home hero sec.png', 'evergreen-hero.jpg', 'sprout-in-hands.jpg',
        'product-collection.jpg', 'solution.png', 'substain.png', 'cpcb-certificate.png',
        'compostable-bags-blank.jpg', 'compostable-waste-bags.jpg', 'breathable-produce-pouches.jpg',
        'biopolymer-granules.jpg', 'eco-lifestyle-bag.jpg', 'product-shopping-bag.jpg',
        'product-bio-carry.jpg', 'product-tshirt-bag.jpg', 'manufacturing.jpg', 'material-journey.jpg',
        'cta-bags-showcase.jpg', 'e-logo.png'
    ];

    foreach ($assets as $file) {
        $slide_dest = $slides_dir . $file;
        $prod_dest  = $products_dir . $file;
        foreach ($asset_sources as $src_dir) {
            $src = $src_dir . $file;
            if (file_exists($src)) {
                if (!file_exists($slide_dest)) @copy($src, $slide_dest);
                if (!file_exists($prod_dest))  @copy($src, $prod_dest);
                break;
            }
        }
    }
})();

// Helper to resolve image URL with disk existence check & multi-source fallback
function resolve_slide_img($url): string {
    if (!$url) return '';
    if (preg_match('#^https?://#i', $url)) return $url;

    $clean = ltrim($url, '/');

    // Normalize bare filenames like "home.png" → uploads/slides/home.png
    if (!str_starts_with($clean, 'uploads/') && !str_starts_with($clean, 'src/assets/')) {
        $clean = 'uploads/slides/' . $clean;
    }

    // admin/ is 1 level deep — root is dirname(__DIR__)
    $root      = dirname(__DIR__);
    $disk_path = $root . '/' . $clean;

    // File already exists at target — serve it
    if (file_exists($disk_path)) {
        return rtrim(BASE_URL, '/') . '/' . $clean;
    }

    // Not found — try to auto-copy from known source locations
    $fname    = basename($clean);
    $fallbacks = [
        $root . '/src/assets/' . $fname,            // local dev
        $root . '/public/uploads/slides/' . $fname, // Vite deploy source
    ];

    foreach ($fallbacks as $src) {
        if (file_exists($src)) {
            @copy($src, $disk_path);
            return rtrim(BASE_URL, '/') . '/' . $clean;
        }
    }

    // Return the URL anyway (browser will show broken image)
    return rtrim(BASE_URL, '/') . '/' . $clean;
}

// ── Fetch all slides from DB ──────────────────────────────────
$raw_slides = $pdo->query('SELECT * FROM hero_slides ORDER BY page_key ASC, sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);

// If database is empty, seed defaults
if (empty($raw_slides)) {
    $defaults = [
        ['home', 'Home Page', 'Pure Starch. Zero Plastic. 100% Certified Compostable.', 'Sustainable Packaging Engineered for Modern Enterprise', 'Customisable, certified biodegradable carry bags, waste liners, and biopolymer solutions designed to meet commercial compliance without compromising performance.', 'Explore Products', '/products', '/uploads/slides/home.png', 1],
        ['home', 'Home Page', 'Better Packaging for a Greener Tomorrow', 'Circularity by Design', 'Sustainable alternatives to conventional plastic — engineered for usability, durability and the realities of modern business.', 'Our Solutions', '/segment', '/uploads/slides/evergreen-hero.jpg', 2],
        ['about', 'About Us', 'Industry can grow differently.', 'Our Heritage & Purpose', 'We believe better material decisions can support both commercial business performance and the living world beyond it. Built to provide an honest, scalable path away from petroleum polymers.', 'Our Story', '/about', '/uploads/slides/sprout-in-hands.jpg', 1],
        ['products', 'Products', 'Engineered for Enterprise.', 'Commercial-Grade Compostable Packaging', 'Explore biodegradable bags and compostable packaging solutions engineered for modern commercial operations.', 'View Catalog', '/products', '/uploads/slides/product-collection.jpg', 1],
        ['segment', 'Solutions / Segments', 'Solutions Tailored Across 7 Core Industry Sectors', 'Our Segments & Capabilities', 'Packaging, Food Service, Agriculture, Horticulture, Medical, Waste Management, and Disposable Cutlery.', 'Explore Solutions', '/segment', '/uploads/slides/solution.png', 1],
        ['sustainability', 'Sustainability', 'A lifecycle, not a label.', 'Our Circular Approach', 'We look beyond the product itself—considering material, manufacture, use and what happens next.', 'Learn More', '/sustainability', '/uploads/slides/substain.png', 1],
        ['certificate', 'Certifications', 'Official Certification & CPCB Compliance', 'Central Pollution Control Board Approved', 'Central Pollution Control Board (CPCB) official government certificate for marketing and selling 100% compostable carry bags compliant with IS/ISO:17088.', 'View Certificate', '/certificate', '/uploads/slides/cpcb-certificate.png', 1],
        ['contact', 'Contact Us', 'Start a conversation about better packaging.', 'Get in Touch with Evergreen', 'Speak with Evergreen Industry about sustainable packaging solutions, bulk enterprise inquiries and distribution partnerships.', 'Contact Us', '/contact', '/uploads/slides/evergreen-hero.jpg', 1],
    ];
    $ins = $pdo->prepare("INSERT INTO hero_slides (page_key, page_name, title, subtitle, description, button_text, button_url, image_url, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,1)");
    foreach ($defaults as $d) { $ins->execute($d); }
    $raw_slides = $pdo->query('SELECT * FROM hero_slides ORDER BY page_key ASC, sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
}

// Build normalized slides with resolved image URLs
$slides = [];
$slides_by_page = [];
foreach ($raw_slides as $s) {
    $s['display_image_url'] = resolve_slide_img($s['image_url']);
    $slides[] = $s;
    $pkey = $s['page_key'] ?: 'home';
    if (!isset($slides_by_page[$pkey])) $slides_by_page[$pkey] = [];
    $slides_by_page[$pkey][] = $s;
}

// ── Actual current hero content visible on each live page ───────────
// These match what is hardcoded in the React route components.
$PAGE_HEROES = [
    'home' => [
        'eyebrow'     => 'Let\'s Reduce Plastic',
        'headline'    => 'Sustainable Packaging for a Greener Tomorrow',
        'copy'        => 'High-quality biodegradable and compostable packaging solutions for a cleaner, healthier planet.',
        'cta_primary' => 'Get a Quote',
        'cta_sec'     => 'WhatsApp Now',
        'image_hint'  => 'home.png',
    ],
    'about' => [
        'eyebrow'     => 'Our Heritage & Purpose',
        'headline'    => 'Industry can grow differently.',
        'copy'        => 'We believe better material decisions can support both commercial business performance and the living world beyond it. Built to provide an honest, scalable path away from petroleum polymers.',
        'cta_primary' => '',
        'cta_sec'     => '',
        'image_hint'  => 'sprout-in-hands.jpg',
    ],
    'products' => [
        'eyebrow'     => 'Commercial-Grade Compostable Packaging',
        'headline'    => 'Engineered for Enterprise.',
        'copy'        => 'Explore biodegradable bags and compostable packaging solutions engineered for modern commercial operations.',
        'cta_primary' => 'View Catalog',
        'cta_sec'     => '',
        'image_hint'  => 'product-collection.jpg',
    ],
    'segment' => [
        'eyebrow'     => 'Our Segments & Capabilities',
        'headline'    => 'Solutions Tailored Across 7 Core Industry Sectors',
        'copy'        => 'Packaging, Food Service, Agriculture, Horticulture, Medical, Waste Management, and Disposable Cutlery.',
        'cta_primary' => 'Explore Solutions',
        'cta_sec'     => '',
        'image_hint'  => 'solution.png',
    ],
    'sustainability' => [
        'eyebrow'     => 'Our Circular Approach',
        'headline'    => 'A lifecycle, not a label.',
        'copy'        => 'We look beyond the product itself—considering material, manufacture, use and what happens next.',
        'cta_primary' => 'Learn More',
        'cta_sec'     => '',
        'image_hint'  => 'substain.png',
    ],
    'certificate' => [
        'eyebrow'     => 'Central Pollution Control Board Approved',
        'headline'    => 'Official Certification & CPCB Compliance',
        'copy'        => 'Central Pollution Control Board (CPCB) official government certificate for marketing and selling 100% compostable carry bags compliant with IS/ISO:17088.',
        'cta_primary' => 'View Certificate',
        'cta_sec'     => '',
        'image_hint'  => 'cpcb-certificate.png',
    ],
    'contact' => [
        'eyebrow'     => 'Contact us',
        'headline'    => "Let's shape a better package.",
        'copy'        => 'Tell us what your business needs. We\'ll help you explore a more responsible way forward.',
        'cta_primary' => 'Send Enquiry',
        'cta_sec'     => '',
        'image_hint'  => 'evergreen-hero.jpg',
    ],
];

$page_title  = 'Web Slider & Hero Sections';
$breadcrumbs = [['Web Slider', '']];
include __DIR__ . '/includes/header.php';
?>

<!-- ══════════════════════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════════════════════ -->
<div class="page-header" style="margin-bottom:20px">
  <div>
    <h1 class="page-title">Web Slider &amp; Hero Sections</h1>
    <p class="page-subtitle">View and customize the current hero slider images, headlines, and call-to-action buttons for all pages across the website.</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-secondary" title="View live frontend website">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
      Preview Website
    </a>
    <button class="btn btn-primary" id="btn-add-slide" onclick="openModal()">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      Add New Slide
    </button>
  </div>
</div>

<!-- Flash messages -->
<div id="flash-zone"></div>

<!-- ══════════════════════════════════════════════════════════
     LIVE SHOWCASE — CURRENT HERO SECTIONS (ACTUAL SITE CONTENT)
═══════════════════════════════════════════════════════════ -->
<div class="card" style="margin-bottom:24px;border:1px solid var(--border);box-shadow:var(--shadow-sm)">
  <div class="card-header" style="border-bottom:1px solid var(--border);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div class="card-title" style="font-size:1.02rem;display:flex;align-items:center;gap:8px">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="var(--green-600)" style="width:20px;height:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
      <strong>Current Live Hero Sections (All Pages)</strong>
      <span class="badge" style="background:var(--green-100);color:var(--green-700);font-size:.72rem;padding:2px 8px;border-radius:20px"><?= count($PAGES) ?> Pages</span>
    </div>
    <span class="text-muted text-sm">Showing the actual content currently visible to site visitors</span>
  </div>

  <!-- Info notice -->
  <div style="margin:0 20px;margin-top:16px;padding:10px 14px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;display:flex;align-items:flex-start;gap:10px;font-size:.8rem;color:#92400e">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" style="width:16px;height:16px;flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
    <span>These cards show the <strong>actual hero content currently on the live website</strong>. The "Manage Slides" table below controls the database slider entries which can be used to update page hero sections.</span>
  </div>

  <div style="padding:20px;width:100%;box-sizing:border-box">
    <div class="banner-cards-grid">
      <?php foreach ($PAGES as $pkey => $pcfg):
        $hero  = $PAGE_HEROES[$pkey] ?? [];
        $p_slides = $slides_by_page[$pkey] ?? [];
        $active_slide = !empty($p_slides) ? $p_slides[0] : null;
        // Prefer DB slide image for thumbnail if available, else try asset fallback
        $slide_img = $active_slide ? $active_slide['display_image_url'] : '';
        if (!$slide_img && !empty($hero['image_hint'])) {
            $slide_img = resolve_slide_img('/uploads/slides/' . $hero['image_hint']);
        }
        $display_headline = $hero['headline'] ?? ($active_slide['title'] ?? '');
        $display_eyebrow  = $hero['eyebrow']  ?? ($active_slide['subtitle'] ?? '');
        $display_copy     = $hero['copy']     ?? ($active_slide['description'] ?? $pcfg['desc']);
        $display_cta      = $hero['cta_primary'] ?? ($active_slide['button_text'] ?? '');
      ?>
        <div class="page-banner-card">
          <!-- Banner Image Thumbnail with overlay -->
          <div style="position:relative;width:100%;height:165px;background:#e2e8f0;overflow:hidden">
            <?php if ($slide_img): ?>
              <img src="<?= htmlspecialchars($slide_img) ?>"
                   alt="<?= htmlspecialchars($display_headline ?: $pcfg['name']) ?>"
                   style="width:100%;height:100%;object-fit:cover"
                   class="banner-thumb-img"
                   onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
              <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;background:var(--sand);color:var(--muted)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:36px;height:36px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5M21 6.75A2.25 2.25 0 0018.75 4.5H5.25A2.25 2.25 0 003 6.75v10.5A2.25 2.25 0 005.25 19.5H18.75A2.25 2.25 0 0021 17.25V6.75z"/></svg>
              </div>
            <?php else: ?>
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--sand);color:var(--muted)">
                <span style="font-size:.84rem;font-weight:500">No Image</span>
              </div>
            <?php endif; ?>

            <!-- Page Badge Overlay -->
            <div style="position:absolute;top:10px;left:10px;z-index:2;display:flex;align-items:center;gap:6px">
              <span style="background:<?= $pcfg['badge_bg'] ?>;color:<?= $pcfg['badge_color'] ?>;font-weight:700;font-size:.72rem;padding:3px 10px;border-radius:20px;box-shadow:0 2px 4px rgba(0,0,0,.15);border:1px solid rgba(0,0,0,.06)">
                <?= htmlspecialchars($pcfg['name']) ?>
              </span>
            </div>

            <!-- Live indicator -->
            <div style="position:absolute;top:10px;right:10px;z-index:2">
              <span style="background:#16a34a;color:#fff;font-weight:700;font-size:.66rem;padding:3px 8px;border-radius:12px;display:flex;align-items:center;gap:4px">
                <span style="width:5px;height:5px;background:#86efac;border-radius:50%;display:inline-block"></span> LIVE
              </span>
            </div>

            <!-- Gradient overlay for text legibility -->
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);z-index:1"></div>
            <!-- Eyebrow on image -->
            <?php if ($display_eyebrow): ?>
            <div style="position:absolute;bottom:10px;left:12px;right:12px;z-index:2">
              <span style="font-size:.65rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#86efac"><?= htmlspecialchars($display_eyebrow) ?></span>
            </div>
            <?php endif; ?>
          </div>

          <!-- Content Details -->
          <div style="padding:14px 16px;flex:1;display:flex;flex-direction:column;justify-content:space-between;min-width:0">
            <div>
              <h3 style="font-size:.95rem;font-weight:700;color:var(--ink);line-height:1.3;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                <?= htmlspecialchars($display_headline ?: 'No Headline') ?>
              </h3>
              <p style="font-size:.8rem;color:var(--muted);line-height:1.45;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                <?= htmlspecialchars($display_copy) ?>
              </p>
            </div>

            <!-- Card Bottom Bar -->
            <div style="border-top:1px solid #f1f5f9;padding-top:10px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
              <div style="display:flex;align-items:center;gap:6px;min-width:0">
                <?php if ($display_cta): ?>
                  <span style="font-size:.72rem;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;padding:2px 8px;border-radius:12px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:130px">
                    <?= htmlspecialchars($display_cta) ?>
                  </span>
                <?php else: ?>
                  <span style="font-size:.72rem;color:var(--muted)">No CTA button</span>
                <?php endif; ?>
              </div>
              <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                <a href="<?= BASE_URL . ltrim($pcfg['route'], '/') ?>" target="_blank" class="btn btn-ghost btn-sm" style="padding:4px 8px;font-size:.75rem;display:inline-flex;align-items:center;gap:4px" title="Preview live page">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                  Preview
                </a>
                <?php if ($active_slide): ?>
                  <button type="button" class="btn btn-secondary btn-sm" style="padding:4px 9px;font-size:.78rem;display:inline-flex;align-items:center;gap:4px" onclick="editSlide(<?= $active_slide['id'] ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    Edit DB Slide
                  </button>
                <?php else: ?>
                  <button type="button" class="btn btn-primary btn-sm" style="padding:4px 10px;font-size:.78rem" onclick="openModalForPage('<?= $pkey ?>', '<?= htmlspecialchars($pcfg['name']) ?>')">
                    + Add Slide
                  </button>
                <?php endif; ?>
              </div>
            </div>

            <!-- Slide list if multiple slides on this page -->
            <?php if (count($p_slides) > 1): ?>
              <div style="margin-top:10px;padding-top:10px;border-top:1px dashed var(--border);display:flex;flex-direction:column;gap:5px">
                <div style="display:flex;align-items:center;justify-content:space-between">
                  <span style="font-size:.71rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em">DB Slides (<?= count($p_slides) ?>):</span>
                  <button type="button" class="btn btn-ghost btn-sm" style="font-size:.7rem;padding:1px 5px;color:var(--green-700)" onclick="openModalForPage('<?= $pkey ?>', '<?= htmlspecialchars($pcfg['name']) ?>')">+ Add Slide</button>
                </div>
                <?php foreach ($p_slides as $s_idx => $ps): ?>
                  <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 8px;background:#f8faf8;border:1px solid #eef2ee;border-radius:6px;gap:6px">
                    <div style="display:flex;align-items:center;gap:6px;overflow:hidden;min-width:0">
                      <span style="font-size:.68rem;font-weight:700;color:var(--green-700);background:var(--green-100);border-radius:4px;padding:1px 5px;flex-shrink:0">#<?= $s_idx + 1 ?></span>
                      <span style="font-size:.75rem;font-weight:600;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($ps['title']) ?>">
                        <?= htmlspecialchars($ps['title'] ?: 'Slide #'.($s_idx+1)) ?>
                      </span>
                    </div>
                    <div style="display:flex;gap:4px;flex-shrink:0">
                      <button type="button" class="btn btn-ghost btn-sm" style="padding:2px 7px;font-size:.72rem" onclick="editSlide(<?= $ps['id'] ?>)">Edit</button>
                      <button type="button" class="btn btn-ghost btn-sm" style="padding:2px 7px;font-size:.72rem;color:#ef4444" onclick="deleteSlide(<?= $ps['id'] ?>, this)" title="Delete slide #<?= $s_idx + 1 ?>">✕ Del</button>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     ALL SLIDES MANAGEMENT TABLE & FILTER TABS
═══════════════════════════════════════════════════════════ -->
<div class="card" style="margin-bottom:24px">
  <div class="card-header" style="border-bottom:1px solid var(--border);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div class="card-title" style="display:flex;align-items:center;gap:8px">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
        <strong>Manage Slides &amp; Hero Banners</strong>
        <span class="badge" style="background:var(--green-100);color:var(--green-700);font-size:.72rem;padding:2px 8px;border-radius:20px" id="slide-count"><?= count($slides) ?></span>
      </div>
      <div style="font-size:.8rem;color:var(--muted);margin-top:2px">Drag rows to reorder within a page · Changes save automatically</div>
    </div>

    <!-- Quick Actions -->
    <div style="display:flex;gap:8px">
      <button class="btn btn-secondary btn-sm" onclick="filterByPage('all')" id="tab-all-btn">
        Show All Pages
      </button>
    </div>
  </div>

  <!-- Page Filter Tabs -->
  <div style="padding:12px 20px;border-bottom:1px solid var(--border);background:#fafcfa;display:flex;align-items:center;flex-wrap:wrap;gap:8px">
    <span style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-right:4px">Page Filter:</span>
    <button type="button" class="page-filter-pill active" data-page="all" onclick="filterByPage('all')">
      All Pages (<?= count($slides) ?>)
    </button>
    <?php foreach ($PAGES as $pkey => $pcfg): ?>
      <?php $p_cnt = count($slides_by_page[$pkey] ?? []); ?>
      <button type="button" class="page-filter-pill" data-page="<?= $pkey ?>" onclick="filterByPage('<?= $pkey ?>')">
        <?= htmlspecialchars($pcfg['name']) ?> (<?= $p_cnt ?>)
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Table Container -->
  <div id="slides-container">
    <div class="table-wrap">
      <table id="slides-table">
        <thead>
          <tr>
            <th style="width:36px"></th>
            <th style="width:90px">Hero Image</th>
            <th style="width:150px">Page</th>
            <th>Headline / Subtitle</th>
            <th style="width:130px">CTA Button</th>
            <th style="width:85px">Status</th>
            <th style="width:75px;text-align:center">Order</th>
            <th style="width:110px;text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody id="slides-tbody">
          <!-- Populated by JS refreshTable() -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL — Add / Edit Slide & Hero Banner
═══════════════════════════════════════════════════════════ -->
<div id="slide-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.6);overflow-y:auto;padding:24px 16px;backdrop-filter:blur(3px)">
  <div style="background:#fff;border-radius:18px;max-width:700px;margin:20px auto;box-shadow:0 25px 70px rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.2);overflow:hidden">

    <!-- Modal Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#f9fafb">
      <div>
        <h2 id="modal-title" style="font-size:1.15rem;font-weight:700;margin:0;color:#111">Add New Hero Slide</h2>
        <p id="modal-sub" style="margin:2px 0 0;font-size:.82rem;color:#6b7280">Select target page, upload image, and customize the text</p>
      </div>
      <button type="button" onclick="closeModal()" style="background:none;border:none;cursor:pointer;color:#6b7280;padding:6px;border-radius:8px">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Modal Form -->
    <form id="slide-form" enctype="multipart/form-data" onsubmit="submitSlide(event)">
      <input type="hidden" id="slide-id" name="slide_id" value="">
      <input type="hidden" id="f-image-url-raw" name="image_url" value="">

      <div style="padding:24px;display:grid;gap:18px">

        <!-- Target Page Selector -->
        <div>
          <label class="form-label" for="f-page-key" style="font-weight:600">Target Website Page <span style="color:#ef4444">*</span></label>
          <select id="f-page-key" name="page_key" class="form-control" style="font-weight:500;height:42px" onchange="syncPageName(this.value)">
            <?php foreach ($PAGES as $pkey => $pcfg): ?>
              <option value="<?= $pkey ?>"><?= htmlspecialchars($pcfg['name']) ?> (<?= htmlspecialchars($pcfg['route']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" id="f-page-name" name="page_name" value="Home Page">
          <div class="form-hint" style="font-size:.76rem;color:var(--muted);margin-top:4px">Controls which page this hero banner/slider appears on.</div>
        </div>

        <!-- Hero Image Upload with Drag & Drop & Live Preview -->
        <div>
          <label class="form-label" style="font-weight:600;display:block;margin-bottom:6px">Hero Banner Image <span style="color:#ef4444">*</span></label>
          
          <!-- Live Preview -->
          <div id="img-preview-wrap" style="margin-bottom:12px;display:none;position:relative">
            <img id="img-preview" src="" alt="Preview" style="width:100%;max-height:220px;object-fit:cover;border-radius:12px;border:2px solid var(--border)">
            <div style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.7);color:#fff;padding:3px 10px;border-radius:8px;font-size:.72rem;backdrop-filter:blur(4px)" id="img-current-label">Current Image</div>
          </div>

          <!-- Drop Area -->
          <div id="drop-zone"
               onclick="document.getElementById('img-file').click()"
               ondragover="event.preventDefault();this.style.borderColor='var(--green-500)';this.style.background='var(--green-50)'"
               ondragleave="this.style.borderColor='#d1d5db';this.style.background='#fafafa'"
               ondrop="handleDrop(event)"
               style="border:2px dashed #d1d5db;border-radius:12px;padding:26px 20px;text-align:center;cursor:pointer;transition:all .2s;background:#fafafa">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--green-600)" style="width:36px;height:36px;display:block;margin:0 auto 8px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
            <div style="font-size:.88rem;font-weight:600;color:#1f2937">Click to upload new banner image or drag &amp; drop</div>
            <div style="font-size:.76rem;color:#6b7280;margin-top:3px">Recommended: 1920×1080px or 16:9 ratio · Max 5 MB (JPEG, PNG, WebP)</div>
          </div>
          <input type="file" id="img-file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none" onchange="previewFile(this)">
        </div>

        <!-- Headline / Title -->
        <div>
          <label class="form-label" for="f-title" style="font-weight:600">Headline / Main Title <span style="color:#ef4444">*</span></label>
          <input type="text" id="f-title" name="title" class="form-control" placeholder="e.g. Pure Starch. Zero Plastic. 100% Certified Compostable." required>
        </div>

        <!-- Eyebrow / Subtitle & Button Label -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div>
            <label class="form-label" for="f-subtitle" style="font-weight:600">Eyebrow / Sub-headline</label>
            <input type="text" id="f-subtitle" name="subtitle" class="form-control" placeholder="e.g. Sustainable Packaging Engineered for Modern Enterprise">
          </div>
          <div>
            <label class="form-label" for="f-btn-text" style="font-weight:600">Button Label</label>
            <input type="text" id="f-btn-text" name="button_text" class="form-control" placeholder="e.g. Explore Products">
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="form-label" for="f-desc" style="font-weight:600">Description / Supporting Paragraph</label>
          <textarea id="f-desc" name="description" class="form-control" rows="2" placeholder="Brief supporting narrative shown beneath the headline"></textarea>
        </div>

        <!-- Button URL & Status row -->
        <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:14px;align-items:end">
          <div>
            <label class="form-label" for="f-btn-url" style="font-weight:600">Button Target URL</label>
            <input type="text" id="f-btn-url" name="button_url" class="form-control" placeholder="/products, /about, /contact or full URL">
          </div>
          <div>
            <label class="form-label" style="font-weight:600">Status</label>
            <div style="display:flex;gap:14px;align-items:center;height:42px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.88rem">
                <input type="radio" name="is_active" value="1" id="f-active-yes" checked> Active
              </label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.88rem">
                <input type="radio" name="is_active" value="0" id="f-active-no"> Hidden
              </label>
            </div>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:16px 24px;border-top:1px solid #e5e7eb;background:#f9fafb">
        <button type="button" id="modal-delete-btn" style="display:none;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:7px 14px;border-radius:8px;font-weight:600;font-size:.82rem;align-items:center;gap:6px;cursor:pointer" onclick="deleteCurrentSlide()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
          Delete This Slide
        </button>
        <div style="display:flex;align-items:center;gap:10px;margin-left:auto">
          <div id="modal-spinner" style="display:none;color:#6b7280;font-size:.83rem">
            <span style="display:inline-block;animation:spin 1s linear infinite">⏳</span> Saving slide…
          </div>
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn btn-primary" id="modal-submit-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span id="modal-btn-label">Save Slide</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Image Lightbox Modal -->
<div id="lightbox-modal" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.85);align-items:center;justify-content:center;padding:20px;cursor:zoom-out" onclick="closeLightbox()">
  <img id="lightbox-img" src="" alt="Full preview" style="max-width:90vw;max-height:85vh;border-radius:12px;box-shadow:0 25px 60px rgba(0,0,0,.5);object-fit:contain">
</div>

<!-- Styles for Web Slider Page -->
<style>
.banner-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
  gap: 20px;
  width: 100%;
  box-sizing: border-box;
}
.page-banner-card {
  min-width: 0;
  width: 100%;
  box-sizing: border-box;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 6px rgba(0,0,0,.03);
  transition: box-shadow .2s ease, border-color .2s ease;
}
.page-banner-card:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,.08) !important;
  border-color: var(--green-400) !important;
}
.page-filter-pill {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: .82rem;
  font-weight: 600;
  border: 1px solid var(--border);
  background: #fff;
  color: var(--ink);
  cursor: pointer;
  transition: background .15s, border-color .15s, color .15s;
  white-space: nowrap;
}
.page-filter-pill:hover {
  background: var(--green-50);
  border-color: var(--green-300);
  color: var(--green-700);
}
.page-filter-pill.active {
  background: var(--green-700);
  border-color: var(--green-700);
  color: #fff;
  box-shadow: 0 2px 5px rgba(26,61,32,.2);
}
.slide-row { transition: background .15s; }
.slide-row:hover { background: #fbfdfb; }
.slide-row.dragging { opacity: .35; background: #ecfdf5; }
.drag-handle { user-select: none; }
.status-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; cursor:pointer; transition:all .15s; }
.alert-slide { padding:12px 18px; border-radius:10px; margin-bottom:18px; font-size:.86rem; display:flex; align-items:center; gap:10px; box-shadow:var(--shadow-sm); }
.alert-slide.success { background:#f0fdf4; border:1px solid #86efac; color:#166534; }
.alert-slide.error   { background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Prevent any horizontal blowout in slider page */
#slides-container {
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
  box-sizing: border-box;
}
.table-wrap {
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
  box-sizing: border-box;
  -webkit-overflow-scrolling: touch;
}
@media (max-width: 640px) {
  .banner-cards-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<!-- ══════════════════════════════════════════════════════════
     JAVASCRIPT LOGIC
═══════════════════════════════════════════════════════════ -->
<script>
const SLIDER_API = '<?= API_URL ?>/slider.php';
const PAGES_CONFIG = <?= json_encode($PAGES) ?>;

// All slides loaded from server
let slides = <?= json_encode(array_values($slides)) ?>;
let activePageFilter = 'all';

// Flash message
function flash(msg, type = 'success') {
  const z = document.getElementById('flash-zone');
  const el = document.createElement('div');
  el.className = `alert-slide ${type}`;
  el.innerHTML = (type === 'success'
    ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>')
    + ' <span>' + msg + '</span>';
  z.prepend(el);
  setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .4s'; }, 3500);
  setTimeout(() => el.remove(), 3900);
}

// ── Filter table by page ─────────────────────────────────────
function filterByPage(pageKey) {
  activePageFilter = pageKey;
  document.querySelectorAll('.page-filter-pill').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.page === pageKey);
  });
  refreshTable();
}

// ── Modal open/close ─────────────────────────────────────────
function syncPageName(key) {
  const cfg = PAGES_CONFIG[key];
  document.getElementById('f-page-name').value = cfg ? cfg.name : key;
}

function openModal(slide = null) {
  document.getElementById('slide-form').reset();
  document.getElementById('img-preview-wrap').style.display = 'none';
  document.getElementById('img-preview').src = '';
  document.getElementById('slide-id').value = '';
  document.getElementById('f-image-url-raw').value = '';
  document.getElementById('modal-title').textContent = 'Add New Hero Slide';
  document.getElementById('modal-sub').textContent = 'Fill in slide details and select or upload an image';
  document.getElementById('modal-btn-label').textContent = 'Save Slide';

  const delBtn = document.getElementById('modal-delete-btn');
  if (delBtn) delBtn.style.display = 'none';

  if (activePageFilter !== 'all') {
    document.getElementById('f-page-key').value = activePageFilter;
    syncPageName(activePageFilter);
  }

  if (slide) {
    document.getElementById('modal-title').textContent = 'Edit Hero Slide';
    document.getElementById('modal-sub').textContent = `Editing slide for ${slide.page_name || slide.page_key}`;
    document.getElementById('modal-btn-label').textContent = 'Update Slide';
    document.getElementById('slide-id').value = slide.id;
    document.getElementById('f-page-key').value = slide.page_key || 'home';
    document.getElementById('f-page-name').value = slide.page_name || 'Home Page';
    document.getElementById('f-title').value = slide.title || '';
    document.getElementById('f-subtitle').value = slide.subtitle || '';
    document.getElementById('f-desc').value = slide.description || '';
    document.getElementById('f-btn-text').value = slide.button_text || '';
    document.getElementById('f-btn-url').value = slide.button_url || '';
    document.getElementById('f-image-url-raw').value = slide.image_url || '';

    if (delBtn) delBtn.style.display = 'inline-flex';

    const isAct = slide.is_active !== undefined ? String(slide.is_active) : '1';
    const rad = document.querySelector(`input[name="is_active"][value="${isAct}"]`);
    if (rad) rad.checked = true;

    const imgDisplay = slide.display_image_url || slide.image_url;
    if (imgDisplay) {
      document.getElementById('img-preview').src = imgDisplay;
      document.getElementById('img-preview-wrap').style.display = 'block';
      document.getElementById('img-current-label').textContent = 'Current Image';
    }
  }

  document.getElementById('slide-modal').style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function openModalForPage(pkey, pname) {
  openModal();
  document.getElementById('f-page-key').value = pkey;
  document.getElementById('f-page-name').value = pname;
}

function closeModal() {
  document.getElementById('slide-modal').style.display = 'none';
  document.body.style.overflow = '';
  const delBtn = document.getElementById('modal-delete-btn');
  if (delBtn) delBtn.style.display = 'none';
}

document.getElementById('slide-modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

function editSlide(id) {
  const s = slides.find(x => x.id == id);
  if (s) openModal(s);
}

// ── Lightbox ─────────────────────────────────────────────────
function openLightbox(src) {
  document.getElementById('lightbox-img').src = src;
  document.getElementById('lightbox-modal').style.display = 'flex';
}
function closeLightbox() {
  document.getElementById('lightbox-modal').style.display = 'none';
}

// ── File upload & preview ────────────────────────────────────
function previewFile(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('img-preview').src = e.target.result;
    document.getElementById('img-preview-wrap').style.display = 'block';
    document.getElementById('img-current-label').textContent = 'New Image Selected';
  };
  reader.readAsDataURL(file);
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('drop-zone').style.borderColor = '#d1d5db';
  document.getElementById('drop-zone').style.background = '#fafafa';
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('img-file').files = dt.files;
    previewFile(document.getElementById('img-file'));
  }
}

// ── Submit Slide ─────────────────────────────────────────────
async function submitSlide(e) {
  e.preventDefault();
  const btn  = document.getElementById('modal-submit-btn');
  const spin = document.getElementById('modal-spinner');
  btn.disabled = true;
  spin.style.display = 'inline-block';

  const id = document.getElementById('slide-id').value;
  const fd = new FormData(document.getElementById('slide-form'));

  const url = id ? `${SLIDER_API}?action=update&id=${id}` : SLIDER_API;

  try {
    const res = await fetch(url, { method: 'POST', body: fd, credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);

    const slide = json.data;
    slide.display_image_url = slide.image_url;

    if (id) {
      const idx = slides.findIndex(s => s.id == id);
      if (idx >= 0) slides[idx] = slide;
      flash('Hero slide updated successfully!');
    } else {
      slides.push(slide);
      flash('New hero slide created successfully!');
    }
    closeModal();
    refreshTable();
    // Quick reload page after 800ms so showcase cards update too
    setTimeout(() => window.location.reload(), 900);
  } catch(err) {
    flash('Error: ' + err.message, 'error');
  } finally {
    btn.disabled = false;
    spin.style.display = 'none';
  }
}

// ── Delete Slide ─────────────────────────────────────────────
async function deleteSlide(id, btn) {
  if (!confirm('Are you sure you want to delete this slide permanently?')) return;
  if (btn) btn.disabled = true;
  try {
    const res = await fetch(`${SLIDER_API}?action=delete&id=${id}`, { method: 'POST', credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    slides = slides.filter(s => s.id != id);
    flash('Slide deleted successfully.');
    refreshTable();
    setTimeout(() => window.location.reload(), 800);
  } catch(err) {
    flash('Error: ' + err.message, 'error');
    if (btn) btn.disabled = false;
  }
}

// ── Delete Slide from Modal ──────────────────────────────────
async function deleteCurrentSlide() {
  const id = document.getElementById('slide-id').value;
  if (!id) return;
  if (!confirm('Are you sure you want to permanently delete this slide?')) return;
  const btn = document.getElementById('modal-delete-btn');
  if (btn) btn.disabled = true;
  try {
    const res = await fetch(`${SLIDER_API}?action=delete&id=${id}`, { method: 'POST', credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    slides = slides.filter(s => s.id != id);
    closeModal();
    flash('Slide deleted successfully.');
    refreshTable();
    setTimeout(() => window.location.reload(), 700);
  } catch(err) {
    flash('Error: ' + err.message, 'error');
    if (btn) btn.disabled = false;
  }
}

// ── Toggle Active Status ─────────────────────────────────────
document.getElementById('slides-container').addEventListener('change', async function(e) {
  if (!e.target.classList.contains('slide-toggle')) return;
  const id       = e.target.dataset.id;
  const isActive = e.target.checked ? 1 : 0;
  const slide    = slides.find(s => s.id == id);
  if (!slide) return;

  const pill = e.target.nextElementSibling;
  pill.textContent = isActive ? 'Active' : 'Hidden';
  pill.className = 'status-pill ' + (isActive ? 'badge-replied' : 'badge-archived');

  const fd = new FormData();
  fd.append('page_key',    slide.page_key || 'home');
  fd.append('page_name',   slide.page_name || 'Home Page');
  fd.append('title',       slide.title || '');
  fd.append('subtitle',    slide.subtitle || '');
  fd.append('description', slide.description || '');
  fd.append('button_text', slide.button_text || '');
  fd.append('button_url',  slide.button_url || '');
  fd.append('image_url',   slide.image_url || '');
  fd.append('sort_order',  slide.sort_order || 0);
  fd.append('is_active',   isActive);

  try {
    const res = await fetch(`${SLIDER_API}?action=update&id=${id}`, { method: 'POST', body: fd, credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    slide.is_active = isActive;
  } catch(err) {
    e.target.checked = !e.target.checked;
    pill.textContent = e.target.checked ? 'Active' : 'Hidden';
    pill.className = 'status-pill ' + (e.target.checked ? 'badge-replied' : 'badge-archived');
    flash('Error updating status: ' + err.message, 'error');
  }
});

// ── Refresh Table ────────────────────────────────────────────
function refreshTable() {
  const container = document.getElementById('slides-container');
  const filtered = activePageFilter === 'all'
    ? slides
    : slides.filter(s => (s.page_key || 'home') === activePageFilter);

  document.getElementById('slide-count').textContent = filtered.length;

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="padding:48px 20px;text-align:center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:48px;height:48px;margin:0 auto 12px;color:var(--muted)"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
        <h3 style="font-size:1.05rem;color:var(--ink);margin-bottom:4px">No slides for this page</h3>
        <p style="font-size:.85rem;color:var(--muted)">Click "Add New Slide" to add a hero banner for this page.</p>
      </div>`;
    return;
  }

  const rows = filtered.map(s => {
    const pcfg = PAGES_CONFIG[s.page_key] || { name: s.page_name || s.page_key, badge_bg: '#f3f4f6', badge_color: '#374151' };
    const imgSrc = s.display_image_url || s.image_url;

    return `
    <tr class="slide-row" data-id="${s.id}" style="cursor:grab">
      <td style="color:#aaa;text-align:center;font-size:18px;cursor:grab" class="drag-handle" title="Drag to reorder">⠿</td>
      <td>
        ${imgSrc
          ? `<img src="${escHtml(imgSrc)}" alt="${escHtml(s.title)}"
                  onclick="openLightbox('${escHtml(imgSrc)}')"
                  title="Click to zoom"
                  style="width:78px;height:48px;object-fit:cover;border-radius:7px;border:1px solid #e5e7eb;cursor:zoom-in"
                  onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
             <div style="display:none;width:78px;height:48px;background:#f3f4f6;border-radius:7px;align-items:center;justify-content:center;color:#9ca3af;font-size:11px">No Image</div>`
          : `<div style="width:78px;height:48px;background:#f3f4f6;border-radius:7px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:11px">No Image</div>`
        }
      </td>
      <td>
        <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:.73rem;font-weight:700;background:${pcfg.badge_bg};color:${pcfg.badge_color}">
          ${escHtml(pcfg.name)}
        </span>
      </td>
      <td>
        <div style="font-weight:700;font-size:.88rem;color:var(--ink)">${escHtml(s.title || '—')}</div>
        ${s.subtitle ? `<div style="font-size:.78rem;color:var(--green-700);margin-top:2px;font-weight:500">${escHtml(s.subtitle)}</div>` : ''}
        ${s.description ? `<div style="font-size:.76rem;color:var(--muted);margin-top:2px;max-width:380px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(s.description)}</div>` : ''}
      </td>
      <td>
        ${s.button_text
          ? `<div style="display:inline-flex;flex-direction:column;gap:2px">
               <span style="display:inline-block;padding:2px 9px;background:#f0fdf4;border:1px solid #86efac;border-radius:14px;font-size:.74rem;color:#166534;font-weight:600">
                 ${escHtml(s.button_text)}
               </span>
               ${s.button_url ? `<span style="font-size:.68rem;color:var(--muted);max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escHtml(s.button_url)}</span>` : ''}
             </div>`
          : `<span style="color:#d1d5db;font-size:.82rem">—</span>`
        }
      </td>
      <td>
        <label class="toggle-label" title="Toggle active status" style="cursor:pointer">
          <input type="checkbox" class="slide-toggle" data-id="${s.id}" ${s.is_active ? 'checked' : ''} style="display:none">
          <span class="status-pill ${s.is_active ? 'badge-replied' : 'badge-archived'}" style="cursor:pointer">
            ${s.is_active ? 'Active' : 'Hidden'}
          </span>
        </label>
      </td>
      <td style="text-align:center;font-size:.82rem;color:var(--muted)">${s.sort_order}</td>
      <td style="text-align:right">
        <div style="display:inline-flex;gap:6px">
          <button class="btn btn-ghost btn-sm btn-icon" title="Edit Slide" onclick="editSlide(${s.id})">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
          </button>
          <button class="btn btn-ghost btn-sm btn-icon" title="Delete Slide" style="color:#ef4444" onclick="deleteSlide(${s.id}, this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');

  container.innerHTML = `
    <div class="table-wrap">
      <table id="slides-table">
        <thead>
          <tr>
            <th style="width:36px"></th>
            <th style="width:90px">Hero Image</th>
            <th style="width:150px">Page</th>
            <th>Headline / Subtitle</th>
            <th style="width:130px">CTA Button</th>
            <th style="width:85px">Status</th>
            <th style="width:75px;text-align:center">Order</th>
            <th style="width:110px;text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody id="slides-tbody">${rows}</tbody>
      </table>
    </div>`;

  initDrag();
}

function escHtml(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Drag-to-reorder ──────────────────────────────────────────
function initDrag() {
  const tbody = document.getElementById('slides-tbody');
  if (!tbody) return;
  let dragSrc = null;

  function getRows() { return [...tbody.querySelectorAll('.slide-row')]; }

  tbody.addEventListener('dragstart', e => {
    const row = e.target.closest('.slide-row');
    if (!row) return;
    dragSrc = row;
    setTimeout(() => row.classList.add('dragging'), 0);
    e.dataTransfer.effectAllowed = 'move';
  });

  tbody.addEventListener('dragover', e => {
    e.preventDefault();
    const row = e.target.closest('.slide-row');
    if (!row || row === dragSrc) return;
    const rect = row.getBoundingClientRect();
    const after = e.clientY > rect.top + rect.height / 2;
    tbody.insertBefore(dragSrc, after ? row.nextSibling : row);
  });

  tbody.addEventListener('dragend', async () => {
    if (dragSrc) dragSrc.classList.remove('dragging');
    dragSrc = null;

    const order = getRows().map(r => parseInt(r.dataset.id));
    order.forEach((id, i) => {
      const s = slides.find(x => x.id == id);
      if (s) s.sort_order = i + 1;
      const row = tbody.querySelector(`[data-id="${id}"]`);
      if (row) {
        const cells = row.querySelectorAll('td');
        if (cells[6]) cells[6].textContent = i + 1;
      }
    });

    try {
      const res = await fetch(`${SLIDER_API}?action=reorder`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ order })
      });
      const json = await res.json();
      if (!json.success) throw new Error(json.message);
      flash('Slide order updated successfully.');
    } catch(err) {
      flash('Error saving order: ' + err.message, 'error');
    }
  });

  getRows().forEach(row => { row.setAttribute('draggable', 'true'); });
}

// Initialize on page load
refreshTable();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

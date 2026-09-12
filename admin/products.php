<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo        = db();
$flash      = '';
$flash_type = 'success';

// ── Helper: generate unique slug ─────────────────────────────
function product_slug(string $name, PDO $pdo, int $exclude_id = 0): string {
    $base = slugify($name);
    $slug = $base;
    $i    = 1;
    while (true) {
        $s = $pdo->prepare('SELECT id FROM products WHERE slug=? AND id != ?');
        $s->execute([$slug, $exclude_id]);
        if (!$s->fetch()) break;
        $slug = $base . '-' . $i++;
    }
    return $slug;
}

// ── Handle image upload via AJAX ─────────────────────────────
// (products.php also acts as upload target from JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_image') {
    header('Content-Type: application/json');
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Upload failed.']); exit;
    }
    $file     = $_FILES['image'];
    $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mime     = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed, true) || $file['size'] > 5*1024*1024) {
        echo json_encode(['success' => false, 'message' => 'Invalid file or too large (max 5 MB).']); exit;
    }
    $ext_map  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    $ext      = $ext_map[$mime];
    $dir      = dirname(__DIR__) . '/uploads/products/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $fname    = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $dir . $fname);
    $url      = BASE_URL . '/uploads/products/' . $fname;
    echo json_encode(['success' => true, 'url' => $url, 'path' => '/uploads/products/' . $fname]);
    exit;
}

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Delete old image file
            $row = $pdo->prepare('SELECT image_path FROM products WHERE id=?');
            $row->execute([$id]);
            $p = $row->fetch();
            if ($p && $p['image_path']) {
                $abs = dirname(__DIR__) . $p['image_path'];
                if (file_exists($abs)) @unlink($abs);
            }
            $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
            log_activity('product_delete', "Product #$id deleted.");
            $flash = 'Product deleted.';
            header('Location: products.php'); exit;
        }

    } elseif ($action === 'toggle_active') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare('UPDATE products SET is_active = NOT is_active WHERE id=?')->execute([$id]);
            $flash = 'Product status toggled.';
        }

    } elseif ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);

        if ($name === '') {
            $flash = 'Product name is required.';
            $flash_type = 'error';
        } else {
            // Get category name string from id
            $cat_name = '';
            if ($category_id > 0) {
                $cs = $pdo->prepare('SELECT name FROM categories WHERE id=?');
                $cs->execute([$category_id]);
                $cr = $cs->fetch();
                $cat_name = $cr ? $cr['name'] : '';
            }

            // Handle new image upload
            $image_path = $_POST['existing_image_path'] ?? '';
            $image_url  = '';
            if (!empty($_FILES['product_image']['name'])) {
                $file    = $_FILES['product_image'];
                $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
                $finfo   = new finfo(FILEINFO_MIME_TYPE);
                $mime    = $finfo->file($file['tmp_name']);
                if ($file['error'] === UPLOAD_ERR_OK && in_array($mime, $allowed, true) && $file['size'] <= 5*1024*1024) {
                    $ext_map = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                    $ext     = $ext_map[$mime];
                    $dir     = dirname(__DIR__) . '/uploads/products/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $fname   = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $dir . $fname)) {
                        // Remove old image
                        if ($image_path) {
                            $abs = dirname(__DIR__) . $image_path;
                            if (file_exists($abs)) @unlink($abs);
                        }
                        $image_path = '/uploads/products/' . $fname;
                        $image_url  = BASE_URL . $image_path;
                    } else {
                        $flash = 'Image upload failed. Check folder permissions.';
                        $flash_type = 'error';
                    }
                } else {
                    $flash = 'Invalid image or file too large (max 5 MB). Only JPEG, PNG, WebP, GIF allowed.';
                    $flash_type = 'error';
                }
            }

            if ($flash_type !== 'error') {
                $slug = product_slug($name, $pdo, $id);

                if ($id > 0) {
                    $pdo->prepare(
                        'UPDATE products SET
                           name=?, slug=?, category_id=?, category=?, description=?,
                           features=?, applications=?, image_path=?, certifications=?,
                           is_featured=?, is_active=?, sort_order=?
                         WHERE id=?'
                    )->execute([
                        sanitize($name), $slug,
                        $category_id ?: null, $cat_name,
                        sanitize($_POST['description'] ?? ''),
                        sanitize($_POST['features']    ?? ''),
                        sanitize($_POST['applications']?? ''),
                        $image_path,
                        sanitize($_POST['certifications'] ?? ''),
                        isset($_POST['is_featured']) ? 1 : 0,
                        isset($_POST['is_active'])   ? 1 : 0,
                        (int)($_POST['sort_order'] ?? 0),
                        $id,
                    ]);
                    log_activity('product_update', "Product #$id '$name' updated.");
                    $flash = 'Product updated successfully.';
                } else {
                    $pdo->prepare(
                        'INSERT INTO products
                           (name, slug, category_id, category, description, features,
                            applications, image_path, certifications, is_featured, is_active, sort_order)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
                    )->execute([
                        sanitize($name), $slug,
                        $category_id ?: null, $cat_name,
                        sanitize($_POST['description'] ?? ''),
                        sanitize($_POST['features']    ?? ''),
                        sanitize($_POST['applications']?? ''),
                        $image_path,
                        sanitize($_POST['certifications'] ?? ''),
                        isset($_POST['is_featured']) ? 1 : 0,
                        isset($_POST['is_active'])   ? 1 : 0,
                        (int)($_POST['sort_order'] ?? 0),
                    ]);
                    log_activity('product_create', "Product '$name' created.");
                    $flash = 'Product created successfully.';
                }
                // Redirect to avoid re-POST on refresh
                header('Location: products.php?flash=' . urlencode($flash));
                exit;
            }
        }
    }
}

// Flash from redirect
if (isset($_GET['flash']) && !$flash) {
    $flash = htmlspecialchars($_GET['flash']);
}

// ── Fetch data ────────────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$cat_f  = (int)($_GET['cat'] ?? 0);

$where  = []; $params = [];
if ($search) { $where[] = '(p.name LIKE ? OR p.description LIKE ?)'; $like="%$search%"; $params[] = $like; $params[] = $like; }
if ($cat_f)  { $where[] = 'p.category_id=?'; $params[] = $cat_f; }
$sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$products = $pdo->prepare(
    "SELECT p.*, c.name AS cat_label
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     $sql_where ORDER BY p.sort_order ASC, p.id ASC"
);
$products->execute($params);
$products = $products->fetchAll();

$categories = $pdo->query(
    'SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order ASC, name ASC'
)->fetchAll();

// Edit row
$edit_id  = (int)($_GET['edit'] ?? 0);
$edit_row = null;
if ($edit_id > 0) {
    $s = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $s->execute([$edit_id]);
    $edit_row = $s->fetch();
}
$show_form = isset($_GET['new']) || $edit_row !== null;

$page_title  = 'Products';
$breadcrumbs = [['Products', '']];
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Products</h1>
    <p class="page-subtitle"><?= count($products) ?> product<?= count($products)!==1?'s':'' ?> &mdash; <a href="categories.php" style="color:var(--green-600);font-size:.82rem">Manage Categories</a></p>
  </div>
  <div style="display:flex;gap:10px">
    <a href="categories.php" class="btn btn-secondary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
      Categories
    </a>
    <a href="products.php?new=1" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      Add Product
    </a>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>" id="flash-msg">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <?= $flash ?>
</div>
<?php endif; ?>

<?php if ($show_form): ?>
<!-- ── Add / Edit Form ──────────────────────────────────────── -->
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <div class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
      <?= $edit_row ? 'Edit Product' : 'Add New Product' ?>
    </div>
    <a href="products.php" class="btn btn-ghost btn-sm">Cancel</a>
  </div>
  <div class="card-body">
    <form method="POST" action="products.php" enctype="multipart/form-data" id="productForm">
      <input type="hidden" name="action" value="save">
      <?php if ($edit_row): ?><input type="hidden" name="id" value="<?= $edit_row['id'] ?>"><?php endif; ?>
      <input type="hidden" name="existing_image_path" value="<?= htmlspecialchars($edit_row['image_path'] ?? '') ?>" id="existing_image_path">

      <div class="grid-2">
        <!-- Name -->
        <div class="form-group">
          <label class="form-label">Product Name <span class="required">*</span></label>
          <input type="text" name="name" class="form-control" required
                 value="<?= htmlspecialchars($edit_row['name'] ?? '') ?>"
                 placeholder="e.g. Compostable Carry Bags">
        </div>
        <!-- Category -->
        <div class="form-group">
          <label class="form-label">Category
            <a href="categories.php" style="font-size:.7rem;color:var(--green-600);margin-left:6px">+ Manage</a>
          </label>
          <select name="category_id" class="form-control">
            <option value="">— Select Category —</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"
                <?= ($edit_row['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3"
                  placeholder="Brief product description shown on the product card…"><?= htmlspecialchars($edit_row['description'] ?? '') ?></textarea>
      </div>

      <div class="grid-2">
        <!-- Features -->
        <div class="form-group">
          <label class="form-label">Key Features</label>
          <textarea name="features" class="form-control" rows="4"
                    placeholder="Certified compostable&#10;Plant-based materials&#10;Zero microplastics"><?= htmlspecialchars($edit_row['features'] ?? '') ?></textarea>
          <div class="form-hint">One feature per line — displayed as bullet points</div>
        </div>
        <!-- Applications -->
        <div class="form-group">
          <label class="form-label">Applications / Use Cases</label>
          <textarea name="applications" class="form-control" rows="4"
                    placeholder="Retail packaging&#10;Grocery bags&#10;Food service"><?= htmlspecialchars($edit_row['applications'] ?? '') ?></textarea>
          <div class="form-hint">One application per line</div>
        </div>
      </div>

      <!-- Image Upload -->
      <div class="form-group">
        <label class="form-label">Product Image</label>
        <div class="upload-zone" id="uploadZone">
          <!-- Preview -->
          <div id="imgPreviewWrap" style="<?= empty($edit_row['image_path']) ? 'display:none' : '' ?>margin-bottom:14px">
            <img id="imgPreview"
                 src="<?= $edit_row['image_path'] ? BASE_URL . htmlspecialchars($edit_row['image_path']) : '' ?>"
                 alt="Preview"
                 style="max-height:180px;max-width:100%;border-radius:10px;object-fit:cover;border:2px solid var(--border)">
            <button type="button" id="removeImg" class="btn btn-danger btn-sm" style="margin-top:8px;display:block"
                    <?= empty($edit_row['image_path']) ? 'style="display:none"' : '' ?>>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              Remove Image
            </button>
          </div>

          <!-- Drop area -->
          <label class="upload-drop" id="uploadDrop" for="product_image"
                 style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
                        border:2px dashed var(--border-dark);border-radius:12px;padding:32px 20px;
                        cursor:pointer;background:var(--sand);transition:all .2s;text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 style="width:38px;height:38px;color:var(--green-500)">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
            <div>
              <span style="font-size:.88rem;font-weight:600;color:var(--ink)">Click to upload</span>
              <span style="font-size:.82rem;color:var(--muted)"> or drag &amp; drop</span>
            </div>
            <span style="font-size:.74rem;color:var(--muted)">JPEG, PNG, WebP, GIF · Max 5 MB</span>
            <input type="file" name="product_image" id="product_image"
                   accept="image/jpeg,image/png,image/webp,image/gif"
                   style="display:none">
          </label>

          <!-- Upload progress bar -->
          <div id="uploadProgress" style="display:none;margin-top:10px">
            <div style="height:4px;background:var(--border);border-radius:4px;overflow:hidden">
              <div id="uploadBar" style="height:100%;background:var(--green-500);width:0;transition:width .3s"></div>
            </div>
            <div id="uploadStatus" style="font-size:.76rem;color:var(--muted);margin-top:6px"></div>
          </div>
        </div>
        <div class="form-hint">Recommended: 800×600px or 16:10 ratio · Replaces existing image when changed</div>
      </div>

      <!-- Certifications + Sort -->
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Certifications</label>
          <input type="text" name="certifications" class="form-control"
                 value="<?= htmlspecialchars($edit_row['certifications'] ?? '') ?>"
                 placeholder="EN 13432, ASTM D6400, CPCB">
          <div class="form-hint">Comma-separated</div>
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" min="0"
                 value="<?= (int)($edit_row['sort_order'] ?? 0) ?>">
          <div class="form-hint">Lower = shown first</div>
        </div>
      </div>

      <!-- Toggles -->
      <div style="display:flex;gap:28px;flex-wrap:wrap;margin-bottom:20px">
        <label class="form-check">
          <input type="checkbox" name="is_featured" <?= ($edit_row['is_featured'] ?? 0) ? 'checked' : '' ?>>
          Mark as Featured
        </label>
        <label class="form-check">
          <input type="checkbox" name="is_active" <?= ($edit_row === null || ($edit_row['is_active'] ?? 1)) ? 'checked' : '' ?>>
          Active (visible on site)
        </label>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" id="saveBtn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <?= $edit_row ? 'Update Product' : 'Create Product' ?>
        </button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- ── Search + Filter toolbar ─────────────────────────────── -->
<div class="toolbar">
  <form method="GET" action="products.php" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="search-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
      <input type="search" name="search" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <select name="cat" class="filter-select">
      <option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= $cat_f===$cat['id']?'selected':'' ?>>
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <?php if ($search||$cat_f): ?><a href="products.php" class="btn btn-ghost btn-sm">Clear</a><?php endif; ?>
  </form>
</div>

<!-- ── Products table ──────────────────────────────────────── -->
<div class="card">
  <div class="table-wrap">
    <?php if (empty($products)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4"/></svg>
        <h3>No products found</h3>
        <p><a href="products.php?new=1" style="color:var(--green-600)">Add your first product</a></p>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th style="width:70px">Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Certifications</th>
          <th>Featured</th>
          <th>Status</th>
          <th>Order</th>
          <th style="width:100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td>
            <?php if ($p['image_path']): ?>
              <img src="<?= BASE_URL . htmlspecialchars($p['image_path']) ?>"
                   alt="<?= htmlspecialchars($p['name']) ?>"
                   style="width:56px;height:42px;object-fit:cover;border-radius:7px;border:1px solid var(--border)">
            <?php else: ?>
              <div style="width:56px;height:42px;background:var(--sand);border-radius:7px;display:grid;place-items:center;border:1px dashed var(--border-dark)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;color:var(--muted)"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5M21 6.75A2.25 2.25 0 0018.75 4.5H5.25A2.25 2.25 0 003 6.75v10.5A2.25 2.25 0 005.25 19.5H18.75A2.25 2.25 0 0021 17.25V6.75z"/></svg>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <div class="td-name"><?= htmlspecialchars($p['name']) ?></div>
            <div class="text-muted text-sm"><?= htmlspecialchars($p['slug']) ?></div>
          </td>
          <td>
            <?php if ($p['cat_label']): ?>
              <span class="badge badge-read" style="font-size:.68rem"><?= htmlspecialchars($p['cat_label']) ?></span>
            <?php else: ?>
              <span class="text-muted text-sm">—</span>
            <?php endif; ?>
          </td>
          <td class="text-sm text-muted"><?= htmlspecialchars($p['certifications'] ?: '—') ?></td>
          <td><?= $p['is_featured'] ? '<span class="badge badge-featured">★ Featured</span>' : '<span class="text-muted text-sm">—</span>' ?></td>
          <td>
            <form method="POST" action="products.php" style="display:inline">
              <input type="hidden" name="action" value="toggle_active">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="badge <?= $p['is_active'] ? 'badge-active' : 'badge-inactive' ?>"
                      style="border:none;cursor:pointer;background:transparent;padding:3px 10px">
                <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
              </button>
            </form>
          </td>
          <td class="text-muted text-sm" style="text-align:center"><?= $p['sort_order'] ?></td>
          <td>
            <div style="display:flex;gap:5px">
              <a href="products.php?edit=<?= $p['id'] ?>" class="btn btn-ghost btn-icon btn-sm" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
              </a>
              <form method="POST" action="products.php" onsubmit="return confirm('Delete this product?')" style="display:inline">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-icon btn-sm" style="color:#dc2626" title="Delete">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<script>
(function(){
  const fileInput  = document.getElementById('product_image');
  const dropZone   = document.getElementById('uploadDrop');
  const preview    = document.getElementById('imgPreview');
  const previewWrap= document.getElementById('imgPreviewWrap');
  const removeBtn  = document.getElementById('removeImg');
  const existingInput = document.getElementById('existing_image_path');
  const progressWrap  = document.getElementById('uploadProgress');
  const progressBar   = document.getElementById('uploadBar');
  const progressStatus= document.getElementById('uploadStatus');

  if (!fileInput) return;

  // Drag & drop styling
  dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.borderColor='var(--green-500)'; dropZone.style.background='var(--green-50)'; });
  dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor=''; dropZone.style.background=''; });
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.borderColor=''; dropZone.style.background='';
    const files = e.dataTransfer.files;
    if (files.length) { const dt = new DataTransfer(); dt.items.add(files[0]); fileInput.files = dt.files; handleFileSelect(files[0]); }
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) handleFileSelect(fileInput.files[0]);
  });

  function handleFileSelect(file) {
    if (!file.type.startsWith('image/')) { alert('Please select an image file.'); return; }
    if (file.size > 5 * 1024 * 1024) { alert('File must be under 5 MB.'); return; }

    // Show instant local preview
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      previewWrap.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }

  // Remove image
  if (removeBtn) {
    removeBtn.addEventListener('click', () => {
      preview.src = '';
      previewWrap.style.display = 'none';
      existingInput.value = '';
      fileInput.value = '';
    });
  }

  // Auto-hide flash message
  const flash = document.getElementById('flash-msg');
  if (flash) setTimeout(() => { flash.style.opacity='0'; flash.style.transition='opacity .5s'; setTimeout(()=>flash.remove(),500); }, 3500);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

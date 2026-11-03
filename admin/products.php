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

// ── Helper: resolve full product image URL ─────────────────────
function get_product_image_url($p): string {
    if (empty($p)) return '';
    $img = !empty($p['image_path']) ? $p['image_path'] : (!empty($p['image_url']) ? $p['image_url'] : '');
    if (!$img) return '';
    if (preg_match('#^https?://#i', $img)) return $img;

    $clean = ltrim($img, '/');
    if (!str_starts_with($clean, 'uploads/') && !str_starts_with($clean, 'src/')) {
        $clean = 'uploads/products/' . $clean;
    }
    return rtrim(BASE_URL, '/') . '/' . $clean;
}

// ── Helper: safely delete an uploaded product image file from disk ──
function delete_product_image_file(?string $path): void {
    if (!$path) return;
    $clean = ltrim($path, '/');
    if (str_starts_with($clean, 'uploads/products/')) {
        $root = dirname(__DIR__);
        $file1 = $root . '/' . $clean;
        if (is_file($file1)) @unlink($file1);
        $file2 = $root . '/public/' . $clean;
        if (is_file($file2)) @unlink($file2);
    }
}

// ── Helper: detect uploaded image MIME safely with multi-layer fallbacks ──
function get_uploaded_image_mime(array $file): string {
    if (empty($file['tmp_name']) || !is_file($file['tmp_name'])) return '';
    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string)@finfo_file($finfo, $file['tmp_name']);
            @finfo_close($finfo);
        }
    } elseif (class_exists('finfo')) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = (string)$finfo->file($file['tmp_name']);
        } catch (\Throwable $e) {}
    }
    if (!$mime && function_exists('mime_content_type')) {
        $mime = (string)@mime_content_type($file['tmp_name']);
    }
    if (!$mime && function_exists('getimagesize')) {
        $info = @getimagesize($file['tmp_name']);
        if ($info && !empty($info['mime'])) {
            $mime = (string)$info['mime'];
        }
    }
    return strtolower(trim($mime));
}

// ── Handle image upload via AJAX ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_image') {
    header('Content-Type: application/json');
    if (empty($_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'No image file uploaded.']); exit;
    }
    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE   => 'Image exceeds server upload_max_filesize limit.',
            UPLOAD_ERR_FORM_SIZE  => 'Image exceeds maximum form size limit.',
            UPLOAD_ERR_PARTIAL    => 'Image was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No image was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing server temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write image to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        echo json_encode(['success' => false, 'message' => $upload_errors[$file['error']] ?? ('Upload failed with error ' . $file['error'])]);
        exit;
    }
    if ($file['size'] > 10 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File too large (max 10 MB).']); exit;
    }
    $mime = get_uploaded_image_mime($file);
    $ext_from_name = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_mimes = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp', 'image/gif'];
    $allowed_exts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($mime, $allowed_mimes, true) && !in_array($ext_from_name, $allowed_exts, true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image format. Allowed: JPEG, PNG, WebP, GIF.']); exit;
    }
    $ext = match ($mime) {
        'image/png', 'image/x-png'               => 'png',
        'image/webp'                             => 'webp',
        'image/gif'                              => 'gif',
        'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
        default => ($ext_from_name === 'jpeg' ? 'jpg' : ($ext_from_name ?: 'jpg')),
    };
    $root    = dirname(__DIR__);
    $dir     = $root . '/uploads/products/';
    $pub_dir = $root . '/public/uploads/products/';
    if (!is_dir($dir))     @mkdir($dir, 0775, true);
    if (!is_dir($pub_dir)) @mkdir($pub_dir, 0775, true);
    if (!is_writable($dir)) @chmod($dir, 0775);

    $fname = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $dest  = $dir . $fname;
    if (@move_uploaded_file($file['tmp_name'], $dest)) {
        @chmod($dest, 0664);
        if (is_dir($pub_dir)) {
            @copy($dest, $pub_dir . $fname);
            @chmod($pub_dir . $fname, 0664);
        }
        $url  = BASE_URL . '/uploads/products/' . $fname;
        $path = '/uploads/products/' . $fname;
        echo json_encode(['success' => true, 'url' => $url, 'path' => $path]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save file. Check folder permissions for /uploads/products/.']);
    }
    exit;
}

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Delete old image file
            $row = $pdo->prepare('SELECT image_path, image_url FROM products WHERE id=?');
            $row->execute([$id]);
            $p = $row->fetch();
            if ($p && !empty($p['image_path'])) {
                delete_product_image_file($p['image_path']);
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

            // Fetch current product from DB if editing
            $curr_db_row = null;
            if ($id > 0) {
                $cur = $pdo->prepare('SELECT image_path, image_url FROM products WHERE id=?');
                $cur->execute([$id]);
                $curr_db_row = $cur->fetch();
            }

            $remove_image        = !empty($_POST['remove_image']);
            $existing_image_path = trim($_POST['existing_image_path'] ?? '');
            $has_new_upload      = !empty($_FILES['product_image']['name']);

            $image_path    = $existing_image_path;
            $image_url_val = $image_path ? basename($image_path) : ($curr_db_row['image_url'] ?? '');

            // Case A: User explicitly clicked "Remove Image" and did not select a replacement
            if ($remove_image && !$has_new_upload) {
                if ($curr_db_row && !empty($curr_db_row['image_path'])) {
                    delete_product_image_file($curr_db_row['image_path']);
                } elseif ($existing_image_path) {
                    delete_product_image_file($existing_image_path);
                }
                $image_path    = '';
                $image_url_val = '';
            }

            // Case B: User uploaded a new image
            if ($has_new_upload) {
                $file = $_FILES['product_image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $upload_errors = [
                        UPLOAD_ERR_INI_SIZE   => 'Image exceeds server upload_max_filesize limit (' . ini_get('upload_max_filesize') . '). Please choose a smaller image (under 5 MB).',
                        UPLOAD_ERR_FORM_SIZE  => 'Image exceeds maximum form size limit.',
                        UPLOAD_ERR_PARTIAL    => 'Image was only partially uploaded. Please try again.',
                        UPLOAD_ERR_NO_FILE    => 'No image was uploaded.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write image to disk (server permission issue).',
                        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
                    ];
                    $flash = $upload_errors[$file['error']] ?? ('Image upload error (code ' . $file['error'] . ').');
                    $flash_type = 'error';
                } elseif ($file['size'] > 10 * 1024 * 1024) {
                    $flash = 'File is too large (max 10 MB). Please upload a smaller image.';
                    $flash_type = 'error';
                } else {
                    $mime = get_uploaded_image_mime($file);
                    $ext_from_name = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed_mimes = [
                        'image/jpeg', 'image/jpg', 'image/pjpeg',
                        'image/png', 'image/x-png',
                        'image/webp',
                        'image/gif'
                    ];
                    $allowed_exts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                    $is_valid_mime = in_array($mime, $allowed_mimes, true);
                    $is_valid_ext  = in_array($ext_from_name, $allowed_exts, true);

                    if ($is_valid_mime || ($is_valid_ext && @getimagesize($file['tmp_name']) !== false)) {
                        $ext = match ($mime) {
                            'image/png', 'image/x-png'               => 'png',
                            'image/webp'                             => 'webp',
                            'image/gif'                              => 'gif',
                            'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
                            default => ($ext_from_name === 'jpeg' ? 'jpg' : ($ext_from_name ?: 'jpg')),
                        };

                        $root    = dirname(__DIR__);
                        $dir     = $root . '/uploads/products/';
                        $pub_dir = $root . '/public/uploads/products/';

                        if (!is_dir($dir))     @mkdir($dir, 0775, true);
                        if (!is_dir($pub_dir)) @mkdir($pub_dir, 0775, true);
                        if (!is_writable($dir)) @chmod($dir, 0775);

                        $fname = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
                        $dest  = $dir . $fname;

                        if (@move_uploaded_file($file['tmp_name'], $dest)) {
                            @chmod($dest, 0664);
                            if (is_dir($pub_dir)) {
                                @copy($dest, $pub_dir . $fname);
                                @chmod($pub_dir . $fname, 0664);
                            }

                            // Delete previous image file from server
                            if ($curr_db_row && !empty($curr_db_row['image_path'])) {
                                delete_product_image_file($curr_db_row['image_path']);
                            } elseif ($existing_image_path) {
                                delete_product_image_file($existing_image_path);
                            }

                            $image_path    = '/uploads/products/' . $fname;
                            $image_url_val = $fname;
                        } else {
                            $flash = 'Failed to save image. Please check folder permissions on server for /uploads/products/ (CHMOD 775 or 777).';
                            $flash_type = 'error';
                        }
                    } else {
                        $flash = 'Invalid image format. Allowed formats: JPEG, PNG, WebP, GIF.';
                        $flash_type = 'error';
                    }
                }
            }

            if ($flash_type !== 'error') {
                $slug = product_slug($name, $pdo, $id);

                if ($id > 0) {
                    $pdo->prepare(
                        'UPDATE products SET
                           name=?, slug=?, category_id=?, category=?, description=?,
                           features=?, applications=?, image_path=?, image_url=?, certifications=?,
                           is_featured=?, is_active=?, sort_order=?
                         WHERE id=?'
                    )->execute([
                        sanitize($name), $slug,
                        $category_id ?: null, $cat_name,
                        sanitize($_POST['description'] ?? ''),
                        sanitize($_POST['features']    ?? ''),
                        sanitize($_POST['applications']?? ''),
                        $image_path,
                        $image_url_val,
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
                            applications, image_path, image_url, certifications, is_featured, is_active, sort_order)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)'
                    )->execute([
                        sanitize($name), $slug,
                        $category_id ?: null, $cat_name,
                        sanitize($_POST['description'] ?? ''),
                        sanitize($_POST['features']    ?? ''),
                        sanitize($_POST['applications']?? ''),
                        $image_path,
                        $image_url_val,
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
            } else {
                // Keep form populated and open on error
                $edit_row = [
                    'id'             => $id,
                    'name'           => $name,
                    'category_id'    => $category_id,
                    'description'    => $_POST['description'] ?? '',
                    'features'       => $_POST['features'] ?? '',
                    'applications'   => $_POST['applications'] ?? '',
                    'certifications' => $_POST['certifications'] ?? '',
                    'image_path'     => $image_path,
                    'image_url'      => $image_url_val,
                    'sort_order'     => (int)($_POST['sort_order'] ?? 0),
                    'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
                    'is_active'      => isset($_POST['is_active']) ? 1 : 0,
                ];
                $show_form = true;
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
$edit_id  = (int)($_GET['edit'] ?? ($_POST['id'] ?? 0));
if (!isset($edit_row)) {
    $edit_row = null;
    if ($edit_id > 0) {
        $s = $pdo->prepare('SELECT * FROM products WHERE id=?');
        $s->execute([$edit_id]);
        $edit_row = $s->fetch() ?: null;
    }
}
if (!isset($show_form)) {
    $show_form = isset($_GET['new']) || $edit_row !== null;
}

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
          <?php $current_img = $edit_row ? get_product_image_url($edit_row) : ''; ?>
          <div id="imgPreviewWrap" style="<?= empty($current_img) ? 'display:none;' : '' ?>margin-bottom:14px">
            <img id="imgPreview"
                 src="<?= htmlspecialchars($current_img) ?>"
                 alt="Preview"
                 style="max-height:180px;max-width:100%;border-radius:10px;object-fit:cover;border:2px solid var(--border)">
            <input type="hidden" name="existing_image_path" id="existing_image_path" value="<?= htmlspecialchars($edit_row['image_path'] ?? ($edit_row['image_url'] ?? '')) ?>">
            <input type="hidden" name="remove_image" id="remove_image" value="0">
            <button type="button" id="removeImg" class="btn btn-danger btn-sm" style="margin-top:8px;<?= empty($current_img) ? 'display:none;' : 'display:inline-flex;' ?>">
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
            <span style="font-size:.74rem;color:var(--muted)">JPEG, PNG, WebP, GIF · Max 10 MB</span>
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
        <div class="form-hint">Recommended: 800×600px or 16:10 ratio · JPEG, PNG, WebP, GIF (Max 10 MB) · Replaces existing image when changed</div>
      </div>

      <!-- Sort Order -->
      <div class="form-group" style="max-width:260px">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" min="0"
               value="<?= (int)($edit_row['sort_order'] ?? 0) ?>">
        <div class="form-hint">Lower = shown first</div>
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
            <?php $p_img = get_product_image_url($p); ?>
            <?php if ($p_img): ?>
              <img src="<?= htmlspecialchars($p_img) ?>"
                   alt="<?= htmlspecialchars($p['name']) ?>"
                   style="width:56px;height:42px;object-fit:cover;border-radius:7px;border:1px solid var(--border)"
                   onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
              <div style="display:none;width:56px;height:42px;background:var(--sand);border-radius:7px;place-items:center;border:1px dashed var(--border-dark)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;color:var(--muted)"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5M21 6.75A2.25 2.25 0 0018.75 4.5H5.25A2.25 2.25 0 003 6.75v10.5A2.25 2.25 0 005.25 19.5H18.75A2.25 2.25 0 0021 17.25V6.75z"/></svg>
              </div>
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
  const fileInput     = document.getElementById('product_image');
  const dropZone      = document.getElementById('uploadDrop');
  const preview       = document.getElementById('imgPreview');
  const previewWrap   = document.getElementById('imgPreviewWrap');
  const removeBtn     = document.getElementById('removeImg');
  const existingInput = document.getElementById('existing_image_path');
  const removeFlag    = document.getElementById('remove_image');

  if (!fileInput) return;

  // Drag & drop styling
  dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    e.stopPropagation();
    dropZone.style.borderColor = 'var(--green-500)';
    dropZone.style.background = 'var(--green-50)';
  });
  dropZone.addEventListener('dragleave', e => {
    e.preventDefault();
    e.stopPropagation();
    dropZone.style.borderColor = '';
    dropZone.style.background = '';
  });
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    e.stopPropagation();
    dropZone.style.borderColor = '';
    dropZone.style.background = '';
    const files = e.dataTransfer.files;
    if (files.length) {
      const dt = new DataTransfer();
      dt.items.add(files[0]);
      fileInput.files = dt.files;
      handleFileSelect(files[0]);
    }
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files && fileInput.files[0]) {
      handleFileSelect(fileInput.files[0]);
    }
  });

  function handleFileSelect(file) {
    if (!file.type.startsWith('image/')) {
      alert('Please select a valid image file (JPEG, PNG, WebP, GIF).');
      return;
    }
    if (file.size > 10 * 1024 * 1024) {
      alert('File must be under 10 MB.');
      return;
    }

    // Reset remove flag since a file was chosen
    if (removeFlag) removeFlag.value = '0';

    // Show instant local preview
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      previewWrap.style.display = 'block';
      if (removeBtn) removeBtn.style.display = 'inline-flex';
    };
    reader.readAsDataURL(file);
  }

  // Remove image
  if (removeBtn) {
    removeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      preview.src = '';
      previewWrap.style.display = 'none';
      if (existingInput) existingInput.value = '';
      if (fileInput) fileInput.value = '';
      if (removeFlag) removeFlag.value = '1';
    });
  }

  // Auto-hide flash message
  const flash = document.getElementById('flash-msg');
  if (flash) setTimeout(() => { flash.style.opacity='0'; flash.style.transition='opacity .5s'; setTimeout(()=>flash.remove(),500); }, 4000);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo        = db();
$flash      = '';
$flash_type = 'success';

// ── POST handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id   = (int)($_POST['id'] ?? 0);
        $name = sanitize(trim($_POST['name'] ?? ''));
        $desc = sanitize($_POST['description'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);

        if ($name === '') {
            $flash = 'Category name is required.';
            $flash_type = 'error';
        } else {
            try {
                if ($id > 0) {
                    $pdo->prepare('UPDATE categories SET name=?,description=?,sort_order=? WHERE id=?')
                        ->execute([$name, $desc, $sort, $id]);
                    // Sync category name string on all products using this category
                    $pdo->prepare('UPDATE products SET category=? WHERE category_id=?')
                        ->execute([$name, $id]);
                    log_activity('category_update', "Category #$id renamed to '$name'.");
                    $flash = 'Category updated.';
                } else {
                    // Generate unique slug
                    $base = slugify($name); $slug = $base; $i = 1;
                    while(true){
                        $s=$pdo->prepare('SELECT id FROM categories WHERE slug=?');
                        $s->execute([$slug]);
                        if(!$s->fetch()) break;
                        $slug = $base.'-'.$i++;
                    }
                    $pdo->prepare('INSERT INTO categories (name,slug,description,sort_order) VALUES (?,?,?,?)')
                        ->execute([$name, $slug, $desc, $sort]);
                    log_activity('category_create', "Category '$name' created.");
                    $flash = "Category '$name' created.";
                }
            } catch (PDOException $e) {
                $flash = str_contains($e->getMessage(),'Duplicate') ? 'Category name already exists.' : 'Database error.';
                $flash_type = 'error';
            }
            header('Location: categories.php?flash=' . urlencode($flash));
            exit;
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $name_row = $pdo->prepare('SELECT name FROM categories WHERE id=?');
            $name_row->execute([$id]);
            $cat_name = $name_row->fetchColumn();
            // Un-assign products
            $pdo->prepare('UPDATE products SET category_id=NULL, category="" WHERE category_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
            log_activity('category_delete', "Category '$cat_name' deleted.");
            header('Location: categories.php?flash=' . urlencode("Category '$cat_name' deleted."));
            exit;
        }

    } elseif ($action === 'toggle_active') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare('UPDATE categories SET is_active = NOT is_active WHERE id=?')->execute([$id]);
            header('Location: categories.php'); exit;
        }
    }
}

if (isset($_GET['flash']) && !$flash) $flash = htmlspecialchars($_GET['flash']);

// ── Fetch ─────────────────────────────────────────────────────
$categories = $pdo->query(
    'SELECT c.*, COUNT(p.id) AS product_count
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.id
     GROUP BY c.id ORDER BY c.sort_order ASC, c.name ASC'
)->fetchAll();

// Edit row
$edit_id  = (int)($_GET['edit'] ?? 0);
$edit_row = null;
if ($edit_id > 0) {
    $s = $pdo->prepare('SELECT * FROM categories WHERE id=?');
    $s->execute([$edit_id]);
    $edit_row = $s->fetch();
}
$show_form = isset($_GET['new']) || $edit_row !== null;

$page_title  = 'Categories';
$breadcrumbs = [['Categories', '']];
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Categories</h1>
    <p class="page-subtitle"><?= count($categories) ?> categories &mdash; <a href="products.php" style="color:var(--green-600);font-size:.82rem">Back to Products</a></p>
  </div>
  <a href="categories.php?new=1" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Add Category
  </a>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>" id="flash-msg">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <?= $flash ?>
</div>
<?php endif; ?>

<?php if ($show_form): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <div class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
      <?= $edit_row ? 'Edit Category' : 'Add New Category' ?>
    </div>
    <a href="categories.php" class="btn btn-ghost btn-sm">Cancel</a>
  </div>
  <div class="card-body">
    <form method="POST" action="categories.php">
      <input type="hidden" name="action" value="save">
      <?php if ($edit_row): ?><input type="hidden" name="id" value="<?= $edit_row['id'] ?>"><?php endif; ?>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Category Name <span class="required">*</span></label>
          <input type="text" name="name" class="form-control" required
                 value="<?= htmlspecialchars($edit_row['name'] ?? '') ?>"
                 placeholder="e.g. Carry Bags">
        </div>
        <div class="form-group">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" min="0"
                 value="<?= (int)($edit_row['sort_order'] ?? 0) ?>">
          <div class="form-hint">Lower = shown first</div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Description <span class="text-muted text-sm" style="font-weight:400">(optional)</span></label>
        <textarea name="description" class="form-control" rows="2"
                  placeholder="Short description for this category…"><?= htmlspecialchars($edit_row['description'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <?= $edit_row ? 'Update Category' : 'Create Category' ?>
        </button>
        <a href="categories.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrap">
    <?php if (empty($categories)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
        <h3>No categories yet</h3>
        <p><a href="categories.php?new=1" style="color:var(--green-600)">Add your first category</a></p>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Description</th>
          <th>Products</th>
          <th>Status</th>
          <th>Order</th>
          <th style="width:100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr>
          <td class="text-muted text-sm"><?= $cat['id'] ?></td>
          <td class="td-name"><?= htmlspecialchars($cat['name']) ?></td>
          <td class="text-muted text-sm"><?= htmlspecialchars($cat['slug']) ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars(mb_strimwidth($cat['description'] ?? '', 0, 50, '…')) ?></td>
          <td>
            <a href="products.php?cat=<?= $cat['id'] ?>"
               style="color:var(--green-600);font-weight:600;font-size:.82rem">
              <?= (int)$cat['product_count'] ?> product<?= $cat['product_count'] != 1 ? 's' : '' ?>
            </a>
          </td>
          <td>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="toggle_active">
              <input type="hidden" name="id" value="<?= $cat['id'] ?>">
              <button type="submit" class="badge <?= $cat['is_active'] ? 'badge-active' : 'badge-inactive' ?>"
                      style="border:none;cursor:pointer;background:transparent;padding:3px 10px">
                <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
              </button>
            </form>
          </td>
          <td class="text-muted text-sm" style="text-align:center"><?= $cat['sort_order'] ?></td>
          <td>
            <div style="display:flex;gap:5px">
              <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-ghost btn-icon btn-sm" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
              </a>
              <form method="POST" onsubmit="return confirm('Delete this category? Products will be un-categorised.')" style="display:inline">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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
const flash = document.getElementById('flash-msg');
if (flash) setTimeout(() => { flash.style.opacity='0'; flash.style.transition='opacity .5s'; setTimeout(()=>flash.remove(),500); }, 3500);
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

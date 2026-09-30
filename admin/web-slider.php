<?php
require_once __DIR__ . '/includes/auth_check.php';

// ── Ensure hero_slides table exists ───────────────────────
$pdo = db();
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `hero_slides` (
      `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
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
      INDEX `idx_sort` (`sort_order`),
      INDEX `idx_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// ── Fetch all slides ───────────────────────────────────────
$slides = $pdo->query('SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);

$page_title  = 'Web Slider';
$breadcrumbs = [['Web Slider', '']];
include __DIR__ . '/includes/header.php';
?>

<!-- ══════════════════════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════════════════════ -->
<div class="page-header">
  <div>
    <h1 class="page-title">Web Slider</h1>
    <p class="page-subtitle">Manage the hero section slides — images, headlines and call-to-action buttons.</p>
  </div>
  <button class="btn btn-primary" id="btn-add-slide" onclick="openModal()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Add New Slide
  </button>
</div>

<!-- Flash messages (injected by JS) -->
<div id="flash-zone"></div>

<!-- ══════════════════════════════════════════════════════════
     SLIDES LIST
═══════════════════════════════════════════════════════════ -->
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <div class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
      All Slides
      <span class="badge" style="background:var(--green-100,#dcfce7);color:var(--green-700,#15803d);font-size:.72rem;padding:2px 8px;border-radius:20px;margin-left:6px" id="slide-count"><?= count($slides) ?></span>
    </div>
    <span class="text-muted text-sm">Drag rows to reorder · Changes save automatically</span>
  </div>

  <div id="slides-container">
  <?php if (empty($slides)): ?>
    <div class="empty-state" id="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
      <h3>No slides yet</h3>
      <p>Click "Add New Slide" to create your first hero slide.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table id="slides-table">
        <thead>
          <tr>
            <th style="width:40px"></th>
            <th style="width:80px">Image</th>
            <th>Title / Subtitle</th>
            <th style="width:120px">Button</th>
            <th style="width:80px">Status</th>
            <th style="width:90px">Order</th>
            <th style="width:100px">Actions</th>
          </tr>
        </thead>
        <tbody id="slides-tbody">
          <?php foreach ($slides as $s): ?>
          <tr class="slide-row" data-id="<?= $s['id'] ?>" style="cursor:grab">
            <td style="color:#aaa;text-align:center;font-size:18px;cursor:grab" class="drag-handle" title="Drag to reorder">⠿</td>
            <td>
              <?php if ($s['image_url']): ?>
                <img src="<?= htmlspecialchars($s['image_url']) ?>"
                     alt="<?= htmlspecialchars($s['title']) ?>"
                     style="width:70px;height:45px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
              <?php else: ?>
                <div style="width:70px;height:45px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:20px;border:1px solid #e5e7eb">🖼</div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600;font-size:.88rem;color:var(--text-primary,#111)"><?= htmlspecialchars($s['title'] ?: '—') ?></div>
              <?php if ($s['subtitle']): ?>
                <div style="font-size:.78rem;color:var(--text-muted,#6b7280);margin-top:2px"><?= htmlspecialchars($s['subtitle']) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($s['button_text']): ?>
                <span style="display:inline-block;padding:2px 10px;background:#f0fdf4;border:1px solid #86efac;border-radius:20px;font-size:.74rem;color:#166534;font-weight:600">
                  <?= htmlspecialchars($s['button_text']) ?>
                </span>
              <?php else: ?>
                <span style="color:#d1d5db;font-size:.82rem">—</span>
              <?php endif; ?>
            </td>
            <td>
              <label class="toggle-label" title="Toggle active" style="cursor:pointer">
                <input type="checkbox" class="slide-toggle" data-id="<?= $s['id'] ?>" <?= $s['is_active'] ? 'checked' : '' ?> style="display:none">
                <span class="status-pill <?= $s['is_active'] ? 'badge-replied' : 'badge-archived' ?>" style="cursor:pointer">
                  <?= $s['is_active'] ? 'Active' : 'Hidden' ?>
                </span>
              </label>
            </td>
            <td style="text-align:center;font-size:.82rem;color:var(--text-muted,#6b7280)"><?= (int)$s['sort_order'] ?></td>
            <td>
              <div style="display:flex;gap:6px">
                <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="editSlide(<?= $s['id'] ?>)">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                </button>
                <button class="btn btn-ghost btn-sm btn-icon" title="Delete" style="color:#ef4444" onclick="deleteSlide(<?= $s['id'] ?>, this)">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL — Add / Edit Slide
═══════════════════════════════════════════════════════════ -->
<div id="slide-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.55);overflow-y:auto;padding:24px 16px">
  <div style="background:#fff;border-radius:16px;max-width:680px;margin:0 auto;box-shadow:0 25px 60px rgba(0,0,0,.25)">

    <!-- Modal header -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb">
      <div>
        <h2 id="modal-title" style="font-size:1.1rem;font-weight:700;margin:0;color:#111">Add New Slide</h2>
        <p id="modal-sub" style="margin:2px 0 0;font-size:.8rem;color:#6b7280">Fill in the slide details and upload an image</p>
      </div>
      <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;color:#6b7280;padding:4px">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Modal body -->
    <form id="slide-form" enctype="multipart/form-data" onsubmit="submitSlide(event)">
      <input type="hidden" id="slide-id" name="slide_id" value="">

      <div style="padding:24px;display:grid;gap:16px">

        <!-- Image upload -->
        <div>
          <label class="form-label" style="display:block;margin-bottom:8px">Hero Image</label>
          <!-- Preview -->
          <div id="img-preview-wrap" style="margin-bottom:12px;display:none">
            <img id="img-preview" src="" alt="Preview" style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;border:2px solid #e5e7eb">
            <div id="img-current-label" style="font-size:.75rem;color:#6b7280;margin-top:4px;text-align:center">Current image</div>
          </div>
          <!-- Drop zone -->
          <div id="drop-zone"
               onclick="document.getElementById('img-file').click()"
               ondragover="event.preventDefault();this.style.borderColor='#16a34a'"
               ondragleave="this.style.borderColor='#d1d5db'"
               ondrop="handleDrop(event)"
               style="border:2px dashed #d1d5db;border-radius:10px;padding:28px 20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;background:#fafafa">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" style="width:36px;height:36px;display:block;margin:0 auto 8px"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            <div style="font-size:.85rem;font-weight:600;color:#374151">Click or drag & drop an image here</div>
            <div style="font-size:.75rem;color:#9ca3af;margin-top:4px">JPEG, PNG, WebP, GIF · Max 5 MB</div>
          </div>
          <input type="file" id="img-file" name="image" accept="image/*" style="display:none" onchange="previewFile(this)">
        </div>

        <!-- Title -->
        <div>
          <label class="form-label" for="f-title">Headline / Title <span style="color:#ef4444">*</span></label>
          <input type="text" id="f-title" name="title" class="form-control" placeholder="e.g. Sustainable Packaging for a Greener Tomorrow" required>
        </div>

        <!-- Subtitle & Eyebrow row -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label class="form-label" for="f-subtitle">Eyebrow / Sub-label</label>
            <input type="text" id="f-subtitle" name="subtitle" class="form-control" placeholder="e.g. Let's Reduce Plastic">
          </div>
          <div>
            <label class="form-label" for="f-btn-text">Button Label</label>
            <input type="text" id="f-btn-text" name="button_text" class="form-control" placeholder="e.g. Get a Quote">
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="form-label" for="f-desc">Description / Sub-copy</label>
          <textarea id="f-desc" name="description" class="form-control" rows="2" placeholder="Short supporting text shown below the headline"></textarea>
        </div>

        <!-- Button URL & Status row -->
        <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:end">
          <div>
            <label class="form-label" for="f-btn-url">Button URL</label>
            <input type="text" id="f-btn-url" name="button_url" class="form-control" placeholder="/contact or https://...">
          </div>
          <div>
            <label class="form-label">Status</label>
            <div style="display:flex;gap:10px;align-items:center;padding-top:4px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.85rem">
                <input type="radio" name="is_active" value="1" id="f-active-yes" checked> Active
              </label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.85rem">
                <input type="radio" name="is_active" value="0" id="f-active-no"> Hidden
              </label>
            </div>
          </div>
        </div>

      </div><!-- /padding -->

      <!-- Modal footer -->
      <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid #e5e7eb">
        <div id="modal-spinner" style="display:none;color:#6b7280;font-size:.83rem">Saving…</div>
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="modal-submit-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span id="modal-btn-label">Save Slide</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Inline styles for this page -->
<style>
.slide-row { transition: background .15s; }
.slide-row:hover { background: #f9fafb; }
.slide-row.dragging { opacity: .4; background: #f0fdf4; }
.drag-handle { user-select: none; }
.toggle-label input { display:none; }
.status-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; cursor:pointer; transition:all .15s; }
.alert-slide { padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:.85rem; display:flex; align-items:center; gap:8px; }
.alert-slide.success { background:#f0fdf4; border:1px solid #86efac; color:#166534; }
.alert-slide.error   { background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; }
#slide-modal { animation: fadeIn .15s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
</style>

<!-- ══════════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════════ -->
<script>
const API = '<?= API_URL ?>/slider.php';

// ── Slide data (PHP → JS) ────────────────────────────────
let slides = <?= json_encode(array_values($slides)) ?>;

// ── Flash ────────────────────────────────────────────────
function flash(msg, type = 'success') {
  const z = document.getElementById('flash-zone');
  const el = document.createElement('div');
  el.className = `alert-slide ${type}`;
  el.innerHTML = (type === 'success'
    ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>')
    + ' ' + msg;
  z.prepend(el);
  setTimeout(() => el.style.opacity = '0', 3500);
  setTimeout(() => el.remove(), 3800);
}

// ── Modal open/close ─────────────────────────────────────
function openModal(slide = null) {
  document.getElementById('slide-form').reset();
  document.getElementById('img-preview-wrap').style.display = 'none';
  document.getElementById('img-preview').src = '';
  document.getElementById('slide-id').value = '';
  document.getElementById('modal-title').textContent = 'Add New Slide';
  document.getElementById('modal-sub').textContent = 'Fill in the slide details and upload an image';
  document.getElementById('modal-btn-label').textContent = 'Save Slide';

  if (slide) {
    document.getElementById('modal-title').textContent = 'Edit Slide';
    document.getElementById('modal-sub').textContent = 'Update the slide content or upload a new image';
    document.getElementById('modal-btn-label').textContent = 'Update Slide';
    document.getElementById('slide-id').value = slide.id;
    document.getElementById('f-title').value = slide.title || '';
    document.getElementById('f-subtitle').value = slide.subtitle || '';
    document.getElementById('f-desc').value = slide.description || '';
    document.getElementById('f-btn-text').value = slide.button_text || '';
    document.getElementById('f-btn-url').value = slide.button_url || '';
    document.querySelector(`input[name="is_active"][value="${slide.is_active}"]`).checked = true;
    if (slide.image_url) {
      document.getElementById('img-preview').src = slide.image_url;
      document.getElementById('img-preview-wrap').style.display = 'block';
    }
  }
  document.getElementById('slide-modal').style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('slide-modal').style.display = 'none';
  document.body.style.overflow = '';
}

document.getElementById('slide-modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// ── Edit button ───────────────────────────────────────────
function editSlide(id) {
  const s = slides.find(x => x.id == id);
  if (s) openModal(s);
}

// ── Image preview / drag-drop ─────────────────────────────
function previewFile(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('img-preview').src = e.target.result;
    document.getElementById('img-preview-wrap').style.display = 'block';
    document.getElementById('img-current-label').textContent = 'New image selected';
  };
  reader.readAsDataURL(file);
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('drop-zone').style.borderColor = '#d1d5db';
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('img-file').files = dt.files;
    previewFile(document.getElementById('img-file'));
  }
}

// ── Submit form ───────────────────────────────────────────
async function submitSlide(e) {
  e.preventDefault();
  const btn   = document.getElementById('modal-submit-btn');
  const spin  = document.getElementById('modal-spinner');
  btn.disabled = true;
  spin.style.display = 'inline';

  const id  = document.getElementById('slide-id').value;
  const fd  = new FormData(document.getElementById('slide-form'));

  const url = id
    ? `${API}?action=update&id=${id}`
    : API;

  try {
    const res = await fetch(url, { method: 'POST', body: fd, credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);

    const slide = json.data;
    if (id) {
      // Update in local array
      const idx = slides.findIndex(s => s.id == id);
      if (idx >= 0) slides[idx] = slide;
      flash('Slide updated successfully!');
    } else {
      slides.push(slide);
      flash('Slide created successfully!');
    }
    closeModal();
    refreshTable();
  } catch(err) {
    flash('Error: ' + err.message, 'error');
  } finally {
    btn.disabled = false;
    spin.style.display = 'none';
  }
}

// ── Delete ────────────────────────────────────────────────
async function deleteSlide(id, btn) {
  if (!confirm('Delete this slide permanently?')) return;
  btn.disabled = true;
  try {
    const res = await fetch(`${API}?action=delete&id=${id}`, { method: 'POST', credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    slides = slides.filter(s => s.id != id);
    flash('Slide deleted.');
    refreshTable();
  } catch(err) {
    flash('Error: ' + err.message, 'error');
    btn.disabled = false;
  }
}

// ── Toggle active ─────────────────────────────────────────
document.getElementById('slides-container').addEventListener('change', async function(e) {
  if (!e.target.classList.contains('slide-toggle')) return;
  const id        = e.target.dataset.id;
  const isActive  = e.target.checked ? 1 : 0;
  const slide     = slides.find(s => s.id == id);
  if (!slide) return;

  // Optimistic UI
  const pill = e.target.nextElementSibling;
  pill.textContent = isActive ? 'Active' : 'Hidden';
  pill.className = 'status-pill ' + (isActive ? 'badge-replied' : 'badge-archived');

  const fd = new FormData();
  fd.append('title',       slide.title || '');
  fd.append('subtitle',    slide.subtitle || '');
  fd.append('description', slide.description || '');
  fd.append('button_text', slide.button_text || '');
  fd.append('button_url',  slide.button_url || '');
  fd.append('image_url',   slide.image_url || '');
  fd.append('sort_order',  slide.sort_order);
  fd.append('is_active',   isActive);

  try {
    const res = await fetch(`${API}?action=update&id=${id}`, { method: 'POST', body: fd, credentials: 'include' });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    slide.is_active = isActive;
  } catch(err) {
    // Revert
    e.target.checked = !e.target.checked;
    pill.textContent = e.target.checked ? 'Active' : 'Hidden';
    pill.className = 'status-pill ' + (e.target.checked ? 'badge-replied' : 'badge-archived');
    flash('Error: ' + err.message, 'error');
  }
});

// ── Rebuild table from slides[] ───────────────────────────
function refreshTable() {
  const container = document.getElementById('slides-container');
  document.getElementById('slide-count').textContent = slides.length;

  if (slides.length === 0) {
    container.innerHTML = `
      <div class="empty-state" id="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
        <h3>No slides yet</h3>
        <p>Click "Add New Slide" to create your first hero slide.</p>
      </div>`;
    return;
  }

  const rows = slides.map(s => `
    <tr class="slide-row" data-id="${s.id}" style="cursor:grab">
      <td style="color:#aaa;text-align:center;font-size:18px;cursor:grab" class="drag-handle" title="Drag to reorder">⠿</td>
      <td>
        ${s.image_url
          ? `<img src="${escHtml(s.image_url)}" alt="${escHtml(s.title)}" style="width:70px;height:45px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb">`
          : `<div style="width:70px;height:45px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:20px;border:1px solid #e5e7eb">🖼</div>`
        }
      </td>
      <td>
        <div style="font-weight:600;font-size:.88rem;color:#111">${escHtml(s.title || '—')}</div>
        ${s.subtitle ? `<div style="font-size:.78rem;color:#6b7280;margin-top:2px">${escHtml(s.subtitle)}</div>` : ''}
      </td>
      <td>
        ${s.button_text
          ? `<span style="display:inline-block;padding:2px 10px;background:#f0fdf4;border:1px solid #86efac;border-radius:20px;font-size:.74rem;color:#166534;font-weight:600">${escHtml(s.button_text)}</span>`
          : `<span style="color:#d1d5db;font-size:.82rem">—</span>`
        }
      </td>
      <td>
        <label class="toggle-label" title="Toggle active" style="cursor:pointer">
          <input type="checkbox" class="slide-toggle" data-id="${s.id}" ${s.is_active ? 'checked' : ''} style="display:none">
          <span class="status-pill ${s.is_active ? 'badge-replied' : 'badge-archived'}" style="cursor:pointer">
            ${s.is_active ? 'Active' : 'Hidden'}
          </span>
        </label>
      </td>
      <td style="text-align:center;font-size:.82rem;color:#6b7280">${s.sort_order}</td>
      <td>
        <div style="display:flex;gap:6px">
          <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="editSlide(${s.id})">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
          </button>
          <button class="btn btn-ghost btn-sm btn-icon" title="Delete" style="color:#ef4444" onclick="deleteSlide(${s.id}, this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
          </button>
        </div>
      </td>
    </tr>`).join('');

  container.innerHTML = `
    <div class="table-wrap">
      <table id="slides-table">
        <thead>
          <tr>
            <th style="width:40px"></th>
            <th style="width:80px">Image</th>
            <th>Title / Subtitle</th>
            <th style="width:120px">Button</th>
            <th style="width:80px">Status</th>
            <th style="width:90px">Order</th>
            <th style="width:100px">Actions</th>
          </tr>
        </thead>
        <tbody id="slides-tbody">${rows}</tbody>
      </table>
    </div>`;
  initDrag();
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Drag-to-reorder ───────────────────────────────────────
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

    // Build new order
    const order = getRows().map(r => parseInt(r.dataset.id));
    order.forEach((id, i) => {
      const s = slides.find(x => x.id == id);
      if (s) s.sort_order = i + 1;
      // Update displayed order col
      const row = tbody.querySelector(`[data-id="${id}"]`);
      if (row) {
        const cells = row.querySelectorAll('td');
        if (cells[5]) cells[5].textContent = i + 1;
      }
    });

    try {
      const res = await fetch(`${API}?action=reorder`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ order })
      });
      const json = await res.json();
      if (!json.success) throw new Error(json.message);
      flash('Slide order saved.');
    } catch(err) {
      flash('Error saving order: ' + err.message, 'error');
    }
  });

  // Make rows draggable
  getRows().forEach(row => { row.setAttribute('draggable', 'true'); });
}

// Init drag on page load
initDrag();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

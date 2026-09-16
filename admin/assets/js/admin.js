/* ============================================================
   EVERGREENINDUSTRY — Admin Panel JS
   ============================================================ */

/* ── Toast notifications ─────────────────────────────────── */
const Toast = {
  container: null,

  init() {
    this.container = document.createElement('div');
    this.container.className = 'toast-container';
    document.body.appendChild(this.container);
  },

  show(message, type = 'success', duration = 3500) {
    const icons = {
      success: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
      error:   `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>`,
      info:    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>`,
    };
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `${icons[type] || ''}<span>${message}</span>`;
    this.container.appendChild(t);
    setTimeout(() => {
      t.style.animation = 'slide-up .3s ease reverse';
      setTimeout(() => t.remove(), 300);
    }, duration);
  },

  success(msg) { this.show(msg, 'success'); },
  error(msg)   { this.show(msg, 'error'); },
  info(msg)    { this.show(msg, 'info'); },
};

/* ── Modal ───────────────────────────────────────────────── */
const Modal = {
  open(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
  },
  close(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
  },
  closeAll() {
    document.querySelectorAll('.modal-overlay.open').forEach(m => {
      m.classList.remove('open');
    });
    document.body.style.overflow = '';
  },
};

/* ── Sidebar mobile toggle ───────────────────────────────── */
function initSidebar() {
  const sidebar  = document.querySelector('.sidebar');
  const overlay  = document.getElementById('sidebar-overlay');
  const toggles  = document.querySelectorAll('.menu-toggle');

  toggles.forEach(btn => {
    btn.addEventListener('click', () => {
      sidebar?.classList.toggle('open');
      overlay?.classList.toggle('open');
    });
  });

  overlay?.addEventListener('click', () => {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('open');
  });
}

/* ── Confirm delete helper ───────────────────────────────── */
function confirmDelete(message = 'Are you sure you want to delete this? This cannot be undone.') {
  return window.confirm(message);
}

/* ── API wrapper ─────────────────────────────────────────── */
const API = {
  base: (function() {
    // Auto-detect API base from current page URL
    // Works for any domain and any subfolder depth
    // e.g. https://domain.com/admin/dashboard.php → https://domain.com/backend/api
    // e.g. https://domain.com/mysite/admin/x.php  → https://domain.com/mysite/backend/api
    const path    = window.location.pathname;
    const adminIdx = path.lastIndexOf('/admin');
    const root    = adminIdx > 0 ? path.substring(0, adminIdx) : '';
    return window.location.origin + root + '/backend/api';
  })(),

  async call(endpoint, params = {}, options = {}) {
    const url = new URL(`${this.base}/${endpoint}`);
    if (options.method === 'GET' || !options.method) {
      Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
    }
    const res = await fetch(url.toString(), {
      method: options.method || 'GET',
      headers: { 'Content-Type': 'application/json', ...options.headers },
      credentials: 'include',
      body: options.method && options.method !== 'GET' ? JSON.stringify(params) : undefined,
    });
    return res.json();
  },

  get(endpoint, params = {})   { return this.call(endpoint, params, { method: 'GET' }); },
  post(endpoint, body = {})    { return this.call(endpoint, body,   { method: 'POST' }); },
  delete(endpoint, params = {}){ return this.call(endpoint, params, { method: 'DELETE' }); },
};

/* ── Form serializer ─────────────────────────────────────── */
function serializeForm(form) {
  const data = {};
  new FormData(form).forEach((v, k) => { data[k] = v; });
  // Handle checkboxes (unchecked ones won't appear in FormData)
  form.querySelectorAll('input[type=checkbox]').forEach(cb => {
    data[cb.name] = cb.checked;
  });
  return data;
}

/* ── Date formatter ──────────────────────────────────────── */
function fmtDate(str) {
  if (!str) return '—';
  const d = new Date(str);
  return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })
    + ' ' + d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
}

function fmtDateShort(str) {
  if (!str) return '—';
  return new Date(str).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}

/* ── Pagination renderer ─────────────────────────────────── */
function renderPagination(container, pagination, onPageChange) {
  if (!container || !pagination || pagination.total_pages <= 1) {
    if (container) container.innerHTML = '';
    return;
  }
  const { current_page, total_pages } = pagination;
  let html = '';

  const prevDisabled = current_page <= 1 ? 'disabled' : '';
  const nextDisabled = current_page >= total_pages ? 'disabled' : '';

  html += `<button class="page-btn" ${prevDisabled} data-page="${current_page - 1}">&#8592;</button>`;

  for (let i = 1; i <= total_pages; i++) {
    if (total_pages > 7 && Math.abs(i - current_page) > 2 && i !== 1 && i !== total_pages) {
      if (i === 2 || i === total_pages - 1) html += `<button class="page-btn" disabled>…</button>`;
      continue;
    }
    html += `<button class="page-btn ${i === current_page ? 'active' : ''}" data-page="${i}">${i}</button>`;
  }

  html += `<button class="page-btn" ${nextDisabled} data-page="${current_page + 1}">&#8594;</button>`;

  container.innerHTML = html;
  container.querySelectorAll('.page-btn:not([disabled])').forEach(btn => {
    btn.addEventListener('click', () => onPageChange(+btn.dataset.page));
  });
}

/* ── Status badge helper ─────────────────────────────────── */
function statusBadge(status) {
  const map = {
    new:      'badge-new',
    read:     'badge-read',
    replied:  'badge-replied',
    archived: 'badge-archived',
    active:   'badge-active',
    inactive: 'badge-inactive',
  };
  const cls = map[status] || 'badge-read';
  return `<span class="badge ${cls}">${status}</span>`;
}

/* ── Logout ──────────────────────────────────────────────── */
async function doLogout() {
  if (!confirm('Log out of the admin panel?')) return;
  // Detect base path from current URL
  // e.g. https://domain.com/admin/dashboard.php  → adminBase = /admin
  //      https://domain.com/mysite/admin/dashboard.php → adminBase = /mysite/admin
  const adminBase = window.location.pathname.substring(
    0, window.location.pathname.lastIndexOf('/admin/') + 7
  ).replace(/\/$/, '');  // strip trailing slash
  const apiBase = adminBase.replace(/\/admin$/, '') + '/backend/api';

  try {
    await fetch(apiBase + '/auth.php?action=logout', {
      method: 'POST', credentials: 'include'
    });
  } catch(_) {}
  window.location.href = adminBase + '/login.php';
}

/* ── Init ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  Toast.init();
  initSidebar();

  // Close modal on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
      if (e.target === overlay) Modal.closeAll();
    });
  });
});

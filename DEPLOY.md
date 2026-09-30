# Evergreen Industry — Deployment Guide
> Make the live site **identical** to local. Follow every step in order.

---

## Part A — Build the frontend

Run this **every time** you make code changes before uploading:

```bash
# Domain-root deploy (yourdomain.com/) — most common
VITE_BASE_PATH=/ npm run build:static

# OR subfolder deploy (yourdomain.com/evergreen-vision/)
VITE_BASE_PATH=/evergreen-vision npm run build:static
```

On Windows PowerShell:
```powershell
$env:VITE_BASE_PATH="/"; npm run build:static
```

Output goes to → `dist/`

---

## Part B — What to upload to the live server

You need to upload **two separate things**:

### 1. Frontend (the React SPA)
Upload the **entire contents** of `dist/` to your hosting's **web root** (`public_html/`):

```
dist/
├── index.html          → public_html/index.html
├── assets/             → public_html/assets/
├── .htaccess           → public_html/.htaccess   ← CRITICAL, must upload
├── favicon.ico         → public_html/favicon.ico
├── favicon.svg         → public_html/favicon.svg
├── e-logo.png          → public_html/e-logo.png
├── robots.txt          → public_html/robots.txt
├── uploads/            → public_html/uploads/    ← CRITICAL: hero + product images
│   ├── slides/         → public_html/uploads/slides/
│   └── products/       → public_html/uploads/products/
└── *.mp4 videos        → public_html/*.mp4
```

> **If deploying to a subfolder** (e.g. `public_html/evergreen-vision/`), upload into that folder
> AND rebuild with `VITE_BASE_PATH=/evergreen-vision`.

### 2. Backend (PHP + Admin)
Upload these folders **alongside** the frontend (same domain, same root):

```
backend/    → public_html/backend/
admin/      → public_html/admin/
uploads/    → public_html/uploads/    (create empty if missing)
```

Final structure on live server:
```
public_html/
├── index.html          ← React SPA
├── assets/             ← JS, CSS, hashed assets
├── .htaccess           ← SPA routing + backend passthrough
├── uploads/            ← hero slide + product images (755 writable)
│   ├── slides/         ← slider images (sprout-in-hands.jpg, home.png, etc.)
│   └── products/       ← product images
├── backend/            ← PHP API
│   ├── api/
│   └── config/
│       └── db.php      ← UPDATE credentials for live DB
└── admin/              ← PHP admin panel
```

> **Root cause of 404 image errors**: If `uploads/` is missing or empty on the live server,
> hero images will 404. The `dist/uploads/` folder (built above) contains all images.
> Upload it along with the rest of `dist/`.  
> Alternatively, run the one-time sync script after upload (see Part H below).

---

## Part C — Configure the live database

### 1. Create the database
- Login to your hosting cPanel → **phpMyAdmin**
- Create a new database, e.g. `ever_bio`
- Note the DB host, name, user, password

### 2. Update credentials
Edit `backend/config/db.php` **before uploading**:
```php
define('DB_HOST', 'localhost');      // usually localhost
define('DB_NAME', 'your_db_name');  // e.g. ever_bio or cpanelusername_ever_bio
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_pass');
```

### 3. Import the database
In phpMyAdmin → select your DB → **Import** → choose `backend/database.sql` → Go.

Then seed all 25 products:
```
https://yourdomain.com/backend/run_migrate.php
```
Delete `run_migrate.php` from server after running.

---

## Part D — Update config for live domain

Edit `backend/config/config.php` — the `BASE_URL` is auto-detected but verify:
```php
define('ALLOWED_ORIGINS', [
    'https://yourdomain.com',
    'https://www.yourdomain.com',
]);
```

Also set:
```php
define('DEBUG_MODE', false);   // already false — keep it off on live
```

---

## Part E — .htaccess check

The `dist/.htaccess` handles:
- SPA fallback (all routes → `index.html`)
- Passes `/backend/` and `/uploads/` to PHP directly
- Security headers + gzip + asset caching

**If you are on shared hosting and the site shows a blank page or 404 on page refresh**, make sure `.htaccess` was uploaded (many FTP clients hide dot-files by default).

---

## Part F — File permissions (Linux hosting)

```bash
chmod 755 public_html/uploads/
chmod 644 public_html/backend/config/db.php
chmod 644 public_html/backend/config/config.php
```

---

## Part G — Checklist before going live

- [ ] `npm run build:static` run with correct `VITE_BASE_PATH`
- [ ] `dist/` contents uploaded to `public_html/` (including `.htaccess`)
- [ ] `backend/` uploaded to `public_html/backend/`
- [ ] `admin/` uploaded to `public_html/admin/`
- [ ] `backend/config/db.php` updated with live DB credentials
- [ ] Database imported via phpMyAdmin
- [ ] `run_migrate.php` seeder run and then deleted
- [ ] `diag.php`, `seed_products.php` deleted from server
- [ ] Test: `https://yourdomain.com/` — homepage loads
- [ ] Test: `https://yourdomain.com/products` — direct URL works (SPA routing)
- [ ] Test: `https://yourdomain.com/backend/api/products.php?action=list` — returns JSON
- [ ] Test: `https://yourdomain.com/admin/login.php` — admin panel loads
- [ ] Change admin password from `Admin@1234` to something strong

---

## Part H — Fix 404 image errors on live server (one-time)

If hero images return 404 on the live site after deploying:

**Option 1 — Upload `dist/uploads/` (easiest)**  
Make sure you uploaded the `dist/uploads/` folder to `public_html/uploads/` — it contains all slide/product images.

**Option 2 — Run the sync script**  
Upload `backend/sync_assets.php` to `public_html/backend/` then visit:
```
https://yourdomain.com/backend/sync_assets.php?token=ev_sync_2025
```
The script will copy all images from `public_html/uploads/slides/` → `uploads/slides/`.
**Delete `sync_assets.php` from the server immediately after running.**

---

## Quick re-deploy (after code changes)

```powershell
# 1. Build
$env:VITE_BASE_PATH="/"; npm run build:static

# 2. Upload ONLY these via FTP/cPanel File Manager:
#    dist/assets/          ← new hashed JS/CSS files
#    dist/index.html       ← updated entry point
#    dist/uploads/         ← upload if you changed any images
#    src/**                ← NOT needed on server (source only)
```

That's it — the live site will match local exactly.

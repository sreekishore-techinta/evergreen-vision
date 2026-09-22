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
├── assets/             ← JS, CSS, images
├── .htaccess           ← SPA routing + backend passthrough
├── backend/            ← PHP API
│   ├── api/
│   └── config/
│       └── db.php      ← UPDATE credentials for live DB
├── admin/              ← PHP admin panel
└── uploads/            ← product images (writable 755)
```

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

## Quick re-deploy (after code changes)

```powershell
# 1. Build
$env:VITE_BASE_PATH="/"; npm run build:static

# 2. Upload ONLY these via FTP/cPanel File Manager:
#    dist/assets/          ← new hashed JS/CSS files
#    dist/index.html       ← updated entry point
#    src/**                ← NOT needed on server (source only)
```

That's it — the live site will match local exactly.

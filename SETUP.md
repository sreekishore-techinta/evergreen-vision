# Evergreen Industry — Setup Guide

## Prerequisites
- **XAMPP** (Apache + MySQL + PHP 8.1+)
- **Node.js** 18+ (for building the React frontend)

---

## 1. Database Setup

1. Start **XAMPP** → start **Apache** and **MySQL**
2. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
3. Click **Import** → choose `backend/database.sql` → click **Go**

Or via command line:
```bash
mysql -u root -p < backend/database.sql
```

This creates the `evergreen_db` database with all tables and seed data.

### Default Admin Login
| Field    | Value           |
|----------|-----------------|
| Username | `admin`         |
| Password | `Admin@1234`    |
| Email    | `admin@evergreenindustry.com` |

> **Change the password immediately after first login** via Admin → My Profile.

---

## 2. Configure Database Credentials

Edit `backend/config/db.php` if your MySQL credentials differ:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'evergreen_db');
define('DB_USER', 'root');     // ← your MySQL username
define('DB_PASS', '');         // ← your MySQL password
```

---

## 3. Place Files in XAMPP

The project should live at:
```
C:\xampp\htdocs\evergreen-vision\
```

If placed elsewhere, update `BASE_URL` in `backend/config/config.php`:
```php
define('BASE_URL', 'http://localhost/evergreen-vision');
```

---

## 4. Build the React Frontend

```bash
# Install dependencies (if not done)
npm install

# Build the static site → outputs to dist/
npm run build:static
```

The `dist/` folder is your deployable static site.

---

## 5. Access the Admin Panel

Navigate to:
```
http://localhost/evergreen-vision/admin/
```

Or directly:
```
http://localhost/evergreen-vision/admin/login.php
```

---

## 6. How It All Works Together

```
/evergreen-vision/
├── dist/               ← React static site (serve this for the frontend)
│   ├── index.html      ← SPA entry point
│   └── assets/         ← JS, CSS, images
│
├── admin/              ← PHP admin panel (XAMPP/Apache serves this)
│   ├── login.php       ← /admin → login
│   ├── dashboard.php   ← main dashboard
│   ├── enquiries.php   ← view/manage enquiries
│   ├── products.php    ← manage products
│   ├── settings.php    ← site settings
│   ├── profile.php     ← admin profile + password
│   └── users.php       ← manage admin users (superadmin only)
│
└── backend/
    ├── database.sql    ← DB schema — import once
    ├── config/
    │   ├── db.php      ← DB credentials
    │   ├── config.php  ← App settings
    │   └── helpers.php ← Shared functions
    └── api/
        ├── auth.php        ← POST login/logout
        ├── enquiries.php   ← POST submit (public) + admin CRUD
        ├── products.php    ← GET list (public) + admin CRUD
        ├── settings.php    ← GET public keys + admin save
        └── admin_users.php ← Admin user management
```

---

## 7. Contact Form Integration

The React contact form at `/contact` POSTs to:
```
/evergreen-vision/backend/api/enquiries.php?action=submit
```

This stores submissions in the `contact_enquiries` table. The admin panel notifies you with a badge on the **Enquiries** sidebar item for unread submissions.

---

## 8. API Endpoints Reference

### Public (no auth)
| Method | URL | Description |
|--------|-----|-------------|
| POST | `/backend/api/enquiries.php?action=submit` | Submit contact form |
| GET  | `/backend/api/products.php?action=list` | List active products |
| GET  | `/backend/api/settings.php?action=get&key=X` | Get public setting |

### Admin (session required)
| Method | URL | Description |
|--------|-----|-------------|
| POST | `/backend/api/auth.php?action=login` | Login |
| POST | `/backend/api/auth.php?action=logout` | Logout |
| GET  | `/backend/api/enquiries.php?action=list` | List enquiries |
| POST | `/backend/api/enquiries.php?action=update_status` | Update status |
| GET  | `/backend/api/products.php?action=list` | List all products |
| POST | `/backend/api/products.php?action=create` | Create product |
| POST | `/backend/api/products.php?action=update` | Update product |
| POST | `/backend/api/settings.php?action=update` | Save settings |

---

## 9. Production Checklist

- [ ] Change admin password from default
- [ ] Set `DEBUG_MODE = false` in `backend/config/config.php`
- [ ] Update `DB_PASS` with a strong MySQL password
- [ ] Update `BASE_URL` to your live domain
- [ ] Enable HTTPS and update `session.cookie_secure = 1`
- [ ] Add `ALLOWED_ORIGINS` with your live domain
- [ ] Move `backend/` outside of web root OR ensure `.htaccess` blocks direct config access

# Installation Guide — Vintage Newspaper CMS

## Prerequisites

- PHP 7.4+
- MySQL 5.7+
- XAMPP (or any Apache + PHP + MySQL stack)
- PHP extensions: `pdo`, `pdo_mysql`, `gd`, `mbstring`

---

## 1. Clone the Repository

```bash
git clone https://github.com/Mr-Fraz/vintage-newspaper.git
```

Place the folder in your web root:

- **Windows (XAMPP):** `C:\xampp\htdocs\vintage-newspaper`
- **Linux / macOS:** `/var/www/html/vintage-newspaper`

---

## 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` with your settings:

```env
DB_HOST=localhost
DB_NAME=vintage_newspaper
DB_USER=root
DB_PASS=

SITE_NAME=Vintage Newspaper
SITE_URL=http://localhost/vintage-newspaper

ADMIN_EMAIL=admin@gmail.com

JWT_SECRET=replace-with-a-random-secret
SESSION_SECRET=replace-with-another-random-secret
```

> ⚠️ `SITE_URL` — no trailing slash. Use `http://` — HTTPS is auto-detected at runtime.
> ⚠️ Never commit `.env` to version control.

---

## 3. Database Setup

### Option A — phpMyAdmin (Recommended for XAMPP)

1. Open `http://localhost/phpmyadmin`
2. Create database: `vintage_newspaper` — charset `utf8mb4`, collation `utf8mb4_unicode_ci`
3. Select the database → click **Import** tab
4. Import files **in this exact order**:

| Step | File | What it adds |
|---|---|---|
| 1 | `database/schema.sql` | Core tables: users, articles |
| 2 | `database/migration_001.sql` | Categories |
| 3 | `database/migration_002.sql` | Tags, revisions, activity log, password resets |
| 4 | `database/migration_003.sql` | Comments |
| 5 | `database/migration_004.sql` | Media library (thumb / medium variants) |
| 6 | `database/migration_005.sql` | FULLTEXT search indexes |
| 7 | `database/seed.sql` | Default admin account + sample articles |

### Option B — MySQL CLI

```bash
mysql -u root -p -e "CREATE DATABASE vintage_newspaper CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p vintage_newspaper < database/schema.sql
mysql -u root -p vintage_newspaper < database/migration_001.sql
mysql -u root -p vintage_newspaper < database/migration_002.sql
mysql -u root -p vintage_newspaper < database/migration_003.sql
mysql -u root -p vintage_newspaper < database/migration_004.sql
mysql -u root -p vintage_newspaper < database/migration_005.sql
mysql -u root -p vintage_newspaper < database/seed.sql
```

---

## 4. File Permissions

### Linux / macOS only

```bash
chmod 755 uploads/
chmod 755 uploads/articles/
```

### Windows (XAMPP)

No changes needed — XAMPP runs as local user with full access.

---

## 5. Access the Application

| URL | Description |
|---|---|
| `http://localhost/vintage-newspaper/` | Public frontend |
| `http://localhost/vintage-newspaper/admin/` | Admin panel |
| `http://localhost/vintage-newspaper/pages/about.php` | About page |
| `http://localhost/vintage-newspaper/pages/contact.php` | Contact page |

### Default Admin Credentials

```
Email:    admin@gmail.com
Password: admin123
```

> ⚠️ Change the default password immediately after first login.
> Admin → Users → click your account → change password.

---

## 6. User Roles

The system has 3 roles:

| Role | Permissions |
|---|---|
| `admin` | Full access — posts, categories, users, comments |
| `editor` | Create/edit posts, moderate comments — no user management |
| `user` | Public only — can comment when logged in |

To assign roles: Admin → Users → select user → change role.

---

## 7. Cron Jobs (Optional)

For scheduled publishing and token cleanup:

### Linux / macOS

```bash
# Auto-publish scheduled articles — every 5 minutes
*/5 * * * * php /path/to/vintage-newspaper/scripts/cron-publish.php

# Purge expired password reset tokens — daily at 3am
0 3 * * * php /path/to/vintage-newspaper/scripts/cron-cleanup.php
```

### Windows (XAMPP)

Use **Windows Task Scheduler** to run these PHP scripts on a schedule.

---

## 8. Shared Hosting Deployment (e.g. InfinityFree, cPanel)

1. Upload all files to `public_html/` via FTP or File Manager
2. Create MySQL database in cPanel → **MySQL Databases**
3. Import SQL files via phpMyAdmin (same order as Step 3)
4. Edit `.env` — set `SITE_URL` to your live domain:
   ```env
   SITE_URL=https://yourdomain.com
   ```
5. Ensure `uploads/articles/` folder exists and is writable

> 💡 `SITE_URL` can use `http://` even on HTTPS hosting — the app auto-upgrades URLs.

---

## Verify Installation Checklist

- [ ] Homepage loads at `SITE_URL`
- [ ] Admin login works at `SITE_URL/admin/`
- [ ] Can create and publish an article
- [ ] Article image uploads and displays with grayscale filter
- [ ] Urdu article auto-detects RTL direction
- [ ] Search returns results (confirms migration_005 FULLTEXT applied)
- [ ] Comments appear after approval in admin
- [ ] About and Contact pages load with matching theme
- [ ] Mobile layout works (test with Chrome DevTools → F12 → Ctrl+Shift+M)

---

## Troubleshooting

| Problem | Fix |
|---|---|
| Blank page or PHP errors | Enable `display_errors = On` in `php.ini` |
| DB connection failed | Check `.env` credentials; ensure MySQL is running |
| `require_once` path error | Ensure you're using the latest files from the repo |
| CSS not loading on HTTPS | App auto-detects — hard refresh (Ctrl+Shift+R) |
| 404 on all pages | No trailing slash in `SITE_URL`; Apache mod_rewrite enabled |
| Image not displaying | Check `uploads/articles/` is writable; verify `SITE_URL` |
| TinyMCE license warning | Must use TinyMCE 6 (self-hosted) — not v7 |
| Search returns nothing | Confirm `migration_005.sql` was imported |
| Comment not appearing | Status is `pending` by default — approve in Admin → Comments |
| Scheduled posts not publishing | Set up `cron-publish.php` as a cron job |
| Cookies error on testing tools | Use real browser (Chrome DevTools device mode) instead |
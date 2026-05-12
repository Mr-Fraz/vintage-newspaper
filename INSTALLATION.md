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
- **Linux/macOS:** `/var/www/html/vintage-newspaper`

---

## 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env`:

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

> ⚠️ Never commit `.env` to version control.

---

## 3. Database Setup

### Option A — phpMyAdmin

1. Open `http://localhost/phpmyadmin`
2. Create database: `vintage_newspaper` (charset: `utf8mb4`)
3. Import files **in this exact order** via the **Import** tab:

| Order | File |
|---|---|
| 1 | `database/schema.sql` |
| 2 | `database/migration_001.sql` |
| 3 | `database/migration_002.sql` |
| 4 | `database/migration_003.sql` |
| 5 | `database/migration_004.sql` |
| 6 | `database/migration_005.sql` |
| 7 | `database/seed.sql` |

### Option B — CLI

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

## 4. File Permissions (Linux/macOS only)

```bash
chmod 755 uploads/
chmod 755 uploads/articles/
```

---

## 5. Access the Application

| URL | Description |
|---|---|
| `http://localhost/vintage-newspaper/` | Public frontend |
| `http://localhost/vintage-newspaper/admin/` | Admin panel |

**Default admin credentials:**

```
Email:    admin@gmail.com
Password: admin123
```

> ⚠️ Change the default password immediately after first login.

---

## 6. Cron Jobs (Optional)

For scheduled publishing and token cleanup, set up cron jobs:

```bash
# Auto-publish scheduled articles — every 5 minutes
*/5 * * * * php /path/to/vintage-newspaper/scripts/cron-publish.php

# Purge expired password reset tokens — daily
0 3 * * * php /path/to/vintage-newspaper/scripts/cron-cleanup.php
```

On Windows (XAMPP), use Windows Task Scheduler to run these scripts.

---

## Verify Installation

- [ ] Homepage loads at `SITE_URL`
- [ ] Admin login works at `SITE_URL/admin/`
- [ ] Can create and publish an article
- [ ] Image upload works (check `uploads/articles/` is writable)
- [ ] Search returns results (confirms migration_005 FULLTEXT index applied)

---

## Troubleshooting

| Problem | Fix |
|---|---|
| Blank page or PHP errors | Enable `display_errors` in `php.ini` during dev |
| DB connection failed | Verify `.env` credentials; ensure MySQL is running |
| `uploads/` permission denied | `chmod 755 uploads/articles/` |
| 404 on all pages | Check `SITE_URL` has no trailing slash; Apache mod_rewrite enabled |
| Search returns nothing | Confirm migration_005.sql was imported |
| TinyMCE license warning | Must use TinyMCE 6 — not v7 |
# 📰 Vintage Newspaper CMS

A full-featured, vintage-themed newspaper Content Management System built with **PHP**, **PDO**, and **MySQL**. Designed with a retro aesthetic and powered by modern web security practices.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![TinyMCE](https://img.shields.io/badge/TinyMCE-6.x-green?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-yellow?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-brightgreen?style=flat-square)

---

## 📌 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

---

## Overview

Vintage Newspaper CMS is a university major project — a fully functional content management system styled after classic print newspapers. It supports multi-role user management, rich-text article authoring, comment moderation, SEO metadata, scheduled publishing, tag management, and a REST API.

---

## Features

### Content Management
| Feature | Status |
|---|---|
| Article CRUD (Create, Read, Update, Delete) | ✅ |
| TinyMCE 6 Rich Text Editor | ✅ |
| Featured Image Upload | ✅ |
| Article Revision History | ✅ |
| Scheduled Publishing (`publish_at`) | ✅ |
| Article Status (`draft`, `pending`, `scheduled`, `published`, `archived`) | ✅ |
| Category Management | ✅ |
| Tag System (comma-separated, many-to-many) | ✅ |
| SEO Title & Meta Description per Article | ✅ |
| Full-text Article Search | ✅ |
| Pagination | ✅ |

### User & Auth System
| Feature | Status |
|---|---|
| User Registration & Login | ✅ |
| Role-based Access Control (`admin`, `editor`, `user`) | ✅ |
| Password Hashing (bcrypt) | ✅ |
| Password Reset via Token | ✅ |
| Session Management | ✅ |
| CSRF Protection on all Forms | ✅ |

### Comment System
| Feature | Status |
|---|---|
| Guest & Registered User Comments | ✅ |
| Comment Moderation (`pending`, `approved`, `spam`) | ✅ |
| Admin Approve / Spam / Delete Actions | ✅ |

### Admin Panel
| Feature | Status |
|---|---|
| Dashboard with Stats | ✅ |
| Post Management | ✅ |
| Category Management | ✅ |
| User Management | ✅ |
| Comment Moderation | ✅ |
| Activity Log | ✅ |

### API
| Feature | Status |
|---|---|
| Articles REST API (paginated) | ✅ |
| Search API | ✅ |
| File Upload API | ✅ |
| Login API | ✅ |
| Password Reset API | ✅ |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 7.4+ (procedural + OOP with static DB class) |
| Database | MySQL 5.7+ via PDO with prepared statements |
| Rich Text Editor | TinyMCE 6 (self-hosted, no API key required) |
| Frontend | Vanilla HTML/CSS/JS — vintage newspaper theme |
| Local Dev | XAMPP (Apache + PHP + MySQL) |
| Auth | PHP Sessions + bcrypt password hashing |

---

## Project Structure

```
vintage-newspaper/
├── admin/                        # Admin panel
│   ├── categories/               # Category CRUD
│   ├── comments/                 # Comment moderation
│   │   └── list.php
│   ├── posts/                    # Article CRUD
│   │   ├── add.php
│   │   ├── edit.php
│   │   ├── list.php
│   │   └── delete.php
│   ├── users/                    # User management
│   ├── includes/                 # Admin layout partials
│   │   ├── admin-header.php
│   │   ├── admin-footer.php
│   │   ├── sidebar.php
│   │   └── auth-check.php
│   ├── index.php                 # Admin dashboard
│   └── logout.php
│
├── api/                          # REST API endpoints
│   ├── articles.php              # GET articles (paginated)
│   ├── search.php                # GET search results
│   ├── upload.php                # POST image upload
│   ├── login.php                 # POST login
│   ├── password-reset.php        # POST request reset
│   └── password-reset-verify.php # POST verify token
│
├── assets/
│   ├── css/
│   │   ├── style.css             # Main frontend styles
│   │   ├── admin.css             # Admin panel styles
│   │   └── vintage.css           # Vintage theme overrides
│   └── js/
│       ├── main.js
│       ├── admin.js
│       ├── search.js
│       └── tinymce/              # Self-hosted TinyMCE 6
│
├── config/
│   ├── config.php                # App constants & session init
│   ├── database.php              # PDO singleton connection
│   └── env.php                   # .env file loader
│
├── database/
│   ├── schema.sql                # Initial DB schema
│   ├── migration_001.sql         # Categories + article enhancements
│   ├── migration_002.sql         # Revisions, tags, activity log, password resets
│   ├── migration_003.sql         # Comments table
│   └── seed.sql                  # Sample data / default admin user
│
├── functions/
│   ├── auth.php                  # Auth helpers (requireAdmin, etc.)
│   ├── db.php                    # DB static class (all queries)
│   ├── helpers.php               # formatDate, uploadImage, etc.
│   └── validation.php            # sanitize, slug, CSRF helpers
│
├── includes/                     # Frontend layout partials
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   ├── init.php
│   └── auth-middleware.php
│
├── pages/                        # Public-facing pages
│   ├── article.php               # Single article + comments
│   ├── category.php              # Category article listing
│   ├── search.php                # Search results
│   ├── login.php
│   ├── register.php
│   ├── about.php
│   └── contact.php
│
├── scripts/
│   ├── cron-cleanup.php          # Cleanup expired tokens
│   └── cron-publish.php          # Auto-publish scheduled posts
│
├── uploads/
│   └── articles/                 # Uploaded article images
│
├── .env                          # Environment variables (do NOT commit)
├── .env.example                  # Environment template
├── .gitignore
├── index.php                     # Homepage entry point
├── INSTALLATION.md
└── README.md
```

---

## Installation

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- XAMPP (or any Apache + PHP + MySQL stack)
- GD Library enabled in `php.ini`
- PDO + PDO_MySQL extensions enabled

### 1. Clone the Repository

```bash
git clone https://github.com/Mr-Fraz/vintage-newspaper.git
cd vintage-newspaper
```

Place the folder inside `C:\xampp\htdocs\` (Windows) or `/var/www/html/` (Linux).

### 2. Configure Environment

Copy the example env file:

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

JWT_SECRET=change-this-to-a-random-string
SESSION_SECRET=another-random-string
```

### 3. Database Setup

Open **phpMyAdmin** → create database `vintage_newspaper` → then import in order:

| Step | File | Purpose |
|---|---|---|
| 1 | `database/schema.sql` | Core tables (users, articles) |
| 2 | `database/migration_001.sql` | Categories |
| 3 | `database/migration_002.sql` | Tags, revisions, activity log, password resets |
| 4 | `database/migration_003.sql` | Comments |
| 5 | `database/seed.sql` | Default admin account |

Or via CLI:

```bash
mysql -u root -p vintage_newspaper < database/schema.sql
mysql -u root -p vintage_newspaper < database/migration_001.sql
mysql -u root -p vintage_newspaper < database/migration_002.sql
mysql -u root -p vintage_newspaper < database/migration_003.sql
mysql -u root -p vintage_newspaper < database/seed.sql
```

### 4. File Permissions (Linux/Mac only)

```bash
chmod 755 uploads/
chmod 755 uploads/articles/
```

### 5. Access the Application

| URL | Description |
|---|---|
| `http://localhost/vintage-newspaper/` | Public frontend |
| `http://localhost/vintage-newspaper/admin/` | Admin panel |

**Default Admin Credentials:**

```
Email:    admin@gmail.com
Password: admin123
```

> ⚠️ Change the default password immediately after first login.

---

## Database Setup

### Tables Overview

| Table | Description |
|---|---|
| `users` | Registered users with roles |
| `articles` | Published content with SEO & scheduling fields |
| `categories` | Article categories |
| `tags` | Tag definitions |
| `article_tag` | Many-to-many articles ↔ tags |
| `comments` | Guest & user comments with moderation status |
| `article_revisions` | Auto-saved revision history per article |
| `activity_log` | Admin action audit trail |
| `password_resets` | Secure token-based password reset |

---

## Configuration

### Environment Variables

| Variable | Description | Default |
|---|---|---|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `vintage_newspaper` |
| `DB_USER` | Database username | `root` |
| `DB_PASS` | Database password | _(empty)_ |
| `SITE_NAME` | Display name of site | `Vintage Newspaper` |
| `SITE_URL` | Full base URL (no trailing slash) | `http://localhost/vintage-newspaper` |
| `ADMIN_EMAIL` | Admin contact email | — |
| `JWT_SECRET` | Secret for API tokens | _(must set)_ |

### Upload Settings (`config/config.php`)

```php
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/articles/');
```

---

## Usage Guide

### Writing an Article

1. Login → **Admin Panel**
2. Go to **Posts → Add New Post**
3. Fill title, select category, write content in **TinyMCE rich text editor**
4. Add tags (comma-separated), excerpt, SEO fields (optional)
5. Upload featured image
6. Set status: `draft` / `published` / `scheduled`
7. For scheduled: set **Publish At** datetime → `cron-publish.php` handles auto-publish
8. Click **Create Post**

### Moderating Comments

1. Go to **Admin → Comments**
2. Review pending comments
3. Click **Approve** to publish, **Spam** to mark, or **Delete** to remove

### Managing Users

1. Go to **Admin → Users**
2. Change roles: `user` → `editor` → `admin`
3. Delete accounts as needed (cannot delete your own)

---

## API Documentation

### GET Articles

```
GET /api/articles.php?page=1&limit=10
```

**Response:**
```json
{
  "success": true,
  "page": 1,
  "limit": 10,
  "total": 42,
  "articles": [
    {
      "id": 1,
      "title": "Article Title",
      "slug": "article-title",
      "excerpt": "Short summary...",
      "category_name": "Politics",
      "author": "Admin",
      "status": "published",
      "created_at": "2025-01-01 12:00:00"
    }
  ]
}
```

### GET Search

```
GET /api/search.php?q=keyword&page=1
```

### POST Upload Image

```
POST /api/upload.php
Content-Type: multipart/form-data

Body: image=[file]
```
Requires active session + CSRF token.

### POST Login

```
POST /api/login.php
Content-Type: application/json

{ "email": "admin@gmail.com", "password": "admin123" }
```

### POST Password Reset

```
POST /api/password-reset.php
Content-Type: application/json

{ "email": "user@example.com" }
```

---

## Security

| Measure | Implementation |
|---|---|
| SQL Injection | PDO prepared statements throughout |
| XSS | `htmlspecialchars()` on all output |
| CSRF | Token field on every POST form, verified server-side |
| Password Storage | `password_hash()` with bcrypt |
| File Uploads | MIME type check + extension whitelist + random filename |
| Session Security | `session_start()` with regeneration on login |
| Auth Guards | `Auth::requireAdmin()` on all admin pages |
| Security Headers | X-Frame-Options, X-Content-Type-Options, X-XSS-Protection |

### Best Practices

- Never commit `.env` to version control (already in `.gitignore`)
- Use a strong `JWT_SECRET` in production
- Enable HTTPS in production and set `SESSION_COOKIE_SECURE`
- Run `cron-cleanup.php` periodically to purge expired password reset tokens
- Backup database regularly

---

## Troubleshooting

| Problem | Fix |
|---|---|
| `Call to member function prepare() on null` | Missing `self::init()` in DB method, or `.env` credentials wrong |
| TinyMCE shows plain HTML | Check `</textarea>` has no spaces; ensure TinyMCE script loaded before `tinymce.init()` |
| TinyMCE license warning | Use TinyMCE 6 (self-hosted, free) — not v7 |
| Image not displaying | Check `uploads/articles/` is writable; verify `SITE_URL` in `.env` |
| 404 on pages | Ensure `SITE_URL` has no trailing slash; verify Apache is running |
| DB connection error | Check MySQL service running; verify `.env` DB credentials |
| Comment not appearing | Status is `pending` by default — approve in Admin → Comments |

---

## Contributing

1. Fork the repo
2. Create a branch: `git checkout -b feature/your-feature`
3. Commit: `git commit -m "Add: your feature description"`
4. Push: `git push origin feature/your-feature`
5. Open a Pull Request

---

## License

This project is licensed under the **MIT License**.

---

## Acknowledgements

- [TinyMCE](https://www.tiny.cloud/) — Rich text editor
- PHP PDO documentation
- Vintage newspaper typography inspiration

---

> **University Major Project** — Software Engineering & Computer Science  
> Built with PHP · MySQL · Vanilla JS · TinyMCE 6

# 📰 Vintage Newspaper CMS

A full-featured, vintage-themed newspaper CMS built with **PHP**, **PDO**, and **MySQL**. Retro aesthetic, modern security practices.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![TinyMCE](https://img.shields.io/badge/TinyMCE-6.x-green?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-yellow?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-brightgreen?style=flat-square)

---

## Table of Contents

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

Vintage Newspaper CMS is a fully functional content management system styled after classic print newspapers. Supports multi-role user management, rich-text article authoring, media library, comment moderation, SEO metadata, scheduled publishing, dark mode, and a REST API.

---

## Features

### Content Management
| Feature | Status |
|---|---|
| Article CRUD | ✅ |
| TinyMCE 6 Rich Text Editor (self-hosted) | ✅ |
| Media Library with image variants (thumb / medium / full) | ✅ |
| Featured Image Upload with alt-text | ✅ |
| Article Revision History | ✅ |
| Scheduled Publishing (`publish_at`) | ✅ |
| Article Status (`draft`, `pending`, `scheduled`, `published`, `archived`) | ✅ |
| Category Management | ✅ |
| Tag System (many-to-many) | ✅ |
| SEO Title & Meta Description per Article | ✅ |
| Full-text Search (FULLTEXT index) | ✅ |
| Pagination | ✅ |

### User & Auth System
| Feature | Status |
|---|---|
| Registration & Login | ✅ |
| Role-based Access Control (`admin`, `editor`, `user`) | ✅ |
| Password Hashing (bcrypt) | ✅ |
| Token-based Password Reset | ✅ |
| Session Management | ✅ |
| CSRF Protection | ✅ |

### Comment System
| Feature | Status |
|---|---|
| Guest & Registered User Comments | ✅ |
| Comment Moderation (`pending`, `approved`, `spam`) | ✅ |
| Admin Approve / Spam / Delete | ✅ |

### Admin Panel
| Feature | Status |
|---|---|
| Dashboard with Stats | ✅ |
| Post Management | ✅ |
| Category Management | ✅ |
| User Management | ✅ |
| Comment Moderation | ✅ |
| Activity Log | ✅ |

### UI
| Feature | Status |
|---|---|
| Dark Mode (warm charcoal / amber) | ✅ |
| Vintage Newspaper Theme | ✅ |
| Responsive Layout | ✅ |

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
| Backend | PHP 7.4+ (procedural + static DB class) |
| Database | MySQL 5.7+ via PDO with prepared statements |
| Rich Text Editor | TinyMCE 6 (self-hosted, no API key required) |
| Frontend | Vanilla HTML/CSS/JS — vintage newspaper theme |
| Local Dev | XAMPP (Apache + PHP + MySQL) |
| Auth | PHP Sessions + bcrypt |

---

## Project Structure

```
vintage-newspaper/
├── admin/                        # Admin panel
│   ├── categories/               # Category CRUD
│   ├── comments/                 # Comment moderation
│   ├── posts/                    # Article CRUD
│   ├── users/                    # User management
│   ├── includes/                 # Admin layout partials
│   ├── index.php                 # Dashboard
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
│   │   ├── vintage.css           # Vintage theme overrides
│   │   └── dark-mode.css         # Dark mode (warm charcoal/amber)
│   └── js/
│       ├── main.js
│       ├── admin.js
│       ├── search.js
│       ├── dark-mode.js          # Dark mode toggle
│       └── tinymce/              # Self-hosted TinyMCE 6
│
├── config/
│   ├── config.php                # App constants & session init
│   ├── database.php              # PDO singleton connection
│   └── env.php                   # .env loader
│
├── database/
│   ├── schema.sql                # Core tables (users, articles)
│   ├── migration_001.sql         # Categories + article enhancements
│   ├── migration_002.sql         # Revisions, tags, activity log, password resets
│   ├── migration_003.sql         # Comments table
│   ├── migration_004.sql         # Media library (variants + alt-text)
│   ├── migration_005.sql         # Performance indexes + FULLTEXT search
│   └── seed.sql                  # Default admin account + sample data
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
│   ├── category.php              # Category listing
│   ├── search.php                # Search results
│   ├── login.php
│   ├── register.php
│   ├── about.php
│   └── contact.php
│
├── scripts/
│   ├── cron-cleanup.php          # Purge expired tokens
│   └── cron-publish.php          # Auto-publish scheduled posts
│
├── uploads/articles/             # Uploaded article images
├── .env                          # Environment variables (do NOT commit)
├── .env.example                  # Environment template
├── .gitignore
├── index.php                     # Homepage entry point
├── INSTALLATION.md
└── README.md
```

---

## Installation

See [INSTALLATION.md](INSTALLATION.md) for full setup instructions.

### Quick Start (XAMPP)

```bash
git clone https://github.com/Mr-Fraz/vintage-newspaper.git
cd vintage-newspaper
cp .env.example .env
# Edit .env with your DB credentials
# Import database files in order (schema → migrations 001–005 → seed)
```

---

## Database Setup

Import files in this exact order:

| Step | File | Purpose |
|---|---|---|
| 1 | `database/schema.sql` | Core tables (users, articles) |
| 2 | `database/migration_001.sql` | Categories |
| 3 | `database/migration_002.sql` | Tags, revisions, activity log, password resets |
| 4 | `database/migration_003.sql` | Comments |
| 5 | `database/migration_004.sql` | Media library (thumb/medium variants, alt-text) |
| 6 | `database/migration_005.sql` | Performance indexes + FULLTEXT search |
| 7 | `database/seed.sql` | Default admin account |

### Tables Overview

| Table | Description |
|---|---|
| `users` | Registered users with roles |
| `articles` | Content with SEO, scheduling, media FK |
| `categories` | Article categories |
| `tags` + `article_tag` | Many-to-many tag system |
| `media` | Media library (filename, thumb, medium, alt-text) |
| `comments` | Guest & user comments with moderation status |
| `article_revisions` | Auto-saved revision history |
| `activity_log` | Admin action audit trail |
| `password_resets` | Token-based password reset |

---

## Configuration

### Environment Variables (`.env`)

| Variable | Description | Default |
|---|---|---|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `vintage_newspaper` |
| `DB_USER` | Database username | `root` |
| `DB_PASS` | Database password | _(empty)_ |
| `SITE_NAME` | Site display name | `Vintage Newspaper` |
| `SITE_URL` | Full base URL (no trailing slash) | `http://localhost/vintage-newspaper` |
| `ADMIN_EMAIL` | Admin contact email | — |
| `JWT_SECRET` | Secret for API tokens | _(must set)_ |
| `SESSION_SECRET` | Session secret | _(must set)_ |

### Upload Settings (`config/config.php`)

```php
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/articles/');
```

---

## Usage Guide

### Writing an Article

1. Admin Panel → **Posts → Add New Post**
2. Fill title, category, content (TinyMCE)
3. Add tags (comma-separated), excerpt, SEO fields
4. Select or upload featured image from Media Library
5. Set status: `draft` / `published` / `scheduled`
6. For scheduled: set **Publish At** datetime → `cron-publish.php` auto-publishes
7. **Create Post**

### Moderating Comments

Admin → **Comments** → Approve / Spam / Delete

### Managing Users

Admin → **Users** → assign roles (`user` / `editor` / `admin`) or delete

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
| CSRF | Token on every POST form, verified server-side |
| Password Storage | `password_hash()` bcrypt |
| File Uploads | MIME check + extension whitelist + random filename |
| Session Security | Regenerated on login |
| Auth Guards | `Auth::requireAdmin()` on all admin pages |
| Security Headers | X-Frame-Options, X-Content-Type-Options, X-XSS-Protection |

**Production checklist:**
- Change default admin password immediately
- Set strong `JWT_SECRET` and `SESSION_SECRET` in `.env`
- Enable HTTPS and set `SESSION_COOKIE_SECURE`
- Schedule `cron-cleanup.php` to purge expired tokens
- Never commit `.env` (already in `.gitignore`)

---

## Troubleshooting

| Problem | Fix |
|---|---|
| `prepare() on null` | Wrong `.env` DB credentials, or `self::init()` missing |
| TinyMCE shows plain HTML | Ensure TinyMCE script loads before `tinymce.init()`; no spaces before `</textarea>` |
| TinyMCE license warning | Must use TinyMCE 6 (self-hosted) — not v7 |
| Image not displaying | Check `uploads/articles/` is writable; verify `SITE_URL` in `.env` |
| 404 on pages | No trailing slash in `SITE_URL`; verify Apache running |
| DB connection error | Check MySQL service; verify `.env` credentials |
| Comment not appearing | Status is `pending` by default — approve in Admin → Comments |
| Search returns no results | migration_005.sql not imported (FULLTEXT index missing) |

---

## Contributing

1. Fork the repo
2. `git checkout -b feature/your-feature`
3. `git commit -m "Add: your feature"`
4. `git push origin feature/your-feature`
5. Open a Pull Request

---

## License

MIT License

---

## Acknowledgements

- [TinyMCE](https://www.tiny.cloud/) — Rich text editor
- Vintage newspaper typography inspiration

---

> **University Major Project** — Software Engineering & Computer Science  
> Built with PHP · MySQL · Vanilla JS · TinyMCE 6
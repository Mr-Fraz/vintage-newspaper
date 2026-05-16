# 📰 Vintage Newspaper CMS

A full-featured, vintage-themed newspaper CMS built with **PHP**, **PDO**, and **MySQL**. 19th-century aesthetic, modern security practices, fully responsive on all devices.

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
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
- [Roles & Permissions](#roles--permissions)
- [API Documentation](#api-documentation)
- [Security](#security)
- [Troubleshooting](#troubleshooting)

---

## Overview

Vintage Newspaper CMS is a fully functional content management system styled after classic Victorian-era print newspapers. It supports multi-role user management, rich-text article authoring with Urdu (RTL) support, media library, comment moderation, SEO metadata, scheduled publishing, dark mode, and a REST API.

Live Demo: [vintagenews.infinityfreeapp.com](https://vintagenews.infinityfreeapp.com)

---

## Features

### Content Management
| Feature | Status |
|---|---|
| Article CRUD | ✅ |
| TinyMCE 6 Rich Text Editor (self-hosted) | ✅ |
| Urdu / RTL article support (auto-detected) | ✅ |
| Media Library with image variants (thumb / medium / full) | ✅ |
| Featured Image with grayscale vintage filter | ✅ |
| Scheduled Publishing (`publish_at`) | ✅ |
| Article Status (`draft`, `pending`, `published`, `archived`) | ✅ |
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
| CSRF Protection | ✅ |

### Comment System
| Feature | Status |
|---|---|
| Guest & Registered User Comments | ✅ |
| Name-only (no email required) | ✅ |
| Comment Moderation (`pending`, `approved`, `spam`) | ✅ |

### Admin Panel
| Feature | Status |
|---|---|
| Dashboard with Stats | ✅ |
| Post Management | ✅ |
| Category Management | ✅ |
| User Management + Role Assignment | ✅ |
| Comment Moderation | ✅ |
| Mobile-responsive hamburger sidebar | ✅ |

### UI / Theme
| Feature | Status |
|---|---|
| Victorian Newspaper Theme (parchment, Cinzel, Playfair Display) | ✅ |
| Dark Mode (warm charcoal / amber) | ✅ |
| Fully Responsive (mobile, tablet, desktop) | ✅ |
| 4-column grid on desktop, single column on mobile | ✅ |
| Compact mobile footer (2-column) | ✅ |
| About & Contact pages matching site theme | ✅ |

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
| Backend | PHP 7.4+ (static DB class + helpers) |
| Database | MySQL 5.7+ via PDO with prepared statements |
| Rich Text Editor | TinyMCE 6 (self-hosted, no API key required) |
| Mailer | PHPMailer (password reset emails) |
| Frontend | Vanilla HTML / CSS / JS — Victorian newspaper theme |
| Fonts | Cinzel, Playfair Display, Libre Baskerville, Gulzar (Urdu) |
| Local Dev | XAMPP (Apache + PHP + MySQL) |
| Auth | PHP Sessions + bcrypt |

---

## Project Structure

```
vintage-newspaper/
├── admin/                        # Admin panel
│   ├── categories/               # Category CRUD
│   ├── comments/                 # Comment moderation
│   ├── posts/                    # Article CRUD (add, edit, list, delete)
│   ├── users/                    # User management
│   ├── includes/                 # Admin layout (header, sidebar, footer)
│   ├── index.php                 # Dashboard
│   └── logout.php
│
├── api/                          # REST API endpoints
│   ├── articles.php
│   ├── search.php
│   ├── upload.php
│   ├── login.php
│   ├── password-reset.php
│   └── password-reset-verify.php
│
├── assets/
│   ├── css/
│   │   ├── style.css             # Main frontend styles + responsive
│   │   ├── admin.css             # Admin panel styles
│   │   ├── pages.css             # About & Contact page styles
│   │   └── vintage.css           # Theme overrides
│   └── js/
│       ├── main.js               # Navbar, hamburger, dark mode
│       ├── admin.js
│       ├── animation.js
│       ├── search.js
│       └── tinymce/              # Self-hosted TinyMCE 6
│
├── config/
│   ├── config.php                # App constants, HTTPS auto-detect
│   ├── database.php              # PDO singleton
│   └── env.php                   # .env loader
│
├── database/
│   ├── schema.sql                # Core tables
│   ├── migration_001.sql         # Categories
│   ├── migration_002.sql         # Tags, revisions, activity log, password resets
│   ├── migration_003.sql         # Comments
│   ├── migration_004.sql         # Media library
│   ├── migration_005.sql         # FULLTEXT indexes
│   └── seed.sql                  # Default admin account + sample data
│
├── functions/
│   ├── auth.php                  # requireAdmin(), requireEditor(), isEditor()
│   ├── db.php                    # All DB queries
│   ├── helpers.php               # formatDate, uploadImage, etc.
│   └── validation.php            # sanitize, slug, CSRF
│
├── includes/                     # Frontend layout partials
│   ├── header.php
│   ├── navbar.php
│   └── footer.php                # Desktop 3-section + mobile 2-column
│
├── libs/
│   └── PHPMailer/                # Email library
│
├── pages/                        # Public pages
│   ├── article.php               # Article + comments
│   ├── category.php
│   ├── search.php
│   ├── login.php
│   ├── register.php
│   ├── about.php                 # Victorian-themed about page
│   ├── contact.php               # Victorian-themed contact page
│   ├── forgot-password.php
│   └── password-reset.php
│
├── scripts/
│   ├── cron-publish.php          # Auto-publish scheduled articles
│   └── cron-cleanup.php          # Purge expired tokens
│
├── uploads/articles/             # Uploaded images
├── .env                          # Environment config (do NOT commit)
├── .env.example                  # Template
├── .gitignore
├── index.php                     # Homepage
├── INSTALLATION.md
└── README.md
```

---

## Installation

See [INSTALLATION.md](INSTALLATION.md) for full step-by-step instructions.

### Quick Start

```bash
git clone https://github.com/Mr-Fraz/vintage-newspaper.git
cd vintage-newspaper
cp .env.example .env
# Edit .env — set DB credentials and SITE_URL
# Import database files in order (schema → migrations 001–005 → seed)
# Open http://localhost/vintage-newspaper/
```

---

## Configuration

### `.env` Variables

| Variable | Description | Example |
|---|---|---|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `vintage_newspaper` |
| `DB_USER` | Database username | `root` |
| `DB_PASS` | Database password | _(empty for XAMPP)_ |
| `SITE_NAME` | Site display name | `Vintage Newspaper` |
| `SITE_URL` | Base URL — no trailing slash | `http://localhost/vintage-newspaper` |
| `ADMIN_EMAIL` | Admin email | `admin@gmail.com` |
| `JWT_SECRET` | API token secret | _(random string)_ |
| `SESSION_SECRET` | Session secret | _(random string)_ |

> `SITE_URL` can use `http://` — the app auto-detects HTTPS and upgrades asset URLs automatically.

---

## Usage Guide

### Writing an Article

1. Admin → **Posts → + New Post**
2. Title, category, content (TinyMCE editor)
3. Tags (comma-separated), excerpt, SEO fields
4. Upload or select featured image from Media Library
5. Set status: `draft` / `published` / `scheduled`
6. Click **Publish**

> Urdu titles are auto-detected — editor switches to RTL automatically.

### Moderating Comments

Admin → **Comments** → Approve / Spam / Delete

### Managing Users

Admin → **Users** → assign roles or delete accounts

---

## Roles & Permissions

| Role | Access |
|---|---|
| `admin` | Full access — posts, categories, users, comments |
| `editor` | Can create and edit posts, moderate comments — cannot manage users |
| `user` | Public only — can post comments when logged in |

To assign a role: Admin → Users → click user → change role dropdown.

---

## API Documentation

### GET Articles
```
GET /api/articles.php?page=1&limit=10
```

### GET Search
```
GET /api/search.php?q=keyword&page=1
```

### POST Upload Image
```
POST /api/upload.php
Content-Type: multipart/form-data
Body: image=[file], csrf_token=[token]
```
Requires active session.

### POST Login
```
POST /api/login.php
Content-Type: application/json

{ "email": "admin@gmail.com", "password": "admin123" }
```

### POST Password Reset Request
```
POST /api/password-reset.php
Content-Type: application/json

{ "email": "user@example.com" }
```

---

## Security

| Measure | Implementation |
|---|---|
| SQL Injection | PDO prepared statements |
| XSS | `htmlspecialchars()` on all output |
| CSRF | Token on every POST, verified server-side |
| Passwords | `password_hash()` bcrypt |
| File Uploads | MIME check + extension whitelist + random filename |
| HTTPS | Auto-detected — asset URLs upgrade automatically |
| Auth Guards | `Auth::requireAdmin()` / `Auth::requireEditor()` |

**Production checklist:**
- Change default admin password immediately
- Set strong `JWT_SECRET` and `SESSION_SECRET`
- Enable HTTPS — `SITE_URL` will auto-upgrade
- Schedule cron jobs for publishing and token cleanup
- Never commit `.env`

---

## Troubleshooting

| Problem | Fix |
|---|---|
| Blank page / PHP errors | Enable `display_errors` in `php.ini` |
| DB connection failed | Check `.env` credentials; ensure MySQL running |
| CSS not loading on HTTPS | App auto-detects protocol — clear cache and retry |
| 404 on pages | No trailing slash in `SITE_URL`; Apache running |
| Image not showing | Check `uploads/articles/` is writable; verify `SITE_URL` |
| TinyMCE license warning | Must use TinyMCE 6 (self-hosted) — not v7 |
| Search returns nothing | Import `migration_005.sql` (FULLTEXT index) |
| Comment not visible | Status is `pending` — approve in Admin → Comments |
| Scheduled posts not publishing | Set up `cron-publish.php` cron job |

---

## License

MIT License

---

## Acknowledgements

- [TinyMCE](https://www.tiny.cloud/) — Rich text editor
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) — Email handling
- Victorian newspaper typography & design inspiration

---

> **University Major Project** — Software Engineering & Computer Science
> Built with PHP · MySQL · Vanilla JS · TinyMCE 6 · PHPMailer
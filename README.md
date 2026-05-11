## Overview

Vintage Newspaper is a lightweight CMS with a retro design, built on PHP + PDO and MySQL. The repository currently contains Phase 1 functionality: DB migrations for revisions/tags, hardened uploads, JWT login + password-reset endpoints, cron scripts for scheduled publishing and cleanup, and admin UI updates (tags, SEO fields, revisions support).

## Current Highlights

- **Authentication & API:** login with JWT generation (`api/login.php`) and password reset endpoints (`api/password-reset.php`, `api/password-reset-verify.php`).
- **Articles:** full CRUD with support for `tags`, `article_revisions`, `status`, `publish_at`, `seo_title`, `meta_description`, and `og_image`.
- **Upload hardening:** `api/upload.php` validates MIME types, strips EXIF, stores uploads under `uploads/articles/YYYY/MM/`, and creates a protective `.htaccess` to prevent execution.
- **Migrations:** `database/migration_002.sql` adds tags, revisions, password resets and alters `articles` (idempotent checks present).
- **Cron jobs:** `scripts/cron-publish.php` and `scripts/cron-cleanup.php` for scheduled publishing and cleanup; guidance in `docs/PHASE1_MIGRATION_AND_CRON.md`.
- **Admin editor:** TinyMCE is included under `assets/js/tinymce/` and initialized in `admin/includes/admin-header.php` and `admin/posts/{add,edit}.php`. (If you prefer plain HTML textareas, TinyMCE can be removed by deleting the includes and init code.)
- **Security & quality:** PDO prepared statements, password hashing, basic rate-limiting hooks, defensive DB helpers in `functions/db.php` to avoid fatal errors when migrations aren't applied.

## Requirements

- PHP 8.0+ (project was developed on PHP 8.x)
- MySQL 5.7+
- GD extension (image processing)
- PDO MySQL extension
- Apache or Nginx; `mod_rewrite` recommended for pretty URLs

## Quick installation

1. Clone and place in your webroot.
2. Create database and import `database/schema.sql`.
3. Apply migrations: at minimum run `database/migration_001.sql` and `database/migration_002.sql`.
4. Configure DB credentials in `config/database.php` or your environment loader (`config/env.php`).
5. Ensure `uploads/articles/` is writable by the web server. The upload endpoint will create safe subfolders and a `.htaccess` file.
6. Visit:
   - Frontend: `http://localhost/vintage-newspaper/`
   - Admin: `http://localhost/vintage-newspaper/admin/`

Default seeded admin (see `database/seed.sql`): `admin@gmail.com` / `admin123`.

## API endpoints (summary)

- `GET /api/articles.php` — list/filter articles (pagination)
- `GET /api/search.php` — full-text search
- `POST /api/upload.php` — secure image upload (session or Bearer JWT)
- `POST /api/login.php` — login + JWT
- `POST /api/password-reset.php` — request reset
- `POST /api/password-reset-verify.php` — verify and update password

Inspect the `api/` folder for details and parameter examples.

## Editor

- TinyMCE is included (admin WYSIWYG). Files: `assets/js/tinymce/`.
- Initialization happens in `admin/includes/admin-header.php` and `admin/posts/add.php` / `admin/posts/edit.php`.
- To revert to native HTML editing: remove TinyMCE script tags and the `tinymce.init(...)` blocks, and use plain `<textarea>` elements. Server-side sanitization is recommended when allowing HTML.

## Migrations & cron

- Migrations: see `database/` — `migration_002.sql` contains the Phase 1 changes (tags, revisions, password_resets, activity_log, article column additions).
- Cron scripts: `scripts/cron-publish.php`, `scripts/cron-cleanup.php`.

## Security notes

- Uploads: MIME & extension checks, EXIF removed, directories separated by date and protected with `.htaccess` to prevent execution.
- DB access uses PDO with prepared statements.
- Keep secrets out of VCS and use environment files or variables.

## Developer pointers

- Admin UI helpers and editor integration: `admin/includes/admin-header.php`, `admin/posts/add.php`, `admin/posts/edit.php`.
- DB helpers and new functions: `functions/db.php`.
- Upload logic: `api/upload.php`.
- Docs for migration and cron: `docs/PHASE1_MIGRATION_AND_CRON.md`.

## Testing checklist

1. Apply migrations.
2. Login to admin and create a post (add image upload, tags, seo fields).
3. Test password reset flow (inspect `password_resets` table).
4. Run `php scripts/cron-publish.php` to validate scheduled publishing behavior.

## Contributing

Fork → branch → PR. Include migration SQL when altering schema. Keep changes focused.

## License

MIT — see `LICENSE`.

---
If you want, I can remove TinyMCE and switch admin editor to default HTML now — say the word and I will update the admin files.

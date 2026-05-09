Phase 1 — Migration and Cron Instructions

Overview

This document describes how to apply the Phase 1 database migration and configure scheduled tasks (publish scheduler and cleanup) on Linux (cron) and Windows (Task Scheduler).

Files added/changed

- database/migration_002.sql — adds `status`, `publish_at`, SEO fields, `views`, `image` column to `articles`, and creates `article_revisions`, `tags`, `article_tag`, `activity_log`, `password_resets`.
- scripts/cron-publish.php — publishes scheduled articles whose `publish_at` <= NOW().
- scripts/cron-cleanup.php — removes expired/used password reset rows and prunes old activity logs.

Applying the migration

1. Backup your database before applying migrations.
2. From your MySQL client, run:

```sql
SOURCE /path/to/repo/database/migration_002.sql;
```

3. Verify new tables exist:

```sql
SHOW TABLES LIKE 'article_revisions';
SHOW TABLES LIKE 'tags';
SHOW TABLES LIKE 'password_resets';
```

4. Verify `articles` has new columns:

```sql
DESCRIBE articles;
```

Cron / Scheduled Tasks

Linux (cron)

- Open crontab for the web user or system user that has PHP CLI access:

```bash
crontab -e
```

- Add the following lines to run `cron-publish.php` every 5 minutes and cleanup once a day at 03:10:

```cron
*/5 * * * * /usr/bin/php /path/to/repo/scripts/cron-publish.php >> /var/log/vintage_publish.log 2>&1
10 3 * * * /usr/bin/php /path/to/repo/scripts/cron-cleanup.php >> /var/log/vintage_cleanup.log 2>&1
```

Windows (Task Scheduler)

- Open Task Scheduler -> Create Task.
- Set trigger for every 5 minutes for `cron-publish.php` and daily for `cron-cleanup.php`.
- Action: `Program/script` = path to `php.exe` (e.g., `C:\php\php.exe`).
- Add `Add arguments` = `C:\path\to\repo\scripts\cron-publish.php`.
- Set working directory to the repository folder.

Testing scripts manually

Run manually from command line (Linux or Windows) to verify:

```bash
php scripts/cron-publish.php
php scripts/cron-cleanup.php
```

Troubleshooting

- Ensure PHP CLI has PDO MySQL enabled and config/env.php or environment variables are set for DB credentials.
- If scripts fail with database connection errors, check `config/database.php` and that `includes/init.php` loads the DB.
- Confirm file permissions allow the cron user to read the PHP files and write log paths.

Notes

- `cron-publish.php` will set `status = 'published'` for rows with `status` in ('scheduled','pending') and `publish_at <= NOW()`.
- Adjust TTLs and cleanup intervals in `scripts/cron-cleanup.php` if needed.

Security

- Ensure uploads directory has an `.htaccess` blocking script execution (a basic `.htaccess` file is included in `/uploads`).
- Run scheduled tasks with the least-privileged user possible.

If you'd like, I can generate a SQL rollback script or add migration stamping to track applied migrations.

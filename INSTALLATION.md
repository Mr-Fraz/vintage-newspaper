# Vintage Newspaper - Setup & Installation Guide

## Prerequisites
- PHP 7.4+
- MySQL 5.7+
- XAMPP or similar local development environment

## Installation Steps

### 1. Database Setup

1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Create a new database or use the existing one
3. Import the schema:
   - Go to **Import** tab
   - Select `database/schema.sql`
   - Click **Import**

4. Apply migrations (for new installations):
   - Go to **Import** tab
   - Select `database/migration_001.sql`
   - Click **Import**

### 2. Environment Configuration

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update `.env` with your database credentials:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=your_password
   DB_NAME=vintage_news
   ```

3. **Important:** Never commit `.env` to version control - it contains sensitive data

### 3. File Permissions

Ensure upload directory is writable:
```bash
chmod 755 uploads/
```

### 4. Access the Application

- **Public Site:** http://localhost/vintage-newspaper
- **Admin Panel:** http://localhost/vintage-newspaper/admin
- **Default Admin Credentials:**
  - Email: admin@gmail.com
  - Password: 123456

## Directory Structure

```
vintage-newspaper/
├── admin/                    # Admin panel
│   ├── categories/          # Category management
│   ├── posts/              # Article management
│   ├── users/              # User management
│   ├── index.php           # Dashboard
│   └── logout.php
├── api/                     # API endpoints
│   ├── search.php          # Article search
│   └── upload.php          # File uploads
├── assets/
│   ├── css/
│   │   └── vintage.css     # Styling
│   ├── fonts/
│   ├── images/
│   └── js/
│       └── search.js       # Search functionality
├── config/
│   ├── config.php          # Configuration (empty, use .env)
│   ├── database.php        # Database connection
│   └── env.php             # Environment loader
├── database/
│   ├── schema.sql          # Initial schema
│   ├── seed.sql            # Seed data
│   └── migration_001.sql   # Enhancements (categories, etc.)
├── functions/
│   ├── auth.php            # Authentication functions
│   └── helpers.php         # Helper functions
├── includes/
│   ├── auth-middleware.php # Auth protection
│   ├── footer.php
│   ├── header.php          # Security headers
│   ├── init.php            # Initialization
│   └── navbar.php
├── pages/
│   ├── article.php         # Single article view
│   ├── category.php        # Category view
│   ├── home.php            # Homepage
│   ├── login.php           # Login form
│   ├── register.php        # Registration form
│   └── search.php          # Search results
├── uploads/                # User uploads
├── .env                    # Environment variables (don't commit)
├── .env.example            # Environment template
├── .gitignore
├── index.php               # Main entry point
└── README.md
```

## Security Features Implemented

✅ **Input Validation & Sanitization**
- All user inputs are validated and sanitized
- Prepared statements prevent SQL injection
- Output is properly escaped to prevent XSS

✅ **CSRF Protection**
- All forms include CSRF tokens
- Tokens are verified on submission
- Prevents cross-site request forgery attacks

✅ **Authentication & Authorization**
- Session-based authentication
- Role-based access control (User/Admin)
- Password hashing with password_hash()
- Auth middleware protects admin pages

✅ **File Upload Security**
- MIME type verification
- File size limits (5MB)
- Secure filename generation
- Upload directory outside web root recommended

✅ **Security Headers**
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin

## Features

### Content Management
- **Articles:** Create, read, update, delete with categories and authors
- **Categories:** Organize articles into categories with URLs
- **Search:** Real-time article search functionality

### User Management
- User registration and login
- Role-based access (User/Admin)
- Admin dashboard with statistics
- User management interface

### Admin Features
- Complete article CRUD
- Category management
- User role management
- Dashboard with statistics
- Comprehensive article filtering

## Usage

### Creating an Article
1. Go to Admin → Manage Articles
2. Click "Add New Article"
3. Fill in title, content, and category
4. Click "Add Article"

### Managing Categories
1. Go to Admin → Manage Categories
2. Add, edit, or delete categories
3. Categories appear on article pages

### Managing Users
1. Go to Admin → Manage Users
2. View all users
3. Change user roles (User/Admin)
4. Delete users (cannot delete yourself)

## Troubleshooting

### Database Connection Error
- Check `.env` file has correct credentials
- Ensure MySQL server is running
- Verify database name exists

### Permission Denied on Uploads
```bash
chmod 755 uploads/
chmod 755 admin/
```

### Session Errors
- Clear browser cookies and cache
- Ensure PHP sessions are enabled
- Check `php.ini` session settings

## Database Tables

### articles
- id, title, content, category_id, author_id, status, featured_image, created_at

### categories
- id, name, slug, description, created_at

### users
- id, name, email, password, role, created_at

## API Endpoints

### Search Articles
```
GET /api/search.php?q=search_term
```

### Upload File
```
POST /api/upload.php
```
Requires CSRF token and authentication

## Support & Maintenance

For issues or improvements:
1. Check the `.github/copilot-instructions.md` for project guidelines
2. Review security notes in code comments
3. Keep database backups regularly
4. Monitor logs for errors

## Version History

- **v2.0** - Added categories, user management, enhanced security
- **v1.0** - Initial release with basic CRUD operations

# 📰 Vintage Newspaper CMS

A vintage-themed newspaper Content Management System built with PHP, PDO, and MySQL. Features a beautiful retro newspaper design with modern functionality.

![Vintage Newspaper](https://img.shields.io/badge/Theme-Vintage-red?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-7.4+-blue?style=flat-square)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## ✨ Features

### 🎯 Core Features
- ✅ **User Authentication** - Secure login/register system with password hashing
- ✅ **Admin Dashboard** - Statistics overview and management interface
- ✅ **Article Management** - Full CRUD operations for articles
- ✅ **Category System** - Organize articles by categories
- ✅ **User Management** - Admin panel for managing users and roles
- ✅ **Search Functionality** - Full-text search across articles
- ✅ **Image Upload** - Featured images for articles with validation
- ✅ **Responsive Design** - Mobile-friendly vintage newspaper theme

### 🔒 Security Features
- ✅ **Password Hashing** - bcrypt for secure password storage
- ✅ **CSRF Protection** - Token-based CSRF prevention
- ✅ **XSS Protection** - Input sanitization and output escaping
- ✅ **SQL Injection Prevention** - PDO prepared statements
- ✅ **File Upload Validation** - Secure image upload handling
- ✅ **Session Management** - Secure session handling

### 🚀 Advanced Features
- ✅ **REST API** - JSON API for articles and search
- ✅ **Pagination** - Efficient article pagination
- ✅ **Slug Generation** - SEO-friendly URLs
- ✅ **Role-based Access** - Admin, Editor, and User roles
- ✅ **Rich Text Content** - HTML content support
- ✅ **Image Management** - Upload, display, and management
- ✅ **Statistics Dashboard** - Admin analytics

## 📋 Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Apache/Nginx** with mod_rewrite
- **GD Library** (for image handling)
- **PDO Extension** (PHP Data Objects)

## 🚀 Installation

### 1. Environment Setup

Clone the repository:
```bash
git clone https://github.com/yourusername/vintage-newspaper.git
cd vintage-newspaper
```

### 2. Database Setup

Create database and import schema:
```sql
CREATE DATABASE vintage_newspaper CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import the database schema:
```bash
mysql -u root -p vintage_newspaper < database/schema.sql
```

Apply migrations (if needed):
```bash
mysql -u root -p vintage_newspaper < database/migration_001.sql
```

### 3. Environment Configuration

Copy and configure environment file:
```bash
cp .env.example .env
```

Update `.env` with your settings:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=vintage_newspaper
DB_USER=root
DB_PASS=your_password

# Site Configuration
SITE_NAME=Vintage Newspaper
SITE_URL=http://localhost/vintage-newspaper
ADMIN_EMAIL=admin@vintagenews.com

# Security
SESSION_SECRET=your_random_secret_key
```

### 4. File Permissions

Set proper permissions for uploads:
```bash
chmod 755 uploads/
chmod 755 uploads/articles/
```

### 5. Access the Application

- **Frontend:** `http://localhost/vintage-newspaper/`
- **Admin Panel:** `http://localhost/vintage-newspaper/admin/`

**Default Admin Credentials:**
- Email: `admin@gmail.com`
- Password: `admin123`

## 📁 Project Structure

```
vintage-newspaper/
├── 📁 admin/                 # Admin panel
│   ├── 📁 categories/        # Category management
│   ├── 📁 posts/            # Article management
│   ├── 📁 users/            # User management
│   ├── 📁 includes/         # Admin templates
│   └── index.php            # Admin dashboard
├── 📁 api/                  # REST API endpoints
│   ├── articles.php         # Articles API
│   ├── search.php           # Search API
│   └── upload.php           # File upload API
├── 📁 assets/               # Static assets
│   ├── 📁 css/              # Stylesheets
│   ├── 📁 js/               # JavaScript files
│   ├── 📁 images/           # Static images
│   └── 📁 fonts/            # Custom fonts
├── 📁 config/               # Configuration files
│   ├── config.php           # Main configuration
│   ├── database.php         # Database connection
│   └── env.php              # Environment loader
├── 📁 database/             # Database files
│   ├── schema.sql           # Database schema
│   ├── migration_001.sql    # Database migrations
│   └── seed.sql             # Sample data
├── 📁 functions/            # Core functions
│   ├── auth.php             # Authentication functions
│   ├── db.php               # Database operations
│   ├── helpers.php          # Utility functions
│   └── validation.php       # Input validation
├── 📁 includes/             # Frontend templates
│   ├── header.php           # Site header
│   ├── navbar.php           # Navigation
│   └── footer.php           # Site footer
├── 📁 pages/                # Frontend pages
│   ├── home.php             # Homepage
│   ├── article.php          # Article view
│   ├── category.php         # Category view
│   ├── search.php           # Search results
│   ├── login.php            # Login page
│   └── register.php         # Registration page
└── 📁 uploads/              # User uploads
    └── 📁 articles/         # Article images
```

## 🎨 Usage Guide

### For Content Authors

#### Adding Articles
1. Login to admin panel
2. Navigate to **Posts → Add New Post**
3. Fill in title, content, category, and excerpt
4. Upload featured image (optional)
5. Set status to "Published" or "Draft"
6. Click "Create Post"

#### Managing Categories
1. Go to **Categories** in admin sidebar
2. Click "Add New Category"
3. Enter name, slug, and description
4. Save category

### For Administrators

#### User Management
1. Access **Users** section in admin
2. Click "Manage" on any user
3. Change user role (User/Admin)
4. Delete users if needed

#### Content Moderation
- View all articles in **Posts → All Posts**
- Edit or delete any article
- Monitor user activity and content

## 🌐 API Documentation

The CMS provides RESTful API endpoints for integration:

### Articles API
```http
GET /api/articles.php?page=1&limit=10
```

**Response:**
```json
{
  "success": true,
  "page": 1,
  "limit": 10,
  "total": 25,
  "articles": [
    {
      "id": 1,
      "title": "Article Title",
      "slug": "article-title",
      "content": "Article content...",
      "excerpt": "Article excerpt...",
      "featured_image": "image.jpg",
      "category_name": "News",
      "author": "John Doe",
      "created_at": "2024-01-01 12:00:00",
      "status": "published"
    }
  ]
}
```

### Search API
```http
GET /api/search.php?q=keyword&page=1
```

**Response:**
```json
{
  "success": true,
  "count": 5,
  "results": [
    {
      "id": 1,
      "title": "Article Title",
      "excerpt": "Matching excerpt...",
      "category_name": "News",
      "created_at": "2024-01-01"
    }
  ]
}
```

### Upload API
```http
POST /api/upload.php
Content-Type: multipart/form-data

Form Data:
- image: [file]
```

## 🔧 Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `vintage_newspaper` |
| `DB_USER` | Database user | `root` |
| `DB_PASS` | Database password | `""` |
| `SITE_NAME` | Site title | `Vintage Newspaper` |
| `SITE_URL` | Site URL | `http://localhost/vintage-newspaper` |
| `ADMIN_EMAIL` | Admin email | `admin@vintagenews.com` |
| `SESSION_SECRET` | Session encryption key | Random string |

### Upload Settings

Configure in `config/config.php`:
```php
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/articles/');
```

## 🔒 Security Best Practices

- **Never commit `.env` file** to version control
- **Use strong passwords** for admin accounts
- **Keep PHP and MySQL updated**
- **Regular backup** of database
- **Monitor file permissions** on upload directories
- **Enable HTTPS** in production
- **Use prepared statements** (already implemented with PDO)

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error:**
- Verify `.env` credentials
- Ensure MySQL service is running
- Check database exists

**File Upload Issues:**
- Check upload directory permissions
- Verify GD library is installed
- Check file size limits in PHP config

**Admin Login Issues:**
- Use default credentials: `admin@gmail.com` / `admin123`
- Check database seed data was imported

**404 Errors:**
- Ensure mod_rewrite is enabled
- Check `.htaccess` file exists
- Verify Apache configuration

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit pull request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Vintage newspaper design inspiration
- PHP community for excellent documentation
- Open source contributors

## 📞 Support

- 📧 **Email:** support@vintagenews.com
- 🐛 **Issues:** [GitHub Issues](https://github.com/yourusername/vintage-newspaper/issues)
- 📖 **Documentation:** [Wiki](https://github.com/yourusername/vintage-newspaper/wiki)

---

**Made with ❤️ for vintage newspaper enthusiasts**

---
**University Major Project**

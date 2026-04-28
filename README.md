# Vintage Newspaper - CMS Platform

A vintage-themed newspaper Content Management System built with PHP and MySQL.

## 🎯 Features

- ✅ User Authentication (Login/Register)
- ✅ Admin Dashboard with Statistics
- ✅ Article Management (CRUD operations)
- ✅ Category System
- ✅ Search Functionality
- ✅ Image Upload
- ✅ Responsive Design
- ✅ Vintage Newspaper Theme
- ✅ Security Features (CSRF, XSS, SQL Injection Protection)

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx with mod_rewrite
- GD Library (for image handling)

## 🚀 Installation

### 1. Database Setup

```sql
CREATE DATABASE vintage_newspaper;
```

Import schema:
```bash
mysql -u root -p vintage_newspaper < database/schema.sql
```

### 2. Configure .env

Update database credentials:
```
DB_HOST=localhost
DB_NAME=vintage_newspaper
DB_USER=root
DB_PASS=your_password
```

### 3. Set Permissions

```bash
chmod 755 uploads/
```

### 4. Access

- Frontend: `http://localhost/vintage-newspaper/`
- Admin: `http://localhost/vintage-newspaper/admin/`

**Default Login:**
- Email: admin@vintagenews.com
- Password: admin123

## 📁 Structure

```
vintage-newspaper/
├── config/          # Configuration
├── functions/       # Core functions
├── pages/           # Frontend pages
├── admin/           # Admin panel
├── api/             # REST endpoints
├── assets/          # CSS/JS
└── uploads/         # User uploads
```

## 🔐 Security

- Password hashing (bcrypt)
- SQL injection prevention
- XSS protection
- CSRF tokens
- File upload validation

## 📝 Usage

**Add Article:**
Admin → Posts → Add New Post

**Manage Categories:**
Admin → Categories

## 🌐 API

```
GET /api/articles.php?page=1
GET /api/search.php?q=keyword
```

## 🤝 Support

Create GitHub issue for bugs/features.

---
**University Major Project**

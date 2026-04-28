<?php
require_once __DIR__ . '/../config/config.php';

class DB {
    private static $conn;
    
    public static function init() {
        global $db;
        self::$conn = $db;
    }
    
    // Get all articles with pagination
    public static function getArticles($page = 1, $limit = POSTS_PER_PAGE) {
        self::init();
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT a.*, c.name as category_name, u.username as author 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.status = 'published' 
                ORDER BY a.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = self::$conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get single article by ID
    public static function getArticle($id) {
        self::init();
        
        $sql = "SELECT a.*, c.name as category_name, u.username as author 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.id = :id AND a.status = 'published'";
        
        $stmt = self::$conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Get articles by category
    public static function getArticlesByCategory($slug, $page = 1, $limit = POSTS_PER_PAGE) {
        self::init();
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT a.*, c.name as category_name, u.username as author 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE c.slug = :slug AND a.status = 'published' 
                ORDER BY a.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = self::$conn->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Search articles
    public static function searchArticles($query) {
        self::init();
        
        $sql = "SELECT a.*, c.name as category_name, u.username as author 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE (a.title LIKE :query OR a.content LIKE :query) 
                AND a.status = 'published' 
                ORDER BY a.created_at DESC";
        
        $stmt = self::$conn->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get all categories
    public static function getCategories() {
        self::init();
        
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = self::$conn->query($sql);
        
        return $stmt->fetchAll();
    }
    
    // Create article
    public static function createArticle($data) {
        self::init();
        
        $sql = "INSERT INTO articles (title, slug, content, excerpt, image, category_id, author_id, status) 
                VALUES (:title, :slug, :content, :excerpt, :image, :category_id, :author_id, :status)";
        
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    // Update article
    public static function updateArticle($id, $data) {
        self::init();
        
        $sql = "UPDATE articles 
                SET title = :title, slug = :slug, content = :content, 
                    excerpt = :excerpt, featured_image = :featured_image, category_id = :category_id, 
                    status = :status, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = self::$conn->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    
    // Delete article
    public static function deleteArticle($id) {
        self::init();
        
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = self::$conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Count total articles
    public static function countArticles() {
        self::init();
        
        $sql = "SELECT COUNT(*) as total FROM articles WHERE status = 'published'";
        $stmt = self::$conn->query($sql);
        $result = $stmt->fetch();
        
        return $result['total'];
    }
}
?>

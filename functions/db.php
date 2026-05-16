<?php
require_once __DIR__ . '/../config/config.php';

class DB
{
    private static ?PDO $conn = null;

    public static function init()
    {
        global $db;
        self::$conn = $db;
    }

    public static function getConnection()
    {
        self::init();
        return self::$conn;
    }

    // Insert media record, return new ID
    public static function insertMedia($data)
    {
        self::init();
        $stmt = self::$conn->prepare("
        INSERT INTO media (filename, filename_thumb, filename_medium, alt_text, uploaded_by)
        VALUES (:filename, :filename_thumb, :filename_medium, :alt_text, :uploaded_by)
    ");
        $stmt->execute($data);
        return self::$conn->lastInsertId();
    }

    // Get all media (for library picker)
    public static function getMediaLibrary()
    {
        self::init();
        $stmt = self::$conn->prepare("
        SELECT * FROM media ORDER BY created_at DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single media by ID
    public static function getMedia($id)
    {
        self::init();
        $stmt = self::$conn->prepare("SELECT * FROM media WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update alt text
    public static function updateMediaAlt($id, $alt)
    {
        self::init();
        $stmt = self::$conn->prepare("UPDATE media SET alt_text = :alt WHERE id = :id");
        return $stmt->execute(['alt' => $alt, 'id' => $id]);
    }

    // Get approved comments for article
    public static function getComments($article_id)
    {
        self::init();
        $stmt = self::$conn->prepare("
        SELECT c.*, u.username 
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.article_id = :aid AND c.status = 'approved'
        ORDER BY c.created_at ASC
    ");
        $stmt->execute(['aid' => $article_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Submit comment
    public static function addComment($data)
    {
        self::init();
        $stmt = self::$conn->prepare("
        INSERT INTO comments (article_id, user_id, guest_name, guest_email, body, status)
        VALUES (:article_id, :user_id, :guest_name, :guest_email, :body, 'pending')
    ");
        return $stmt->execute($data);
    }

    // Admin: get all comments
    public static function getAllComments($status = null)
    {
        self::init();
        $where = $status ? "WHERE c.status = :status" : "";
        $stmt = self::$conn->prepare("
        SELECT c.*, a.title as article_title, u.username
        FROM comments c
        LEFT JOIN articles a ON c.article_id = a.id
        LEFT JOIN users u ON c.user_id = u.id
        $where
        ORDER BY c.created_at DESC
    ");
        $params = $status ? ['status' => $status] : [];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin: update comment status
    public static function updateCommentStatus($id, $status)
    {
        self::init();
        $stmt = self::$conn->prepare("UPDATE comments SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    // Admin: delete comment
    public static function deleteComment($id)
    {
        self::init();
        $stmt = self::$conn->prepare("DELETE FROM comments WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Check if slug exists (excluding current category if id provided)
    public static function slugExists($slug, $id = 0)
    {
        self::init();

        if ($id > 0) {
            $stmt = self::$conn->prepare(
                "SELECT id FROM categories WHERE slug = :slug AND id != :id"
            );
            $stmt->execute(['slug' => $slug, 'id' => $id]);
        } else {
            $stmt = self::$conn->prepare(
                "SELECT id FROM categories WHERE slug = :slug"
            );
            $stmt->execute(['slug' => $slug]);
        }

        return $stmt->fetch() ? true : false;
    }

    // Get category by ID
    public static function getCategoryById($id)
    {
        self::init();
        $stmt = self::$conn->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update category
    public static function updateCategory($data)
    {
        self::init();
        $sql = "UPDATE categories 
            SET name = :name,
                slug = :slug,
                description = :description
            WHERE id = :id";
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Add new category
    public static function addCategory($data)
    {
        self::init();
        $sql = "INSERT INTO categories (name, slug, description) 
                VALUES (:name, :slug, :description)";
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Get all articles with pagination — optimized: no SELECT *
    public static function getArticles($page = 1, $limit = POSTS_PER_PAGE)
    {
        self::init();
        $offset = ($page - 1) * $limit;

        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.image, a.image_alt,
                       a.views, a.created_at, a.status,
                       c.name as category_name, c.slug as category_slug,
                       u.username as author
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
    public static function getArticle($id)
    {
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

    // Get article for admin editing (any status)
    public static function getArticleForEdit($id)
    {
        self::init();

        $sql = "SELECT a.*, c.name as category_name, u.username as author 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.id = :id";

        $stmt = self::$conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get articles by category — optimized: INNER JOIN + specific columns
    public static function getArticlesByCategory($slug, $page = 1, $limit = POSTS_PER_PAGE)
    {
        self::init();
        $offset = ($page - 1) * $limit;

        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.image, a.created_at,
                       c.name as category_name, c.slug as category_slug,
                       u.username as author
                FROM articles a
                INNER JOIN categories c ON a.category_id = c.id AND c.slug = :slug
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.status = 'published'
                ORDER BY a.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = self::$conn->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Search articles — optimized: FULLTEXT index instead of LIKE scan
    public static function searchArticles($query, $limit = 20)
    {
        self::init();

        if (strlen($query) < 3) return [];

        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.image, a.created_at,
                       c.name as category_name, u.username as author,
                       MATCH(a.title, a.content) AGAINST (:query IN BOOLEAN MODE) AS relevance
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                WHERE MATCH(a.title, a.content) AGAINST (:query2 IN BOOLEAN MODE)
                AND a.status = 'published'
                ORDER BY relevance DESC
                LIMIT :limit";

        $stmt = self::$conn->prepare($sql);
        $stmt->bindValue(':query',  $query, PDO::PARAM_STR);
        $stmt->bindValue(':query2', $query, PDO::PARAM_STR);
        $stmt->bindValue(':limit',  $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get all categories
    public static function getCategories()
    {
        self::init();
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Dashboard stats — single query instead of 4 round-trips
    public static function getDashboardStats()
    {
        self::init();
        $stmt = self::$conn->prepare("
            SELECT
                COUNT(*) as total_articles,
                SUM(status = 'published') as published_articles,
                SUM(status = 'draft') as draft_articles,
                SUM(status = 'pending') as pending_articles,
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM categories) as total_categories,
                (SELECT COUNT(*) FROM comments WHERE status = 'pending') as pending_comments
            FROM articles
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Recent articles for dashboard
    public static function getRecentArticles($limit = 5)
    {
        self::init();
        $stmt = self::$conn->prepare("
            SELECT a.id, a.title, a.status, a.created_at,
                   c.name as category_name, u.username
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create article
    public static function createArticle($data)
    {
        self::init();

        $sql = "INSERT INTO articles (title, slug, lang, content, excerpt, image, media_id, image_alt, category_id, author_id, status, seo_title, meta_description, og_image)
                VALUES (:title, :slug, :lang, :content, :excerpt, :image, :media_id, :image_alt, :category_id, :author_id, :status, :seo_title, :meta_description, :og_image)";

        $stmt = self::$conn->prepare($sql);
        $ok = $stmt->execute(array_merge([
            'lang'             => 'en',
            'seo_title'        => null,
            'meta_description' => null,
            'og_image'         => null
        ], $data));

        if ($ok) return self::$conn->lastInsertId();
        return false;
    }

    // Update article
    public static function updateArticle($id, $data)
    {
        self::init();

        $sql = "UPDATE articles 
                SET title = :title, slug = :slug, content = :content, 
                    excerpt = :excerpt, image = :image, media_id = :media_id, image_alt = :image_alt, category_id = :category_id, 
                    status = :status, seo_title = :seo_title, meta_description = :meta_description, og_image = :og_image, updated_at = NOW() 
                WHERE id = :id";

        $stmt = self::$conn->prepare($sql);
        $data['id'] = $id;
        $defaults = [
            'seo_title' => null,
            'meta_description' => null,
            'og_image' => null
        ];
        $params = array_merge($defaults, $data);
        return $stmt->execute($params);
    }

    // Clear all tags for an article (helper)
    public static function clearArticleTags($article_id)
    {
        self::init();
        $stmt = self::$conn->prepare("DELETE FROM article_tag WHERE article_id = :aid");
        return $stmt->execute(['aid' => $article_id]);
    }

    // Delete article
    public static function deleteArticle($id)
    {
        self::init();
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = self::$conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Count total published articles
    public static function countArticles()
    {
        self::init();
        $sql = "SELECT COUNT(*) as total FROM articles WHERE status = 'published'";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Create a revision entry from the current article state
    public static function createRevision($article_id, $user_id = null)
    {
        self::init();

        $article = self::getArticleForEdit($article_id);
        if (!$article) return false;

        $sql = "INSERT INTO article_revisions (article_id, user_id, title, content, seo_title, meta_description, created_at)
                VALUES (:article_id, :user_id, :title, :content, :seo_title, :meta_description, NOW())";

        $stmt = self::$conn->prepare($sql);
        return $stmt->execute([
            'article_id'       => $article_id,
            'user_id'          => $user_id,
            'title'            => $article['title'],
            'content'          => $article['content'],
            'seo_title'        => isset($article['seo_title']) ? $article['seo_title'] : null,
            'meta_description' => isset($article['meta_description']) ? $article['meta_description'] : null
        ]);
    }

    // Get revisions for an article
    public static function getArticleRevisions($article_id)
    {
        self::init();

        $sql = "SELECT ar.*, u.username as author
                FROM article_revisions ar
                LEFT JOIN users u ON ar.user_id = u.id
                WHERE ar.article_id = :article_id
                ORDER BY ar.created_at DESC";

        $stmt = self::$conn->prepare($sql);
        $stmt->execute(['article_id' => $article_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get tags attached to an article
    public static function getArticleTags($article_id)
    {
        self::init();
        try {
            $sql = "SELECT t.* FROM tags t
                    JOIN article_tag at ON at.tag_id = t.id
                    WHERE at.article_id = :article_id";

            $stmt = self::$conn->prepare($sql);
            $stmt->execute(['article_id' => $article_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Attach tags (array of names) to an article
    public static function attachTagsToArticle($article_id, $tagNames = [])
    {
        self::init();
        if (empty($tagNames)) return true;

        try {
            foreach ($tagNames as $tagName) {
                $name = trim($tagName);
                if ($name === '') continue;
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));

                $stmt = self::$conn->prepare("SELECT id FROM tags WHERE slug = :slug LIMIT 1");
                $stmt->execute(['slug' => $slug]);
                $tag = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($tag) {
                    $tag_id = $tag['id'];
                } else {
                    $ins = self::$conn->prepare("INSERT INTO tags (name, slug, created_at) VALUES (:name, :slug,S NOW())");
                    $ins->execute(['name' => $name, 'slug' => $slug]);
                    $tag_id = self::$conn->lastInsertId();
                }

                $chk = self::$conn->prepare("SELECT 1 FROM article_tag WHERE article_id = :aid AND tag_id = :tid LIMIT 1");
                $chk->execute(['aid' => $article_id, 'tid' => $tag_id]);
                if (!$chk->fetch()) {
                    $attach = self::$conn->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (:aid, :tid)");
                    $attach->execute(['aid' => $article_id, 'tid' => $tag_id]);
                }
            }
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    // Log an activity
    public static function logActivity($user_id = null, $action = null, $entity_type = null, $entity_id = null, $meta = null, $ip = null)
    {
        self::init();

        $sql = "INSERT INTO activity_log (user_id, action, entity_type, entity_id, meta, ip, created_at)
                VALUES (:user_id, :action, :entity_type, :entity_id, :meta, :ip, NOW())";

        $stmt = self::$conn->prepare($sql);
        $metaJson = $meta ? json_encode($meta) : null;
        return $stmt->execute([
            'user_id'     => $user_id,
            'action'      => $action,
            'entity_type' => $entity_type,
            'entity_id'   => $entity_id,
            'meta'        => $metaJson,
            'ip'          => $ip
        ]);
    }

    // Create a password reset token and return it
    public static function createPasswordReset($email, $ttl_seconds = 3600)
    {
        self::init();

        $token = bin2hex(random_bytes(32));

        $sql = "INSERT INTO password_resets (email, token, expires_at, used, created_at)
            VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL :ttl SECOND), 0, NOW())";

        try {
            $stmt = self::$conn->prepare($sql);
            $ok = $stmt->execute([
                'email' => $email,
                'token' => $token,
                'ttl'   => $ttl_seconds
            ]);
            if ($ok) return $token;
            return false;
        } catch (Exception $e) {
            error_log('createPasswordReset error: ' . $e->getMessage());
            return false;
        }
    }

    // Get a valid password reset row by token
    public static function getPasswordReset($token)
    {
        self::init();

        $sql = "SELECT * FROM password_resets WHERE token = :token AND used = 0 AND expires_at >= NOW() LIMIT 1";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mark a token as used
    public static function consumePasswordReset($token)
    {
        self::init();

        $sql = "UPDATE password_resets SET used = 1 WHERE token = :token";
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute(['token' => $token]);
    }

    // ── MULTI-LANGUAGE TRANSLATION METHODS ──────────────────────────────────

    /**
     * Upsert a translation for an article.
     * If lang record exists → update. Else → insert.
     */
    public static function upsertTranslation($article_id, $lang, $data)
    {
        self::init();
        $existing = self::getTranslation($article_id, $lang);
        if ($existing) {
            $sql = "UPDATE article_translations
                    SET title=:title, slug=:slug, content=:content, excerpt=:excerpt,
                        seo_title=:seo_title, meta_description=:meta_description, status=:status,
                        updated_at=NOW()
                    WHERE article_id=:article_id AND lang=:lang";
        } else {
            $sql = "INSERT INTO article_translations
                        (article_id, lang, title, slug, content, excerpt, seo_title, meta_description, status)
                    VALUES
                        (:article_id, :lang, :title, :slug, :content, :excerpt, :seo_title, :meta_description, :status)";
        }
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute(array_merge([
            'article_id'       => $article_id,
            'lang'             => $lang,
            'seo_title'        => null,
            'meta_description' => null,
        ], $data));
    }

    /** Get one translation row */
    public static function getTranslation($article_id, $lang)
    {
        self::init();
        $stmt = self::$conn->prepare(
            "SELECT * FROM article_translations WHERE article_id=:a AND lang=:l LIMIT 1"
        );
        $stmt->execute(['a' => $article_id, 'l' => $lang]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Get all translations for an article */
    public static function getTranslations($article_id)
    {
        self::init();
        $stmt = self::$conn->prepare(
            "SELECT * FROM article_translations WHERE article_id=:a ORDER BY lang"
        );
        $stmt->execute(['a' => $article_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Delete one translation */
    public static function deleteTranslation($article_id, $lang)
    {
        self::init();
        $stmt = self::$conn->prepare(
            "DELETE FROM article_translations WHERE article_id=:a AND lang=:l"
        );
        return $stmt->execute(['a' => $article_id, 'l' => $lang]);
    }

    /**
     * Fetch published article by slug — checks base articles table first,
     * then article_translations. Returns merged row with lang flag.
     */
    public static function getArticleBySlugAndLang($slug, $lang = 'en')
    {
        self::init();
        if ($lang === 'en') {
            $stmt = self::$conn->prepare(
                "SELECT a.*, u.username AS author_name, c.name AS category_name
                 FROM articles a
                 LEFT JOIN users u ON a.author_id = u.id
                 LEFT JOIN categories c ON a.category_id = c.id
                 WHERE a.slug=:slug AND a.status='published' LIMIT 1"
            );
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Join translation with original for image/category etc.
        $stmt = self::$conn->prepare(
            "SELECT t.*, a.image, a.image_alt, a.media_id, a.category_id,
                    u.username AS author_name, c.name AS category_name
             FROM article_translations t
             JOIN articles a ON t.article_id = a.id
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE t.slug=:slug AND t.lang=:lang AND t.status='published'
             LIMIT 1"
        );
        $stmt->execute(['slug' => $slug, 'lang' => $lang]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
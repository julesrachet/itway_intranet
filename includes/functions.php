<?php
require_once 'config/database.php';

class BlogFunctions {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createPost($title, $content, $authorId, $authorName) {
        $query = "INSERT INTO posts (title, content, author_id, author_name, created_at) 
                  VALUES (:title, :content, :author_id, :author_name, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_id', $authorId);
        $stmt->bindParam(':author_name', $authorName);
        return $stmt->execute();
    }

    public function getAllPosts($limit = 10, $offset = 0) {
        $query = "SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePostsByID($id) {
        $id = (int) $id;
        $query = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getPostById($id) {
        $query = "SELECT * FROM posts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPostsByAuthor($authorId) {
        $query = "SELECT * FROM posts WHERE author_id = :author_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author_id', $authorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}  // ← unique fermeture de la classe
?>

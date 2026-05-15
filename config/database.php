<?php
class Database {
    private $host     = 'localhost';
    private $db_name  = 'blog_db';
    private $username = 'blog_user';
    private $password = 'ChangeMe2024!';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("DB Connection error: " . $e->getMessage());
            die("Erreur de connexion à la base de données.");
        }
        return $this->conn;
    }
}

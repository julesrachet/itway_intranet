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
            "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
            $this->username,
            $this->password
        );
        $this->conn->exec("set names utf8");
        
        // ✅ Ajouter ces 2 lignes
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
    } catch(PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
    
    return $this->conn;
}
}

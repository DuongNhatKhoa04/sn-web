<?php
require_once 'Database.php';

class Article {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // Lay tat ca bai viet moi nhat
    public function getLatest($limit = 6) {
        $stmt = $this->db->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lay chi tiet 1 bai
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Tim kiem
    public function search($keyword) {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE title LIKE :key OR summary LIKE :key");
        $stmt->execute([':key' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<?php
require_once 'Database.php';

class Article {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // 1. Lấy danh sách bài viết có phân trang
    public function getPaginated($limit, $offset) {
        // LIMIT: Lấy bao nhiêu bài
        // OFFSET: Bỏ qua bao nhiêu bài (để lấy trang tiếp theo)
        $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Đếm tổng số bài viết (Để tính ra tổng số trang)
    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM articles");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // 3. Tìm kiếm (Giữ nguyên)
    public function search($keyword) {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE title LIKE :key OR summary LIKE :key");
        $stmt->execute([':key' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 4. Lấy chi tiết (Giữ nguyên)
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
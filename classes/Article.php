<?php
require_once 'Database.php';

class Article {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // 1. Lấy danh sách bài viết KÈM THEO THÔNG TIN DANH MỤC
    public function getPaginated($limit, $offset) {
        // Dùng LEFT JOIN để lấy thêm icon và color từ bảng categories
        // Kết nối qua tên: a.category = c.name
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category = c.name 
                ORDER BY a.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Đếm tổng số bài viết
    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM articles");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // 3. Tìm kiếm (Cập nhật để lấy thêm thông tin danh mục)
    public function search($keyword) {
        // Sử dụng % để tìm kiếm gần đúng (LIKE)
        $keyword = "%$keyword%";
        
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category = c.name 
                WHERE a.title LIKE :key OR a.summary LIKE :key
                ORDER BY a.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':key' => $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 4. Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category = c.name 
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
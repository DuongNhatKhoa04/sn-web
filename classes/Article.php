<?php
require_once 'Database.php';

class Article {
    private $db;

    // Khi khởi tạo, tự động kết nối với Database
    public function __construct() {
        // Đảm bảo kết nối đúng kiểu new Database()
        $this->db = (new Database())->getConnection();
    }

    // 1. Lấy danh sách bài viết theo trang (Phân trang)
    public function getPaginated($limit, $offset) {
        // --- SỬA LỖI Ở ĐÂY ---
        // Thay vì: ON a.category = c.name
        // Sửa thành: ON a.category_id = c.category_id
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                ORDER BY a.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Đếm tổng số lượng bài viết đang có trong kho
    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM articles");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // 3. Tìm kiếm bài viết theo từ khóa
    public function search($keyword) {
        $keyword = "%$keyword%";
        
        // --- SỬA LỖI Ở ĐÂY ---
        // Cập nhật câu JOIN theo ID
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.title LIKE :key OR a.summary LIKE :key
                ORDER BY a.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':key' => $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 4. Lấy chi tiết một bài viết cụ thể dựa vào ID
    public function getById($id) {
        // --- SỬA LỖI Ở ĐÂY ---
        // Cập nhật câu JOIN theo ID
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
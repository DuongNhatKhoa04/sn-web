<?php
require_once 'Database.php';

class Article {
    private $db;

    // Khi khởi tạo, tự động kết nối với Database
    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // 1. Lấy danh sách bài viết theo trang (Phân trang)
    // $limit: Lấy bao nhiêu bài? $offset: Bỏ qua bao nhiêu bài đầu?
    public function getPaginated($limit, $offset) {
        // Câu lệnh SQL: Lấy bài viết (articles) VÀ kèm theo thông tin icon/màu sắc từ bảng danh mục (categories)
        // LEFT JOIN giống như việc dán nhãn màu lên bìa sách dựa theo thể loại sách
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category = c.name 
                ORDER BY a.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        // Gán giá trị số lượng và vị trí bắt đầu
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        // Trả về danh sách kết quả
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
        // Thêm dấu % vào trước và sau để tìm kiếm tương đối (ví dụ: tìm "phố" sẽ ra "thành phố")
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
    
    // 4. Lấy chi tiết một bài viết cụ thể dựa vào ID
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
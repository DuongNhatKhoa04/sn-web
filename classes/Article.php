<?php
require_once 'Database.php';

class Article {
    private $db;

    // Khi khởi tạo (new Article), tự động xin kết nối Database ngay
    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // 1. Lấy danh sách bài viết theo trang (Phân trang)
    // $limit: Lấy bao nhiêu bài? (VD: 6 bài)
    // $offset: Bỏ qua bao nhiêu bài đầu? (VD: Trang 2 thì bỏ qua 6 bài đầu)
    public function getPaginated($limit, $offset) {
        // Câu lệnh SQL: "Lấy tất cả thông tin bài viết (a.*), KÈM THEO tên danh mục (cat_name)..."
        // LEFT JOIN: Giống như việc nối bảng Bài Viết với bảng Danh Mục dựa vào mã số (category_id)
        $sql = "SELECT a.*, 
                       c.name as cat_name, 
                       c.icon as cat_icon, 
                       c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                ORDER BY a.created_at DESC  -- Sắp xếp bài mới nhất lên đầu
                LIMIT :limit OFFSET :offset"; // Chỉ lấy đúng số lượng yêu cầu
                
        $stmt = $this->db->prepare($sql);
        // Gán giá trị số vào câu lệnh (để tránh bị hack SQL Injection)
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute(); // Thực thi lệnh
        
        // Trả về danh sách kết quả (Mảng dữ liệu)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Đếm tổng số bài viết đang có trong kho (Để biết mà chia trang)
    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM articles");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total']; // Trả về con số (VD: 15 bài)
    }

    // 3. Tìm kiếm bài viết theo từ khóa
    public function search($keyword) {
        // Thêm dấu % vào trước và sau để tìm kiếm tương đối (ví dụ: gõ "iphone" tìm ra "iphone 16")
        $keyword = "%$keyword%";
        
        $sql = "SELECT a.*, c.name as cat_name, c.icon as cat_icon, c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.title LIKE :key OR a.summary LIKE :key -- Tìm trong Tiêu đề HOẶC Tóm tắt
                ORDER BY a.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':key' => $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 4. Lấy chi tiết 1 bài viết dựa vào ID (Khi bấm vào xem chi tiết)
    public function getById($id) {
        $sql = "SELECT a.*, c.name as cat_name, c.icon as cat_icon, c.color as cat_color 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.id = :id"; // Chỉ lấy bài có ID trùng khớp
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Chỉ trả về 1 dòng kết quả duy nhất
    }
}
?>
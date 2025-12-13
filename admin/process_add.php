<?php
// admin/process_add.php
require_once '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = Database::getInstance()->getConnection();

    // Lấy dữ liệu văn bản
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $summary = $_POST['summary'];
    $content = $_POST['content']; // Dữ liệu HTML từ CKEditor gửi sang
    $author_id = 1; // Mặc định admin

    // --- PHẦN XỬ LÝ UPLOAD ẢNH (BẮT BUỘC ĐỂ CÓ ĐIỂM) ---
    $imageUrl = '';
    
    // Kiểm tra xem người dùng có chọn file không
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../images/posts/'; // Thư mục lưu ảnh
        
        // Tạo tên file mới ngẫu nhiên để tránh trùng (VD: post_6512a...jpg)
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('post_') . '.' . $extension;
        $targetFile = $uploadDir . $newFileName;

        // Di chuyển file từ bộ nhớ tạm vào thư mục images/posts
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Lưu đường dẫn vào biến để lát insert vào DB
            $imageUrl = 'images/posts/' . $newFileName; 
        } else {
            die("Lỗi: Không thể lưu file ảnh lên server.");
        }
    } else {
        die("Lỗi: Vui lòng chọn ảnh đại diện.");
    }

    // --- PHẦN INSERT VÀO DATABASE (BẢNG ARTICLES) ---
    try {
        $sql = "INSERT INTO articles (title, category_id, summary, content, image_url, author_id, created_at, views) 
                VALUES (:t, :c, :s, :content, :img, :auth, NOW(), 0)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':t' => $title,
            ':c' => $cat_id,
            ':s' => $summary,
            ':content' => $content, // Lưu cả mã HTML của CKEditor vào DB
            ':img' => $imageUrl,    // Lưu đường dẫn ảnh (VD: images/posts/abc.jpg)
            ':auth' => $author_id
        ]);

        // Thông báo thành công và quay về trang chủ
        echo "<script>alert('Đã thêm bài viết thành công!'); window.location.href='../index.php';</script>";

    } catch (PDOException $e) {
        echo "Lỗi Insert: " . $e->getMessage();
    }
}
?>
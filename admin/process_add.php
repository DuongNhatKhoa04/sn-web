<?php
// admin/process_add.php
require_once '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Kết nối CSDL (Sửa lỗi getInstance cũ)
    $db = new Database();
    $conn = $db->getConnection();

    // 2. Lấy dữ liệu từ form
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $summary = $_POST['summary'];
    $content = $_POST['content']; 
    $author_id = 1; // Giả định ID admin là 1

    // 3. XỬ LÝ UPLOAD ẢNH
    $imageUrl = '';
    
    // Kiểm tra có file được gửi lên không
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Tạo thư mục nếu chưa có
        $uploadDir = '../images/posts/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Tạo tên file mới ngẫu nhiên để tránh trùng
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('post_') . '.' . $extension;
        $targetFile = $uploadDir . $newFileName;

        // Di chuyển file vào thư mục đích
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Lưu đường dẫn tương đối để insert vào DB
            $imageUrl = 'images/posts/' . $newFileName; 
        } else {
            die("<h3 style='color:red'>Lỗi: Không thể lưu file ảnh! Kiểm tra quyền ghi thư mục.</h3>");
        }
    } else {
        die("<h3 style='color:red'>Lỗi: Vui lòng chọn ảnh đại diện.</h3>");
    }

    // 4. INSERT VÀO DATABASE
    try {
        $sql = "INSERT INTO articles (title, category_id, summary, content, image_url, author_id, created_at, views) 
                VALUES (:t, :c, :s, :content, :img, :auth, NOW(), 0)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':t' => $title,
            ':c' => $cat_id,
            ':s' => $summary,
            ':content' => $content,
            ':img' => $imageUrl,
            ':auth' => $author_id
        ]);

        // Thông báo thành công và quay về trang chủ
        echo "<script>
            alert('Đã thêm bài viết thành công!'); 
            window.location.href='../index.php';
        </script>";

    } catch (PDOException $e) {
        echo "<h3 style='color:red'>Lỗi Insert CSDL: " . $e->getMessage() . "</h3>";
    }
}
?>
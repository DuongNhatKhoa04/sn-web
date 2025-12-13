<?php
require_once '../classes/Database.php';

// Kiểm tra xem có phải người dùng bấm nút Submit không (POST method)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Kết nối Database
    $db = new Database();
    $conn = $db->getConnection();

    // 2. Nhận dữ liệu từ form gửi sang (Dùng isset để tránh lỗi nếu thiếu dữ liệu)
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $cat_id = isset($_POST['category_id']) ? $_POST['category_id'] : 1;
    $summary = isset($_POST['summary']) ? $_POST['summary'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : ''; // Nội dung HTML từ CKEditor
    $author_id = 1; // Mặc định Admin (ID = 1) là người viết

    // 3. XỬ LÝ UPLOAD ẢNH (Phần này quan trọng)
    $imageUrl = '';
    
    // Kiểm tra xem có file ảnh gửi lên không và không có lỗi
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../images/posts/'; // Thư mục đích để lưu ảnh
        
        // Tạo thư mục nếu chưa có (Để tránh lỗi không tìm thấy đường dẫn)
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Lấy đuôi file (vd: .jpg, .png)
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        // Tạo tên file mới ngẫu nhiên (vd: post_65a1b2.jpg) để không bị trùng tên file cũ
        $newFileName = uniqid('post_') . '.' . $extension;
        $targetFile = $uploadDir . $newFileName;

        // Di chuyển file từ bộ nhớ tạm (tmp) vào thư mục images/posts
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Lưu đường dẫn "tương đối" để sau này hiển thị lên web
            $imageUrl = 'images/posts/' . $newFileName; 
        } else {
            die("<h3 style='color:red'>Lỗi: Không thể lưu file ảnh vào server!</h3>");
        }
    } else {
        die("<h3 style='color:red'>Lỗi: Bạn chưa chọn ảnh đại diện.</h3>");
    }

    // 4. LƯU VÀO DATABASE (INSERT)
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

        // Thông báo thành công bằng Javascript và quay về trang chủ
        echo "<script>
            alert('Đã thêm bài viết thành công!'); 
            window.location.href='../index.php';
        </script>";

    } catch (PDOException $e) {
        echo "<h3 style='color:red'>Lỗi lưu Database: " . $e->getMessage() . "</h3>";
    }
}
?>
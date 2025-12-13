<?php
require_once '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $id = $_POST['id'];
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $summary = $_POST['summary'];
    $content = $_POST['content'];
    
    // Mặc định giữ ảnh cũ
    $imageUrl = $_POST['current_image'];

    // Nếu người dùng có chọn ảnh mới thì Upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $dir = '../images/posts/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = uniqid('post_') . '.' . $ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $newName)) {
            $imageUrl = 'images/posts/' . $newName;
        }
    }

    // Cập nhật Database
    try {
        $sql = "UPDATE articles 
                SET title = ?, category_id = ?, summary = ?, content = ?, image_url = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$title, $cat_id, $summary, $content, $imageUrl, $id]);

        echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
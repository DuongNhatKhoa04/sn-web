<?php
// admin/toggle_banner.php
session_start();

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../classes/Database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Tạo đường dẫn chuẩn quy ước cho Banner
        $targetLink = "pages/detail.php?id=" . $id;

        // 2. Kiểm tra xem bài viết này đã có trong bảng banners chưa?
        // Chúng ta so sánh chính xác đường dẫn link_url
        $stmt = $conn->prepare("SELECT id FROM banners WHERE link_url = ?");
        $stmt->execute([$targetLink]);
        $banner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($banner) {
            // A. NẾU ĐÃ CÓ -> XÓA KHỎI BANNER (TẮT)
            $delStmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
            $delStmt->execute([$banner['id']]);
        } else {
            // B. NẾU CHƯA CÓ -> THÊM VÀO BANNER (BẬT)
            // Lấy thông tin bài viết để copy sang bảng banners
            $artStmt = $conn->prepare("SELECT title, image_url FROM articles WHERE id = ?");
            $artStmt->execute([$id]);
            $article = $artStmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                $sqlInsert = "INSERT INTO banners (image_url, title, link_url, display_order, is_active) 
                              VALUES (?, ?, ?, 1, 1)";
                $insStmt = $conn->prepare($sqlInsert);
                $insStmt->execute([
                    $article['image_url'], 
                    $article['title'], 
                    $targetLink
                ]);
            }
        }

        // Xử lý xong thì quay lại trang danh sách
        header("Location: index.php"); 

    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
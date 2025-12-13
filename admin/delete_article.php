<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

require_once '../classes/Database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db = new Database();
    $conn = $db->getConnection();

    try {
        // 1. Xóa Banner liên quan đến bài viết này (nếu có)
        // Link banner thường dạng: pages/detail.php?id=15
        $linkToDelete = "%id=" . $id; 
        $stmtBanner = $conn->prepare("DELETE FROM banners WHERE link_url LIKE ?");
        $stmtBanner->execute([$linkToDelete]);

        // 2. Xóa bài viết (Comment/Like sẽ tự xóa theo nhờ CASCADE)
        $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: index.php"); 
    } catch (Exception $e) {
        echo "Lỗi khi xóa: " . $e->getMessage();
    }
}
?>
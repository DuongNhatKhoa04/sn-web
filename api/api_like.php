<?php
// File: api/api_like.php
header('Content-Type: application/json');

// Cấu hình Database
$host = 'localhost'; $db = 's_news_db'; $user = 'root'; $pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối DB']);
    exit;
}

// Lấy dữ liệu
$data = json_decode(file_get_contents("php://input"), true);
$articleId = isset($data['id']) ? (int)$data['id'] : 0;
$action    = isset($data['action']) ? $data['action'] : 'like';

if ($articleId > 0) {
    if ($action === 'unlike') {
        // SỬA LỖI Ở ĐÂY: Chỉ trừ like nếu số like > 0 để tránh lỗi số âm
        $stmt = $conn->prepare("UPDATE articles SET likes = likes - 1 WHERE id = :id AND likes > 0");
    } else {
        // Tăng like
        $stmt = $conn->prepare("UPDATE articles SET likes = likes + 1 WHERE id = :id");
    }
    
    if ($stmt->execute([':id' => $articleId])) {
        // Lấy số like mới để trả về
        $stmt = $conn->prepare("SELECT likes FROM articles WHERE id = :id");
        $stmt->execute([':id' => $articleId]);
        $newCount = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'new_likes' => $newCount]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật DB']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
}
?>
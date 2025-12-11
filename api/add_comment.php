<?php
require_once '../classes/Database.php';
header('Content-Type: application/json'); // Báo cho trình duyệt biết kết quả trả về là JSON (dạng dữ liệu máy tính đọc được)

// Chỉ chấp nhận gửi bằng phương thức POST (bảo mật hơn GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
    exit;
}

// Lấy dữ liệu gửi lên
$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Kiểm tra xem có điền thiếu thông tin không
if ($article_id == 0 || empty($username) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ thông tin']);
    exit;
}

try {
    // Mở kết nối Database
    $database = new Database();
    $db = $database->getConnection();
    // Chuẩn bị câu lệnh chèn (INSERT) bình luận mới vào bảng comments
    $stmt = $db->prepare("INSERT INTO comments (article_id, username, content) VALUES (?, ?, ?)");
    $stmt->execute([$article_id, $username, $content]);
    // Báo thành công
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    // Báo lỗi nếu có trục trặc
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
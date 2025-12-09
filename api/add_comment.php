<?php
require_once '../classes/Database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
    exit;
}

$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if ($article_id == 0 || empty($username) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ thông tin']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("INSERT INTO comments (article_id, username, content) VALUES (?, ?, ?)");
    $stmt->execute([$article_id, $username, $content]);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
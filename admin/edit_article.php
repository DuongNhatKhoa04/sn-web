<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

require_once '../classes/Database.php';
$db = new Database();
$conn = $db->getConnection();

// Lấy ID bài viết cần sửa
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin bài viết cũ
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Bài viết không tồn tại!");
}

// Lấy danh mục
$cats = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa bài viết</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow p-4">
            <div class="d-flex justify-content-between mb-4">
                <h4 class="text-primary fw-bold">Chỉnh Sửa Bài Viết #<?php echo $id; ?></h4>
                <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </div>
            
            <form action="process_edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo $article['image_url']; ?>">

                <div class="mb-3">
                    <label class="fw-bold">Tiêu đề:</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Danh mục:</label>
                        <select name="category_id" class="form-select">
                            <?php foreach($cats as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>" <?php echo ($c['category_id'] == $article['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo $c['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Ảnh đại diện (Chọn nếu muốn đổi):</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Ảnh hiện tại: <?php echo basename($article['image_url']); ?></small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Tóm tắt:</label>
                    <textarea name="summary" class="form-control" rows="3" required><?php echo htmlspecialchars($article['summary']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Nội dung:</label>
                    <textarea name="content" id="editor" class="form-control" required><?php echo $article['content']; ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold">LƯU THAY ĐỔI</button>
            </form>
        </div>
    </div>
    <script>CKEDITOR.replace('editor');</script>
</body>
</html>
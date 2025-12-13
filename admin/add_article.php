<?php
// admin/add_article.php
require_once '../classes/Database.php';
// Kết nối DB để lấy danh sách danh mục đưa vào thẻ <select>
$conn = Database::getInstance()->getConnection();
$cats = $conn->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm bài viết (Admin)</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Thêm Bài Viết Mới</h4>
            </div>
            <div class="card-body">
                <form action="process_add.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề:</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục:</label>
                            <select name="category_id" class="form-select">
                                <?php foreach($cats as $c): ?>
                                    <option value="<?= $c['category_id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ảnh đại diện (Upload):</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tóm tắt:</label>
                        <textarea name="summary" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung chi tiết (CK Editor):</label>
                        <textarea name="content" id="content_editor" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">Đăng bài</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Thay thế textarea có id="content_editor" bằng giao diện CKEditor
        CKEDITOR.replace('content_editor');
    </script>
</body>
</html>
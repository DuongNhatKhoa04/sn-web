<?php
// admin/add_article.php
session_start(); // 1. Khởi động session

// --- CỔNG BẢO VỆ: CHẶN KHÔNG CHO VÀO NẾU CHƯA ĐĂNG NHẬP ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Chưa đăng nhập hoặc không phải admin -> Đá về trang Login
    header("Location: login.php");
    exit();
}
// -----------------------------------------------------------

require_once '../classes/Database.php';

// Kết nối CSDL
$db = new Database();
$conn = $db->getConnection();

// Lấy danh mục... (Code cũ giữ nguyên)
$cats = [];
try {
    $stmt = $conn->query("SELECT * FROM categories");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $cats = []; }
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1 fw-bold">S-NEWS ADMIN</span>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-white">Xin chào, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
                <a href="logout.php" class="btn btn-light btn-sm fw-bold text-primary">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h4 class="mb-0 text-primary fw-bold"><i class="fa-solid fa-pen-nib"></i> Thêm Bài Viết Mới</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="process_add.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tiêu đề bài viết:</label>
                                <input type="text" name="title" class="form-control form-control-lg" placeholder="Nhập tiêu đề..." required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Danh mục:</label>
                                    <select name="category_id" class="form-select">
                                        <?php if (!empty($cats)): ?>
                                            <?php foreach($cats as $c): ?>
                                                <?php 
                                                    // Logic hiển thị tên danh mục thông minh
                                                    $catName = "Không tên";
                                                    if (isset($c['name'])) $catName = $c['name'];
                                                    elseif (isset($c['cat_name'])) $catName = $c['cat_name'];
                                                    
                                                    $catID = isset($c['category_id']) ? $c['category_id'] : (isset($c['id']) ? $c['id'] : 0);
                                                ?>
                                                <option value="<?php echo $catID; ?>"><?php echo $catName; ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">Không có danh mục nào</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Ảnh đại diện (Upload):</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tóm tắt ngắn:</label>
                                <textarea name="summary" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Nội dung chi tiết (CK Editor):</label>
                                <textarea name="content" id="content_editor" class="form-control" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">ĐĂNG BÀI VIẾT</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        CKEDITOR.replace('content_editor');
    </script>
</body>
</html>
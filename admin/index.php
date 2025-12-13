<?php
session_start();
// Cổng bảo vệ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../classes/Database.php';
$db = new Database();
$conn = $db->getConnection();

// Lấy danh sách bài viết (kèm tên danh mục)
$sql = "SELECT a.*, c.name as cat_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.category_id 
        ORDER BY a.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bài viết</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold">ADMIN DASHBOARD</span>
            <div class="d-flex gap-3 align-items-center">
                <span class="text-white">Chào, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
                <a href="../index.php" class="btn btn-sm btn-outline-light">Xem Web</a>
                <a href="logout.php" class="btn btn-sm btn-light text-primary fw-bold">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary fw-bold">Danh Sách Bài Viết</h3>
            <a href="add_article.php" class="btn btn-success fw-bold"><i class="fa-solid fa-plus me-1"></i> Thêm bài mới</a>
        </div>

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Hình ảnh</th>
                            <th style="width: 40%;">Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Ngày đăng</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?php echo $row['id']; ?></td>
                            <td>
                                <img src="../<?php echo $row['image_url']; ?>" 
                                     style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='https://placehold.co/60x40'">
                            </td>
                            <td>
                                <a href="../pages/detail.php?id=<?php echo $row['id']; ?>" target="_blank" class="text-decoration-none fw-bold text-dark">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </td>
                            <td><span class="badge bg-info text-dark"><?php echo $row['cat_name'] ?? 'Chưa phân loại'; ?></span></td>
                            <td class="text-muted small"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td class="text-end pe-4">
                                <a href="edit_article.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1" title="Sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="delete_article.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa bài này không?');" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
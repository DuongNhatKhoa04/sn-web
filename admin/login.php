<?php
// Bắt đầu phiên làm việc (để lưu trạng thái đăng nhập)
session_start();
require_once '../classes/Database.php';

// Nếu người dùng ĐÃ đăng nhập rồi (có lưu trong session) -> Đuổi thẳng vào trang thêm bài
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    header("Location: index.php");
    exit();
}

$error = ''; // Biến chứa thông báo lỗi (nếu có)

// Khi người dùng bấm nút "Đăng nhập" (Gửi form POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Lấy tên đăng nhập người dùng nhập
    $password = $_POST['password']; // Lấy mật khẩu người dùng nhập

    // Kết nối CSDL để kiểm tra
    $db = new Database();
    $conn = $db->getConnection();

    // Tìm trong bảng users xem có ai tên như vậy không
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nếu tìm thấy User VÀ Mật khẩu trùng khớp
    if ($user && $user['password'] == $password) {
        // Kiểm tra xem có phải là Admin không?
        if ($user['role'] == 'admin') {
            // ĐÚNG LÀ ADMIN -> Lưu thông tin vào Session (như đóng dấu vào tay khi đi xem ca nhạc)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Chuyển hướng sang trang thêm bài viết
            header("Location: index.php");
            exit();
        } else {
            $error = "Bạn là User thường, không được vào Admin!";
        }
    } else {
        $error = "Sai tài khoản hoặc mật khẩu rồi!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Căn giữa màn hình */
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="card login-card shadow border-0">
        <h3 class="text-center text-primary mb-4 fw-bold">S-NEWS ADMIN</h3>
        
        <?php if ($error): ?>
            <div class="alert alert-danger text-center p-2 mb-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Tài khoản</label>
                <input type="text" name="username" class="form-control" placeholder="Nhập tài khoản" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">ĐĂNG NHẬP</button>
        </form>
        <div class="text-center mt-3">
            <a href="../index.php" class="text-decoration-none text-muted small">← Quay về trang chủ</a>
        </div>
    </div>
</body>
</html>
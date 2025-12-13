<?php
// admin/login.php
session_start();
require_once '../classes/Database.php';

// Nếu đã đăng nhập rồi thì đá về trang thêm bài viết luôn
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    header("Location: add_article.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kết nối CSDL
    $db = new Database();
    $conn = $db->getConnection();

    // Kiểm tra tài khoản
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra mật khẩu (So sánh trực tiếp vì trong DB bạn đang lưu plain text '123456')
    if ($user && $user['password'] == $password) {
        // Kiểm tra quyền (chỉ cho role 'admin' vào)
        if ($user['role'] == 'admin') {
            // Đăng nhập thành công -> Lưu vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: add_article.php");
            exit();
        } else {
            $error = "Bạn không có quyền truy cập Admin!";
        }
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
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
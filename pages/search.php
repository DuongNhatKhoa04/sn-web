<?php
// KẾT NỐI DB
$conn = new PDO("mysql:host=localhost;dbname=s_news_db;charset=utf8mb4", 'root', '');

// LOGIC TÌM KIẾM & PHÂN TRANG
$keyword = $_GET['q'] ?? '';
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit   = 5; // Số bài mỗi trang
$offset  = ($page - 1) * $limit;

$articles = [];
$totalPages = 0;

if ($keyword) {
    // 1. Lấy danh sách bài viết
    $sql = "SELECT * FROM articles 
            WHERE title LIKE :kw OR summary LIKE :kw 
            ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Đếm tổng để chia trang
    $sqlCount = "SELECT COUNT(*) FROM articles WHERE title LIKE :kw OR summary LIKE :kw";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmtCount->execute();
    $totalRecords = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm tin tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="container py-5">

    <form action="" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Nhập từ khóa..." value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
        </div>
    </form>

    <h4 class="mb-4">Kết quả tìm kiếm: "<strong><?= htmlspecialchars($keyword) ?></strong>"</h4>

    <div class="list-group mb-4">
        <?php foreach ($articles as $row): ?>
            <div class="list-group-item p-3">
                <div class="d-flex gap-3">
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" style="width: 150px; height: 100px; object-fit: cover;" class="rounded">
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-1">
                                <a href="article_detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($row['title']) ?>
                                </a>
                            </h5>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                        </div>
                        <p class="mb-2 text-muted"><?= htmlspecialchars($row['summary']) ?></p>
                        
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary"><?= htmlspecialchars($row['category']) ?></span>
                            
                            <button class="btn btn-sm btn-outline-danger border-0 like-btn" 
                                    data-id="<?= $row['id'] ?>" onclick="toggleLike(this)">
                                <i class="fa-regular fa-heart"></i> 
                                <span class="like-count"><?= $row['likes'] ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($articles) && $keyword): ?>
            <div class="alert alert-warning">Không tìm thấy bài viết nào.</div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <script>
    function toggleLike(btn) {
        const articleId = btn.getAttribute('data-id');
        const icon = btn.querySelector('i');
        const countSpan = btn.querySelector('.like-count');

        // Gửi request lên server (api_like.php)
        fetch('api_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: articleId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật số like mới
                countSpan.innerText = data.new_likes;
                // Đổi icon thành tim đặc (đã like)
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                // Hiệu ứng
                btn.classList.add('active');
            }
        })
        .catch(err => console.error('Lỗi:', err));
    }
    </script>

</body>
</html>
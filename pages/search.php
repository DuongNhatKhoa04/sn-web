<?php
// Lưu ý: Đường dẫn require cần chỉnh lại vì file này nằm trong thư mục pages/
require_once '../classes/BasePage.php';
require_once '../classes/Database.php';

class SearchPage extends BasePage {
    protected function renderBody() {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit   = 5; 
        $offset  = ($page - 1) * $limit;

        $db = new Database();
        $conn = $db->getConnection();
        
        $articles = [];
        $totalRecords = 0;
        $totalPages = 0;

        if ($keyword) {
            // 1. Tìm kiếm + Phân trang
            $sql = "SELECT * FROM articles 
                    WHERE title LIKE :kw OR summary LIKE :kw 
                    ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. Đếm tổng
            $sqlCount = "SELECT COUNT(*) FROM articles WHERE title LIKE :kw OR summary LIKE :kw";
            $stmtCount = $conn->prepare($sqlCount);
            $stmtCount->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
            $stmtCount->execute();
            $totalRecords = $stmtCount->fetchColumn();
            $totalPages = ceil($totalRecords / $limit);
        }
        ?>

        <div class="container py-4">
            <form action="search.php" method="GET" class="mb-5 shadow-sm p-4 bg-white rounded">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" class="form-control border-primary" 
                           placeholder="Nhập từ khóa tìm kiếm..." value="<?= htmlspecialchars($keyword) ?>">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Tìm kiếm
                    </button>
                </div>
            </form>

            <h4 class="mb-4">Kết quả tìm kiếm: "<strong><?= htmlspecialchars($keyword) ?></strong>" <small class="text-muted">(<?= $totalRecords ?> bài)</small></h4>

            <div class="list-group list-group-flush">
                <?php if (count($articles) > 0): ?>
                    <?php foreach ($articles as $row): 
                        $likes = isset($row['likes']) ? $row['likes'] : 0;
                    ?>
                        <div class="list-group-item border-0 shadow-sm mb-3 rounded p-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <img src="../<?= htmlspecialchars($row['image_url'] ?? 'images/no-image.jpg') ?>" 
                                         class="img-fluid rounded" style="width:100%; height:150px; object-fit:cover;">
                                </div>
                                <div class="col-md-9 d-flex flex-column">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="mb-1 fw-bold">
                                            <a href="detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($row['title']) ?>
                                            </a>
                                        </h5>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-2 text-muted"><?= htmlspecialchars($row['summary']) ?></p>
                                    
                                    <div class="mt-auto d-flex align-items-center gap-3">
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category']) ?></span>
                                        
                                        <button class="btn btn-sm btn-outline-danger border-0 like-btn" 
                                                onclick="toggleLike(this, <?= $row['id'] ?>)">
                                            <i class="fa-regular fa-heart"></i> 
                                            <span class="like-count"><?= $likes ?></span>
                                        </button>
                                        
                                        <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary ms-auto">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($keyword): ?>
                    <div class="alert alert-warning text-center">Không tìm thấy bài viết nào phù hợp.</div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>">Trước</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>">Sau</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php
    }
}

$page = new SearchPage("Tìm kiếm - SNews", true);
$page->render();
?>
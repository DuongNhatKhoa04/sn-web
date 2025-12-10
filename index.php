<?php
require_once 'classes/BasePage.php';
require_once 'classes/Article.php';

class HomePage extends BasePage {
    protected function renderBody() {
        // ============================================================
        // 1. LOGIC PHP: LẤY DỮ LIỆU
        // ============================================================
        
        // A. LẤY BANNER TỪ DATABASE (Code Mới thêm vào)
        $banners = [];
        try {
            // Kết nối nhanh để lấy banner (sử dụng thông tin mặc định: root, rỗng)
            $connBanner = new PDO("mysql:host=localhost;dbname=s_news_db;charset=utf8mb4", 'root', '');
            $connBanner->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Lấy 5 banner đang bật, sắp xếp theo thứ tự
            $stmt = $connBanner->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC LIMIT 5");
            $stmt->execute();
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Nếu lỗi kết nối thì để mảng rỗng, web vẫn chạy bình thường
            $banners = [];
        }

        // B. LẤY DANH SÁCH BÀI VIẾT & PHÂN TRANG (Code Cũ của bạn)
        $limit = 6;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $limit;

        $articleModel = new Article();
        $listArticles = $articleModel->getPaginated($limit, $offset);
        $totalArticles = $articleModel->getTotalCount();
        $totalPages = ceil($totalArticles / $limit);
        ?>
        
        <div class="row mb-5 align-items-center">
            <div class="col-lg-12">
                <?php if (!empty($banners)): ?>
                    <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                        
                        <div class="carousel-indicators">
                            <?php foreach ($banners as $i => $banner): ?>
                                <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="<?= $i ?>" 
                                        class="<?= ($i === 0) ? 'active' : '' ?>" aria-current="true"></button>
                            <?php endforeach; ?>
                        </div>

                        <div class="carousel-inner">
                            <?php foreach ($banners as $index => $banner): ?>
                                <div class="carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                                    <a href="<?= htmlspecialchars($banner['link_url']) ?>">
                                        <img src="<?= htmlspecialchars($banner['image_url']) ?>" 
                                             class="d-block w-100" 
                                             style="height: 450px; object-fit: cover;" 
                                             alt="<?= htmlspecialchars($banner['title']) ?>">
                                        
                                        <?php if (!empty($banner['title'])): ?>
                                        <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 8px;">
                                            <h5><?= htmlspecialchars($banner['title']) ?></h5>
                                        </div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary text-center">Chưa có banner hiển thị.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold m-0" style="color: #6a6a6a;">
                <i class="fa-solid fa-star text-warning me-2"></i>TIN MỚI NHẤT
            </h3>
            <div class="ms-3 flex-grow-1" style="height: 2px; background: linear-gradient(90deg, var(--color-accent), transparent);"></div>
        </div>

        <div class="row">
        <?php
            if (!empty($listArticles)) {
                foreach ($listArticles as $row) {
                    $link = "pages/detail.php?id=" . $row['id'];
                    $img = $this->getImageUrl($row['image_url']);
                    
                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="overflow-hidden" style="height: 200px;">
                                <a href="'.$link.'">
                                    <img src="'.$img.'" class="card-img-top h-100 w-100" style="object-fit: cover; transition: transform 0.3s;" alt="'.$row['title'].'">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge">'.$row['category'].'</span>
                                    <small class="text-muted ms-2"><i class="fa-regular fa-clock"></i> '.date('d/m', strtotime($row['created_at'])).'</small>
                                </div>
                                <h5 class="card-title">
                                    <a href="'.$link.'" class="text-decoration-none">'.$row['title'].'</a>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1">'.substr($row['summary'], 0, 90).'...</p>
                                <div class="mt-3 text-end">
                                    <a href="'.$link.'" class="btn btn-primary btn-sm rounded-pill px-3">
                                        Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12 text-center py-5 text-muted">Chưa có bài viết nào.</div>';
            }
        ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-2 anim-up delay-2">
            <ul class="pagination justify-content-center">
                
                <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php
    }
}

$page = new HomePage("Trang chủ - SNews", true);
$page->render();
?>
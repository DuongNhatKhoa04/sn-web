<?php
require_once 'classes/BasePage.php';
require_once 'classes/Article.php';

class HomePage extends BasePage {
    protected function renderBody() {
        // --- 1. XỬ LÝ PHÂN TRANG ---
        $limit = 6; // Số bài hiển thị trên 1 trang
        // Lấy trang hiện tại từ URL (ví dụ: index.php?page=2), mặc định là 1
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        // Tính vị trí bắt đầu lấy dữ liệu (Offset)
        $offset = ($currentPage - 1) * $limit;

        // Gọi Model
        $articleModel = new Article();
        // Lấy danh sách bài viết cho trang hiện tại
        $listArticles = $articleModel->getPaginated($limit, $offset);
        // Tính tổng số trang
        $totalArticles = $articleModel->getTotalCount();
        $totalPages = ceil($totalArticles / $limit);

        // --- 2. PHẦN GIAO DIỆN SLIDER (Giữ nguyên) ---
        ?>
        <div class="row mb-5 align-items-center">
            <div class="col-lg-12">
                <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="https://placehold.co/1200x450/C4D9FF/ffffff?text=Chao+Mung+S-NEWS" class="d-block w-100" alt="Slide 1">
                        </div>
                        <div class="carousel-item">
                            <img src="https://placehold.co/1200x450/C5BAFF/ffffff?text=Tin+Tuc+Moi+Nhat" class="d-block w-100" alt="Slide 2">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold m-0" style="color: #6a6a6a;">
                <i class="fa-solid fa-star text-warning me-2"></i>TIN MỚI NHẤT
            </h3>
            <div class="ms-3 flex-grow-1" style="height: 2px; background: linear-gradient(90deg, var(--accent-blue), transparent);"></div>
        </div>

        <div class="row">
        <?php
            if (count($listArticles) > 0) {
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
                echo '<div class="col-12 text-center py-5 text-muted">Chưa có bài viết nào trong CSDL.</div>';
            }
        ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5" style="animation: fadeInUp 1.2s ease-out;">
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
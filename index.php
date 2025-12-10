<?php
require_once 'classes/BasePage.php';
require_once 'classes/Article.php';

class HomePage extends BasePage {
    protected function renderBody() {
        // ============================================================
        // 1. LẤY DỮ LIỆU
        // ============================================================
        
        // A. Banner
        $banners = [];
        try {
            $connBanner = new PDO("mysql:host=localhost;dbname=s_news_db;charset=utf8mb4", 'root', '');
            $connBanner->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $connBanner->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC LIMIT 5");
            $stmt->execute();
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { $banners = []; }

        // B. Bài viết
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
                                <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="<?= $i ?>" class="<?= ($i === 0) ? 'active' : '' ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($banners as $index => $banner): ?>
                                <?php 
                                    $bannerImg = $this->getImageUrl($banner['image_url']); 
                                    $bannerLink = str_replace('article_detail.php', 'pages/detail.php', $banner['link_url']);
                                ?>
                                <div class="carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                                    <a href="<?= htmlspecialchars($bannerLink) ?>">
                                        <img src="<?= $bannerImg ?>" class="d-block w-100" style="height: 450px; object-fit: cover;">
                                        <?php if (!empty($banner['title'])): ?>
                                        <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 8px;">
                                            <h5><?= htmlspecialchars($banner['title']) ?></h5>
                                        </div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary text-center">Chưa có banner.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold m-0" style="color: #6a6a6a;"><i class="fa-solid fa-star text-warning me-2"></i>TIN MỚI NHẤT</h3>
            <div class="ms-3 flex-grow-1" style="height: 2px; background: linear-gradient(90deg, var(--color-accent), transparent);"></div>
        </div>

        <div class="row">
        <?php if (!empty($listArticles)) {
            foreach ($listArticles as $row) {
                $link = "pages/detail.php?id=" . $row['id'];
                $img = $this->getImageUrl($row['image_url']);
                $likes = isset($row['likes']) ? $row['likes'] : 0;
                
                echo '
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="overflow-hidden" style="height: 200px;">
                            <a href="'.$link.'"><img src="'.$img.'" class="card-img-top h-100 w-100" style="object-fit: cover;"></a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><a href="'.$link.'" class="text-decoration-none text-dark fw-bold">'.$row['title'].'</a></h5>
                            <p class="card-text text-muted small flex-grow-1">'.substr($row['summary'], 0, 90).'...</p>
                            <div class="mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                                
                                <button class="btn btn-outline-danger btn-sm border-0 like-btn" onclick="toggleLike(this, '.$row['id'].')">
                                    <i class="fa-regular fa-heart"></i> <span class="like-count fw-bold ms-1">'.$likes.'</span>
                                </button>

                                <a href="'.$link.'" class="btn btn-primary btn-sm rounded-pill px-3">Xem <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>"><a class="page-link" href="?page=<?php echo $currentPage - 1; ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>"><a class="page-link" href="?page=<?php echo $currentPage + 1; ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
            </ul>
        </nav>
        <?php endif; ?>

        <script>
        // Key lưu trữ riêng biệt
        const STORAGE_KEY = 'snews_liked_v3'; 

        function toggleLike(btn, articleId) {
            var $btn = $(btn);
            
            // 1. Xác định trạng thái hiện tại từ LocalStorage
            var rawList = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            var likedList = rawList.map(Number); // Chuyển về số
            articleId = parseInt(articleId);

            var isLiked = likedList.includes(articleId);
            var action = isLiked ? 'unlike' : 'like';

            // 2. CẬP NHẬT UI NGAY LẬP TỨC (Không chờ Server, không Disable nút)
            var currentCount = parseInt($btn.find('.like-count').text()) || 0;
            
            if (action === 'like') {
                // Hiệu ứng Like
                $btn.removeClass('btn-outline-danger').addClass('btn-danger');
                $btn.find('i').removeClass('fa-regular').addClass('fa-solid');
                $btn.css('color', 'white');
                $btn.find('.like-count').text(currentCount + 1);
            } else {
                // Hiệu ứng Unlike
                $btn.removeClass('btn-danger').addClass('btn-outline-danger');
                $btn.find('i').removeClass('fa-solid').addClass('fa-regular');
                $btn.css('color', '');
                $btn.find('.like-count').text(Math.max(0, currentCount - 1));
            }

            // 3. Cập nhật LocalStorage NGAY (để nếu user bấm nhanh quá vẫn ăn logic)
            if (action === 'like') {
                if (!likedList.includes(articleId)) likedList.push(articleId);
            } else {
                likedList = likedList.filter(id => id !== articleId);
            }
            localStorage.setItem(STORAGE_KEY, JSON.stringify(likedList));

            // 4. Gửi Request ngầm
            $.ajax({
                url: 'api/api_like.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: articleId, action: action }),
                success: function(response) {
                    if (response.success) {
                        // Đồng bộ lại số like chuẩn từ server
                        $btn.find('.like-count').text(response.new_likes);
                    } else {
                        // Nếu server lỗi -> Rollback UI (rất hiếm khi xảy ra)
                        console.error('API Error');
                    }
                },
                error: function() {
                    console.error('Network Error');
                }
            });
        }

        // Tự động tô màu khi load trang
        $(document).ready(function() {
            var likedList = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]').map(Number);
            $('.like-btn').each(function() {
                var onclickVal = $(this).attr('onclick'); 
                if (onclickVal) {
                    var match = onclickVal.match(/\d+/);
                    if (match) {
                        var id = parseInt(match[0]);
                        if (likedList.includes(id)) {
                            var $btn = $(this);
                            $btn.removeClass('btn-outline-danger').addClass('btn-danger');
                            $btn.find('i').removeClass('fa-regular').addClass('fa-solid');
                            $btn.css('color', 'white');
                        }
                    }
                }
            });
        });
        </script>
        <?php
    }
}
$page = new HomePage("Trang chủ - SNews", true);
$page->render();
?>
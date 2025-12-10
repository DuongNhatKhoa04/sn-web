<?php
require_once 'classes/BasePage.php';
require_once 'classes/Article.php';

class HomePage extends BasePage {
    protected function renderBody() {
        // ============================================================
        // 1. LOGIC PHP: LẤY DỮ LIỆU BANNER & BÀI VIẾT
        // ============================================================
        
        // A. LẤY 5 BANNER TỪ DATABASE
        $banners = [];
        try {
            $connBanner = new PDO("mysql:host=localhost;dbname=s_news_db;charset=utf8mb4", 'root', '');
            $connBanner->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $connBanner->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC LIMIT 5");
            $stmt->execute();
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $banners = [];
        }

        // B. LẤY BÀI VIẾT PHÂN TRANG
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
                                <?php 
                                    // 1. Xử lý ảnh: dùng hàm getImageUrl của BasePage
                                    $bannerImg = $this->getImageUrl($banner['image_url']); 
                                    
                                    // 2. Sửa lỗi Link: đổi article_detail.php -> pages/detail.php
                                    $bannerLink = $banner['link_url'];
                                    $bannerLink = str_replace('article_detail.php', 'pages/detail.php', $bannerLink);
                                ?>

                                <div class="carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                                    <a href="<?= htmlspecialchars($bannerLink) ?>">
                                        <img src="<?= $bannerImg ?>" 
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
                    <div class="alert alert-secondary text-center">Chưa có banner nào. Hãy kiểm tra database.</div>
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
                    $likes = isset($row['likes']) ? $row['likes'] : 0;
                    
                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="overflow-hidden" style="height: 200px; position: relative;">
                                <a href="'.$link.'">
                                    <img src="'.$img.'" class="card-img-top h-100 w-100" style="object-fit: cover; transition: transform 0.3s;" alt="'.$row['title'].'">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">'.$row['category'].'</span>
                                        <small class="text-muted ms-2"><i class="fa-regular fa-clock"></i> '.date('d/m', strtotime($row['created_at'])).'</small>
                                    </div>
                                </div>
                                <h5 class="card-title">
                                    <a href="'.$link.'" class="text-decoration-none text-dark fw-bold">'.$row['title'].'</a>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1">'.substr($row['summary'], 0, 90).'...</p>
                                
                                <div class="mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                                    <button class="btn btn-outline-danger btn-sm border-0 like-btn" 
                                            onclick="toggleLike(this, '.$row['id'].')">
                                        <i class="fa-regular fa-heart"></i> 
                                        <span class="like-count fw-bold ms-1">'.$likes.'</span>
                                    </button>

                                    <a href="'.$link.'" class="btn btn-primary btn-sm rounded-pill px-3">
                                        Xem <i class="fa-solid fa-arrow-right ms-1"></i>
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
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>"><i class="fa-solid fa-chevron-left"></i></a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>"><i class="fa-solid fa-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <script>
        // Hàm xử lý Like/Unlike khi bấm nút
        function toggleLike(btn, articleId) {
            var $btn = $(btn);
            var storageKey = 'liked_articles';
            var likedList = JSON.parse(localStorage.getItem(storageKey) || '[]');
            var isLiked = likedList.includes(articleId);
            var action = isLiked ? 'unlike' : 'like'; // Đảo ngược trạng thái

            $btn.prop('disabled', true); // Khóa nút khi đang xử lý

            $.ajax({
                url: 'api/api_like.php', // Gọi API
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: articleId, action: action }),
                success: function(response) {
                    if (response.success) {
                        // 1. Cập nhật số lượng like hiển thị
                        $btn.find('.like-count').text(response.new_likes);

                        // 2. Cập nhật localStorage
                        if (action === 'like') {
                            likedList.push(articleId);
                        } else {
                            likedList = likedList.filter(id => id !== articleId);
                        }
                        localStorage.setItem(storageKey, JSON.stringify(likedList));

                        // 3. Cập nhật màu sắc nút
                        updateLikeButtonUI($btn, action === 'like');
                    }
                    $btn.prop('disabled', false);
                },
                error: function() {
                    alert('Lỗi kết nối!');
                    $btn.prop('disabled', false);
                }
            });
        }

        // Hàm cập nhật giao diện nút (Icon đặc/rỗng, Màu đỏ/trắng)
        function updateLikeButtonUI($btn, isLiked) {
            var $icon = $btn.find('i');
            if (isLiked) {
                $btn.removeClass('btn-outline-danger').addClass('btn-danger'); // Nền đỏ
                $icon.removeClass('fa-regular').addClass('fa-solid'); // Tim đặc
                $btn.css('color', 'white');
            } else {
                $btn.removeClass('btn-danger').addClass('btn-outline-danger'); // Viền đỏ
                $icon.removeClass('fa-solid').addClass('fa-regular'); // Tim rỗng
                $btn.css('color', ''); // Reset màu chữ
            }
        }

        // Khi load trang xong: Kiểm tra localStorage để tô màu các bài đã like
        $(document).ready(function() {
            var likedList = JSON.parse(localStorage.getItem('liked_articles') || '[]');
            
            $('.like-btn').each(function() {
                // Lấy ID từ thuộc tính onclick="toggleLike(this, 123)"
                var onclickVal = $(this).attr('onclick'); 
                if(onclickVal) {
                    var match = onclickVal.match(/\d+/);
                    if(match) {
                        var id = parseInt(match[0]);
                        // Nếu ID này có trong danh sách đã like -> update giao diện
                        if (likedList.includes(id)) {
                            updateLikeButtonUI($(this), true);
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
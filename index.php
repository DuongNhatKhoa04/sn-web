<?php
// --- BƯỚC 1: NẠP CÁC FILE CẦN THIẾT ---
// Gọi file BasePage.php để lấy cái khung (Header, Footer)
require_once 'classes/BasePage.php';
// Gọi file Article.php để có công cụ lấy bài viết từ kho
require_once 'classes/Article.php';

// Tạo trang chủ kế thừa từ khung sườn BasePage
class HomePage extends BasePage {
    
    // Hàm này chịu trách nhiệm vẽ nội dung chính (phần thân trang web)
    protected function renderBody() {
        
        // --- A. LẤY DỮ LIỆU BANNER (ẢNH TRƯỢT) ---
        $banners = []; // Tạo một cái giỏ rỗng để chứa banner
        try {
            // 1. Tự mở kết nối riêng vào kho dữ liệu (Database)
            $connBanner = new PDO("mysql:host=localhost;dbname=s_news_db;charset=utf8mb4", 'root', '');
            // Thiết lập chế độ: Có lỗi là báo ngay
            $connBanner->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 2. Soạn lệnh: "Lấy 5 banner đang bật (is_active=1), sắp xếp theo thứ tự"
            $stmt = $connBanner->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC LIMIT 5");
            
            // 3. Thực thi lệnh
            $stmt->execute();
            
            // 4. Lấy tất cả kết quả bỏ vào giỏ
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { 
            // Nếu có lỗi (ví dụ sai mật khẩu DB) thì để giỏ rỗng, không làm sập web
            $banners = []; 
        }

        // --- B. LẤY DANH SÁCH BÀI VIẾT (TIN TỨC) ---
        $limit = 6; // Quy định: Mỗi trang chỉ hiện 6 bài
        
        // Xem trên thanh địa chỉ web xem đang ở trang mấy (VD: ?page=2)
        // Nếu không có thì mặc định là trang 1
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        
        // Tính toán: Trang 2 thì phải bỏ qua 6 bài đầu (offset)
        $offset = ($currentPage - 1) * $limit; 
        
        // Gọi "người quản lý bài viết" (Article Model) ra làm việc
        $articleModel = new Article();
        
        // Nhờ lấy danh sách bài viết theo phân trang
        $listArticles = $articleModel->getPaginated($limit, $offset);
        
        // Nhờ đếm tổng số bài viết đang có trong kho
        $totalArticles = $articleModel->getTotalCount();
        
        // Tính xem cần bao nhiêu trang để hiển thị hết (VD: 20 bài / 6 = 4 trang)
        $totalPages = ceil($totalArticles / $limit); 
        ?>
        
        <main class="container">
            
            <section id="featured-news" class="row mb-5 align-items-center">
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
                                        // Tìm đường dẫn ảnh (nếu lỗi thì lấy ảnh mặc định)
                                        $bannerImg = $this->getImageUrl($banner['image_url']); 
                                        // Sửa đường dẫn link bài viết cho đúng
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
            </section>

            <div class="d-flex align-items-center mb-4">
                <h3 class="fw-bold m-0" style="color: #6a6a6a;"><i class="fa-solid fa-star text-warning me-2"></i>TIN MỚI NHẤT</h3>
                <div class="ms-3 flex-grow-1" style="height: 2px; background: linear-gradient(90deg, var(--color-accent), transparent);"></div>
            </div>

            <section id="latest-news" class="row">
            <?php if (!empty($listArticles)) {
                // Duyệt qua từng bài viết để in ra màn hình
                foreach ($listArticles as $row) {
                    $link = "pages/detail.php?id=" . $row['id']; // Link xem chi tiết
                    $img = $this->getImageUrl($row['image_url']); // Link ảnh
                    $likes = isset($row['likes']) ? $row['likes'] : 0; // Số lượt thích
                    
                    // Lấy tên danh mục (Ưu tiên tên đã JOIN bảng, nếu không có thì lấy tên cũ)
                    $catName = !empty($row['cat_name']) ? $row['cat_name'] : $row['category'];
                    $catIcon = !empty($row['cat_icon']) ? $row['cat_icon'] : 'fa-solid fa-folder';
                    $colorClass = 'text-light';
                    $bgClass = str_replace('text-', 'bg-', $colorClass); 

                    // In ra thẻ bài viết (Card HTML)
                    echo '
                    <article class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="overflow-hidden" style="height: 200px;">
                                <a href="'.$link.'"><img src="'.$img.'" class="card-img-top h-100 w-100" style="object-fit: cover;"></a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge '.$bgClass.' bg-opacity-10 '.$colorClass.'">
                                            <i class="'.$catIcon.' me-1"></i> '.$catName.'
                                        </span>
                                        <small class="text-muted ms-2"><i class="fa-regular fa-clock"></i> '.date('d/m', strtotime($row['created_at'])).'</small>
                                    </div>
                                </div>
                                
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
                    </article>';
                }
            } ?>
            </section>

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
        </main>

        <script>
        const STORAGE_KEY = 'snews_liked_final'; 

        function toggleLike(btn, articleId) {
            var $btn = $(btn);
            // Lấy danh sách đã like từ trình duyệt
            var rawList = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            var likedList = rawList.map(Number);
            articleId = parseInt(articleId);

            // Kiểm tra xem đã like chưa
            var isLiked = likedList.includes(articleId);
            var action = isLiked ? 'unlike' : 'like'; 
            var currentCount = parseInt($btn.find('.like-count').text()) || 0;
            
            // Đổi màu nút ngay lập tức cho mượt
            if (action === 'like') {
                $btn.removeClass('btn-outline-danger').addClass('btn-danger'); 
                $btn.find('i').removeClass('fa-regular').addClass('fa-solid'); 
                $btn.css('color', 'white');
                $btn.find('.like-count').text(currentCount + 1); 
            } else {
                $btn.removeClass('btn-danger').addClass('btn-outline-danger'); 
                $btn.find('i').removeClass('fa-solid').addClass('fa-regular');
                $btn.css('color', '');
                $btn.find('.like-count').text(Math.max(0, currentCount - 1)); 
            }

            // Lưu lại vào trình duyệt
            if (action === 'like') {
                if (!likedList.includes(articleId)) likedList.push(articleId);
            } else {
                likedList = likedList.filter(id => id !== articleId);
            }
            localStorage.setItem(STORAGE_KEY, JSON.stringify(likedList));

            // Gửi tin hiệu ngầm (AJAX) về Server để lưu vào Database
            $.ajax({
                url: 'api/api_like.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: articleId, action: action }),
                success: function(response) {
                    if (response.success) {
                        $btn.find('.like-count').text(response.new_likes);
                    }
                }
            });
        }

        // Khi tải trang xong, kiểm tra bài nào đã like thì tô đỏ nút
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
// Chạy trang web
$page = new HomePage("Trang chủ - SNews", true);
$page->render();
?>
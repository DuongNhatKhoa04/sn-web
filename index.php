<?php
// --- BƯỚC 1: NẠP CÁC FILE CẦN THIẾT ---
// Gọi file BasePage.php để lấy cấu trúc khung sườn (Header, Footer)
require_once 'classes/BasePage.php';
// Gọi file Article.php để sử dụng các hàm lấy bài viết từ CSDL
require_once 'classes/Article.php';

// Tạo lớp HomePage kế thừa từ BasePage
class HomePage extends BasePage {
    
    // Hàm này chịu trách nhiệm vẽ nội dung chính (phần thân trang web)
    protected function renderBody() {
        
        // --- A. LẤY DỮ LIỆU BANNER (ẢNH TRƯỢT) ---
        $banners = []; // Tạo một danh sách rỗng để chứa banner
        try {
            // 1. Tự mở kết nối riêng vào kho dữ liệu (Database) để lấy banner
            // (Lưu ý: Nếu file Database.php đổi user/pass thì đoạn này cũng cần cập nhật theo)
            $connBanner = new PDO("mysql:host=localhost;dbname=s_news_db;charset=utf8mb4", 'root', '');
            // Thiết lập chế độ báo lỗi ngay lập tức nếu sai truy vấn
            $connBanner->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 2. Soạn lệnh: "Lấy 5 banner đang bật (is_active=1), sắp xếp theo thứ tự ưu tiên"
            $stmt = $connBanner->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC LIMIT 5");
            
            // 3. Thực thi lệnh và lấy kết quả
            $stmt->execute();
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { 
            // Nếu có lỗi kết nối thì gán mảng rỗng để web không bị chết
            $banners = []; 
        }

        // --- B. LẤY DANH SÁCH BÀI VIẾT (TIN TỨC) ---
        $limit = 6; // Quy định: Mỗi trang chỉ hiện đúng 6 bài
        
        // Kiểm tra xem người dùng đang ở trang mấy (VD: index.php?page=2)
        // Nếu không có tham số page thì mặc định là trang 1
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        
        // Tính toán vị trí bắt đầu lấy tin (Offset)
        // VD: Trang 1 -> offset 0. Trang 2 -> offset 6 (bỏ qua 6 bài đầu)
        $offset = ($currentPage - 1) * $limit; 
        
        // Khởi tạo đối tượng Article để xử lý dữ liệu bài viết
        $articleModel = new Article();
        
        // Nhờ hàm getPaginated lấy danh sách bài theo trang
        $listArticles = $articleModel->getPaginated($limit, $offset);
        
        // Đếm tổng số bài viết đang có trong kho để tính số trang
        $totalArticles = $articleModel->getTotalCount();
        
        // Tính tổng số trang (dùng hàm ceil để làm tròn lên, VD: 6.1 -> 7 trang)
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
                                        // Xử lý đường dẫn ảnh (nếu ảnh lỗi sẽ dùng ảnh mặc định trong hàm getImageUrl)
                                        $bannerImg = $this->getImageUrl($banner['image_url']); 
                                        // Sửa đường dẫn link bài viết cho đúng chuẩn thư mục pages/
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
                        <div class="p-5 text-center bg-primary text-white rounded-3 shadow-sm" 
                             style="background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
                            <h1 class="display-3 fw-bold mb-3">S-NEWS</h1>
                            <p class="lead mb-0">Trang tin tức tổng hợp nhanh nhất, chính xác nhất.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <div class="d-flex align-items-center mb-4">
                <h3 class="fw-bold m-0" style="color: #6a6a6a;"><i class="fa-solid fa-star text-warning me-2"></i>TIN MỚI NHẤT</h3>
                <div class="ms-3 flex-grow-1" style="height: 2px; background: linear-gradient(90deg, var(--color-accent), transparent);"></div>
            </div>

            <section id="latest-news" class="row">
            <?php if (!empty($listArticles)) {
                // Duyệt qua từng bài viết để in ra thẻ HTML
                foreach ($listArticles as $row) {
                    $link = "pages/detail.php?id=" . $row['id']; // Link xem chi tiết
                    $img = $this->getImageUrl($row['image_url']); // Link ảnh
                    $likes = isset($row['likes']) ? $row['likes'] : 0; // Số lượt thích hiện tại
                    
                    // Lấy thông tin danh mục (Ưu tiên tên lấy từ bảng categories)
                    $catName = !empty($row['cat_name']) ? $row['cat_name'] : $row['category'];
                    $catIcon = !empty($row['cat_icon']) ? $row['cat_icon'] : 'fa-solid fa-folder';
                    
                    // Xử lý màu sắc hiển thị
                    $colorClass = 'text-light';
                    $bgClass = str_replace('text-', 'bg-', $colorClass); 

                    // In ra cấu trúc thẻ bài viết (Card)
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

        // Hàm xử lý sự kiện khi bấm nút Tim
        function toggleLike(btn, articleId) {
            var $btn = $(btn);
            // Lấy danh sách các bài đã like từ bộ nhớ trình duyệt
            var rawList = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            var likedList = rawList.map(Number);
            articleId = parseInt(articleId);

            // Kiểm tra: Nếu ID bài viết đã có trong danh sách -> Hành động là 'unlike'
            var isLiked = likedList.includes(articleId);
            var action = isLiked ? 'unlike' : 'like'; 
            
            // Lấy số like hiện tại trên giao diện
            var currentCount = parseInt($btn.find('.like-count').text()) || 0;
            
            // Cập nhật giao diện NGAY LẬP TỨC (để người dùng thấy mượt mà)
            if (action === 'like') {
                $btn.removeClass('btn-outline-danger').addClass('btn-danger'); // Tô đỏ
                $btn.find('i').removeClass('fa-regular').addClass('fa-solid'); // Đổi icon đặc
                $btn.css('color', 'white');
                $btn.find('.like-count').text(currentCount + 1); // Tăng số
            } else {
                $btn.removeClass('btn-danger').addClass('btn-outline-danger'); // Bỏ đỏ
                $btn.find('i').removeClass('fa-solid').addClass('fa-regular'); // Đổi icon rỗng
                $btn.css('color', '');
                $btn.find('.like-count').text(Math.max(0, currentCount - 1)); // Giảm số
            }

            // Lưu trạng thái mới vào bộ nhớ trình duyệt (LocalStorage)
            if (action === 'like') {
                if (!likedList.includes(articleId)) likedList.push(articleId);
            } else {
                likedList = likedList.filter(id => id !== articleId);
            }
            localStorage.setItem(STORAGE_KEY, JSON.stringify(likedList));

            // Gửi yêu cầu ngầm (AJAX) về Server để lưu vào Database
            $.ajax({
                url: 'api/api_like.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: articleId, action: action }),
                success: function(response) {
                    // Nếu server trả về số like chính xác, cập nhật lại lần nữa cho chắc
                    if (response.success) {
                        $btn.find('.like-count').text(response.new_likes);
                    }
                }
            });
        }

        window.addEventListener('load', function() {
            var likedList = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]').map(Number);
            
            // Duyệt qua tất cả các nút like đang hiển thị trên màn hình
            $('.like-btn').each(function() {
                var onclickVal = $(this).attr('onclick'); 
                if (onclickVal) {
                    // Lấy ID bài viết từ thuộc tính onclick="toggleLike(this, 15)"
                    var match = onclickVal.match(/\d+/); 
                    if (match) {
                        var id = parseInt(match[0]);
                        // Nếu bài này đã được like trước đó -> Tô đỏ nút ngay lập tức
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

// KHỞI CHẠY TRANG CHỦ
$page = new HomePage("Trang chủ - SNews", true);
$page->render();
?>
<?php
require_once '../classes/BasePage.php';
require_once '../classes/Article.php';

class SearchPage extends BasePage {
    protected function renderBody() {
        // Lấy từ khóa từ URL
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        echo '<div class="mb-4">';
        echo '<h2 class="fw-bold">Kết quả tìm kiếm cho: "<span class="text-primary">' . htmlspecialchars($keyword) . '</span>"</h2>';
        echo '</div>';

        if (empty($keyword)) {
            echo '<div class="alert alert-warning">Vui lòng nhập từ khóa để tìm kiếm.</div>';
            return;
        }

        // Gọi hàm search mới trong Article
        $articleModel = new Article();
        $results = $articleModel->search($keyword);

        echo '<div class="row">';
        if (!empty($results)) {
            foreach ($results as $row) {
                $link = "detail.php?id=" . $row['id'];
                $img = $this->getImageUrl($row['image_url']);
                $likes = isset($row['likes']) ? $row['likes'] : 0;

                // XỬ LÝ TAG DANH MỤC (Giống trang chủ)
                $catName = !empty($row['cat_name']) ? $row['cat_name'] : $row['category'];
                $catIcon = !empty($row['cat_icon']) ? $row['cat_icon'] : 'fa-solid fa-folder';
                $colorClass = !empty($row['cat_color']) ? $row['cat_color'] : 'text-primary';
                $bgClass = str_replace('text-', 'bg-', $colorClass); 

                echo '
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="overflow-hidden" style="height: 200px;">
                            <a href="'.$link.'"><img src="'.$img.'" class="card-img-top h-100 w-100" style="object-fit: cover;"></a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge '.$bgClass.' bg-opacity-10 text-dark border border-'.$colorClass.'">
                                        <i class="'.$catIcon.' me-1 '.$colorClass.'"></i> '.$catName.'
                                    </span>
                                    <small class="text-muted ms-2"><i class="fa-regular fa-clock"></i> '.date('d/m', strtotime($row['created_at'])).'</small>
                                </div>
                            </div>
                            
                            <h5 class="card-title"><a href="'.$link.'" class="text-decoration-none text-dark fw-bold">'.$row['title'].'</a></h5>
                            <p class="card-text text-muted small flex-grow-1">'.substr($row['summary'], 0, 90).'...</p>
                            
                            <div class="mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                                <button class="btn btn-outline-danger btn-sm border-0 like-btn" onclick="toggleLikeDetail(this, '.$row['id'].')">
                                    <i class="fa-regular fa-heart"></i> <span class="like-count fw-bold ms-1">'.$likes.'</span>
                                </button>
                                <a href="'.$link.'" class="btn btn-primary btn-sm rounded-pill px-3">Xem <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12 text-center py-5 text-muted">
                    <i class="fa-solid fa-magnifying-glass fa-3x mb-3 text-secondary"></i>
                    <h4>Không tìm thấy bài viết nào phù hợp.</h4>
                    <p>Hãy thử tìm với từ khóa khác nhé!</p>
                  </div>';
        }
        echo '</div>';
        
        // --- JAVASCRIPT XỬ LÝ LIKE CHO TRANG SEARCH ---
        // (Logic giống hệt trang detail vì cùng nằm trong thư mục pages/)
        ?>
        <script>
        const STORAGE_KEY_SEARCH = 'snews_liked_final'; 

        function toggleLikeDetail(btn, articleId) {
            var $btn = $(btn);
            var rawList = JSON.parse(localStorage.getItem(STORAGE_KEY_SEARCH) || '[]');
            var likedList = rawList.map(Number);
            articleId = parseInt(articleId);

            var isLiked = likedList.includes(articleId);
            var action = isLiked ? 'unlike' : 'like';
            var currentCount = parseInt($btn.find('.like-count').text()) || 0;

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

            if (action === 'like') {
                if (!likedList.includes(articleId)) likedList.push(articleId);
            } else {
                likedList = likedList.filter(id => id !== articleId);
            }
            localStorage.setItem(STORAGE_KEY_SEARCH, JSON.stringify(likedList));

            $.ajax({
                url: '../api/api_like.php', // Dùng ../ vì ở trong pages/
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

        // Tự động tô màu nút Like khi load trang
        $(document).ready(function() {
            var likedList = JSON.parse(localStorage.getItem(STORAGE_KEY_SEARCH) || '[]').map(Number);
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

$page = new SearchPage("Tìm kiếm");
$page->render();
?>
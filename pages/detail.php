<?php
require_once '../classes/BasePage.php';
// --- SỬA LỖI: Nhúng file Article.php ngay tại đây ---
require_once '../classes/Article.php'; 

class DetailPage extends BasePage {
    protected function renderBody() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Khởi tạo Model
        $articleModel = new Article();
        $article = $articleModel->getById($id);

        if (!$article) {
            echo '<div class="alert alert-danger text-center my-5">Bài viết không tồn tại!</div>';
            return;
        }

        // Tăng view mỗi khi vào xem
        $this->db->query("UPDATE articles SET views = views + 1 WHERE id = $id");
        
        // Xử lý dữ liệu hiển thị
        $img = $this->getImageUrl($article['image_url']);
        $likes = isset($article['likes']) ? $article['likes'] : 0;
        
        // Dữ liệu Tag danh mục (Fallback nếu null)
        $catName = !empty($article['cat_name']) ? $article['cat_name'] : $article['category'];
        $catIcon = !empty($article['cat_icon']) ? $article['cat_icon'] : 'fa-solid fa-folder';
        $colorClass = !empty($article['cat_color']) ? $article['cat_color'] : 'text-primary';
        $bgClass = str_replace('text-', 'bg-', $colorClass); 

        // Lấy Bình luận
        $cmtStmt = $this->db->prepare("SELECT * FROM comments WHERE article_id = ? ORDER BY created_at DESC");
        $cmtStmt->execute([$id]);
        $comments = $cmtStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <nav aria-label="breadcrumb" class="mb-4 animate__animated animate__fadeInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><?php echo $catName; ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="fw-bold mb-3 display-5 animate__animated animate__fadeInUp"><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <div class="d-flex align-items-center mb-4 border-bottom pb-3 text-muted animate__animated animate__fadeInUp delay-1">
                    <span class="badge <?php echo $bgClass; ?> bg-opacity-25 text-dark border border-<?php echo $colorClass; ?> me-3 px-3 py-2 rounded-pill">
                        <i class="<?php echo $catIcon; ?> me-1 <?php echo $colorClass; ?>"></i> <?php echo $catName; ?>
                    </span>
                    <div class="me-3"><i class="fa-regular fa-clock me-1"></i> <?php echo date('d/m/Y', strtotime($article['created_at'])); ?></div>
                    <div class="me-3"><i class="fa-regular fa-eye me-1"></i> <?php echo $article['views']; ?> xem</div>
                    
                    <button id="btnLikeDetail" class="btn btn-outline-danger btn-sm rounded-pill d-flex align-items-center gap-2 like-btn shadow-sm" 
                            onclick="toggleLikeDetail(this, <?php echo $id; ?>)">
                        <i class="fa-regular fa-heart"></i> 
                        <span id="textLike">Thích</span>
                        <span class="badge bg-danger text-white ms-1 like-count"><?php echo $likes; ?></span>
                    </button>
                </div>

                <div class="text-center mb-4 animate__animated animate__fadeInUp delay-2">
                    <img src="<?php echo $img; ?>" class="img-fluid rounded-4 shadow w-100" style="max-height: 500px; object-fit: cover;" alt="Minh hoạ">
                </div>

                <div class="article-content fs-5 animate__animated animate__fadeInUp delay-2" style="line-height: 1.8; text-align: justify; color: #2c3e50;">
                    <p class="fw-bold fs-4 fst-italic border-start border-4 border-primary ps-3 bg-light py-2 rounded-end">
                        <?php echo $article['summary']; ?>
                    </p>
                    <div class="mt-4">
                        <?php echo nl2br($article['content']); ?>
                    </div>
                </div>
                
                <div class="mt-5 text-end fst-italic text-muted">
                    <p>— Ban biên tập S-News —</p>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 rounded-4 shadow-sm border">
                    <h4 class="mb-4 fw-bold text-primary"><i class="fa-regular fa-comments me-2"></i>Bình luận (<span id="cmtCount"><?php echo count($comments); ?></span>)</h4>
                    
                    <form id="commentForm" class="mb-5">
                        <input type="hidden" id="article_id" value="<?php echo $id; ?>">
                        <div class="mb-3">
                            <input type="text" id="username" class="form-control form-control-lg bg-light border-0" placeholder="Tên của bạn (Bắt buộc)" required>
                        </div>
                        <div class="mb-3">
                            <textarea id="content" class="form-control form-control-lg bg-light border-0" rows="3" placeholder="Chia sẻ ý kiến của bạn..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold hover-scale">
                            <i class="fa-solid fa-paper-plane me-1"></i> Gửi bình luận
                        </button>
                    </form>

                    <div id="commentList">
                        <?php foreach ($comments as $cmt): ?>
                            <div class="d-flex border-bottom py-3 animate__animated animate__fadeIn">
                                <div class="flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($cmt['username']); ?>&background=random&color=fff" class="rounded-circle shadow-sm" width="50">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($cmt['username']); ?></h6>
                                    <small class="text-muted d-block mb-2"><i class="fa-regular fa-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($cmt['created_at'])); ?></small>
                                    <p class="mb-0 text-secondary bg-light p-3 rounded-3 d-inline-block">
                                        <?php echo nl2br(htmlspecialchars($cmt['content'])); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        const STORAGE_KEY_DETAIL = 'snews_liked_final'; 

        function toggleLikeDetail(btn, articleId) {
            var $btn = $(btn);
            var rawList = JSON.parse(localStorage.getItem(STORAGE_KEY_DETAIL) || '[]');
            var likedList = rawList.map(Number);
            articleId = parseInt(articleId);

            var isLiked = likedList.includes(articleId);
            var action = isLiked ? 'unlike' : 'like';
            var currentCount = parseInt($btn.find('.like-count').text()) || 0;

            if (action === 'like') {
                updateDetailBtnUI($btn, true);
                $btn.find('.like-count').text(currentCount + 1);
            } else {
                updateDetailBtnUI($btn, false);
                $btn.find('.like-count').text(Math.max(0, currentCount - 1));
            }

            if (action === 'like') {
                if (!likedList.includes(articleId)) likedList.push(articleId);
            } else {
                likedList = likedList.filter(id => id !== articleId);
            }
            localStorage.setItem(STORAGE_KEY_DETAIL, JSON.stringify(likedList));

            $.ajax({
                url: '../api/api_like.php',
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

        function updateDetailBtnUI($btn, isLiked) {
            var $icon = $btn.find('i');
            var $text = $btn.find('#textLike');
            
            if (isLiked) {
                $btn.removeClass('btn-outline-danger').addClass('btn-danger');
                $icon.removeClass('fa-regular').addClass('fa-solid');
                $text.text('Đã thích');
                $btn.css('box-shadow', '0 5px 15px rgba(220, 53, 69, 0.4)');
            } else {
                $btn.removeClass('btn-danger').addClass('btn-outline-danger');
                $icon.removeClass('fa-solid').addClass('fa-regular');
                $text.text('Thích');
                $btn.css('box-shadow', 'none');
            }
        }

        $(document).ready(function() {
            var likedList = JSON.parse(localStorage.getItem(STORAGE_KEY_DETAIL) || '[]').map(Number);
            var articleId = <?php echo $id; ?>;
            var $btn = $('#btnLikeDetail');

            if (likedList.includes(articleId)) {
                updateDetailBtnUI($btn, true);
            }
        });
        </script>
        <?php
    }
}
$page = new DetailPage("Chi tiết tin");
$page->render();
?>
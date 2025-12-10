<?php
require_once '../classes/BasePage.php';

class DetailPage extends BasePage {
    protected function renderBody() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            echo '<div class="alert alert-danger">Bài viết không tồn tại!</div>';
            return;
        }

        // Tăng view
        $this->db->query("UPDATE articles SET views = views + 1 WHERE id = $id");
        $img = $this->getImageUrl($article['image_url']);
        
        // Lấy Comments
        $cmtStmt = $this->db->prepare("SELECT * FROM comments WHERE article_id = ? ORDER BY created_at DESC");
        $cmtStmt->execute([$id]);
        $comments = $cmtStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($article['category']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <div class="d-flex align-items-center text-muted mb-4 border-bottom pb-3">
                    <div class="me-3"><i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($article['created_at'])); ?></div>
                    <div class="me-3"><i class="fa-regular fa-eye"></i> <?php echo $article['views']; ?> xem</div>
                    
                    <button id="btnLikeDetail" class="btn btn-outline-danger btn-sm rounded-pill d-flex align-items-center gap-2" data-id="<?php echo $id; ?>">
                        <i class="fa-regular fa-heart" id="iconHeart"></i> 
                        <span id="textLike">Thích</span>
                        <span class="badge bg-danger text-white ms-1" id="likeCount"><?php echo isset($article['likes']) ? $article['likes'] : 0; ?></span>
                    </button>
                </div>

                <div class="text-center mb-4">
                    <img src="<?php echo $img; ?>" class="img-fluid rounded shadow w-100" alt="Minh hoạ">
                </div>

                <div class="article-content fs-5" style="line-height: 1.8; text-align: justify;">
                    <p class="fw-bold"><?php echo $article['summary']; ?></p>
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 rounded shadow-sm">
                    <h4 class="mb-4">Bình luận (<span id="cmtCount"><?php echo count($comments); ?></span>)</h4>
                    <form id="commentForm" class="mb-5">
                        <input type="hidden" id="article_id" value="<?php echo $id; ?>">
                        <div class="mb-3">
                            <input type="text" id="username" class="form-control" placeholder="Tên của bạn" required>
                        </div>
                        <div class="mb-3">
                            <textarea id="content" class="form-control" rows="3" placeholder="Viết bình luận..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Gửi</button>
                    </form>

                    <div id="commentList">
                        <?php foreach ($comments as $cmt): ?>
                            <div class="d-flex border-bottom py-3">
                                <div class="flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($cmt['username']); ?>&background=random" class="rounded-circle" width="50">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($cmt['username']); ?></h6>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($cmt['created_at'])); ?></small>
                                    <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($cmt['content'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        $(document).ready(function(){
            var articleId = $('#btnLikeDetail').data('id');
            var storageKey = 'liked_articles';

            // Hàm kiểm tra xem bài này đã like chưa (trong localStorage)
            function isLiked(id) {
                var likedList = JSON.parse(localStorage.getItem(storageKey) || '[]');
                return likedList.includes(id);
            }

            // Hàm cập nhật giao diện nút ban đầu
            function updateButtonState() {
                if (isLiked(articleId)) {
                    $('#btnLikeDetail').removeClass('btn-outline-danger').addClass('btn-danger'); // Nền đỏ
                    $('#iconHeart').removeClass('fa-regular').addClass('fa-solid'); // Tim đặc
                    $('#textLike').text('Đã thích');
                } else {
                    $('#btnLikeDetail').removeClass('btn-danger').addClass('btn-outline-danger'); // Viền đỏ
                    $('#iconHeart').removeClass('fa-solid').addClass('fa-regular'); // Tim rỗng
                    $('#textLike').text('Thích');
                }
            }

            // Chạy hàm cập nhật ngay khi vào trang
            updateButtonState();

            // Sự kiện Click
            $('#btnLikeDetail').click(function(){
                var btn = $(this);
                var currentAction = isLiked(articleId) ? 'unlike' : 'like'; // Xác định hành động ngược lại

                // Disable nút để tránh spam click
                btn.prop('disabled', true);

                $.ajax({
                    url: '../api/api_like.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: articleId, action: currentAction }),
                    success: function(response){
                        if(response.success){
                            // Cập nhật số like hiển thị
                            $('#likeCount').text(response.new_likes);

                            // Cập nhật localStorage
                            var likedList = JSON.parse(localStorage.getItem(storageKey) || '[]');
                            if (currentAction === 'like') {
                                likedList.push(articleId); // Thêm ID vào danh sách
                            } else {
                                likedList = likedList.filter(id => id !== articleId); // Xóa ID khỏi danh sách
                            }
                            localStorage.setItem(storageKey, JSON.stringify(likedList));

                            // Cập nhật giao diện nút
                            updateButtonState();
                        } else {
                            alert('Lỗi: ' + response.message);
                        }
                        btn.prop('disabled', false);
                    },
                    error: function(){
                        alert('Lỗi kết nối server');
                        btn.prop('disabled', false);
                    }
                });
            });

            // Xử lý gửi Comment (Giữ nguyên code cũ nếu cần)
            // ...
        });
        </script>
        <?php
    }
}
$page = new DetailPage("Chi tiết tin");
$page->render();
?>
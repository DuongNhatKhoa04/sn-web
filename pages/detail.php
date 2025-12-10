<?php
// File: pages/detail.php
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
        $img = $this->getImageUrl($article['image_url']); // Hàm này xử lý đường dẫn ảnh
        
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
                <div class="text-muted mb-4 border-bottom pb-3">
                    <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($article['created_at'])); ?> 
                    <span class="mx-2">|</span> 
                    <i class="fa-regular fa-eye"></i> <?php echo $article['views']; ?> xem
                    
                    <span class="mx-2">|</span>
                    <span class="text-danger fw-bold">
                        <i class="fa-solid fa-heart"></i> <span id="likeCount"><?php echo isset($article['likes']) ? $article['likes'] : 0; ?></span> thích
                    </span>
                </div>

                <div class="text-center mb-4 position-relative">
                    <img src="<?php echo $img; ?>" class="img-fluid rounded shadow" alt="Minh hoa">
                    
                    <button id="btnLike" class="btn btn-danger position-absolute bottom-0 end-0 m-3 rounded-pill shadow" data-id="<?php echo $id; ?>">
                        <i class="fa-regular fa-thumbs-up"></i> Thả tim
                    </button>
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
            $('#btnLike').click(function(){
                var articleId = $(this).data('id');
                var btn = $(this);
                
                // Hiệu ứng bấm nút
                btn.prop('disabled', true); 

                $.ajax({
                    url: '../api/api_like.php', // Đảm bảo đường dẫn đúng tới file api bạn đã tạo
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: articleId }),
                    success: function(response){
                        if(response.success){
                            // Cập nhật số like mới
                            $('#likeCount').text(response.new_likes);
                            btn.html('<i class="fa-solid fa-check"></i> Đã thích');
                            btn.removeClass('btn-danger').addClass('btn-success');
                        } else {
                            alert('Lỗi: ' + (response.message || 'Không thể like'));
                            btn.prop('disabled', false);
                        }
                    },
                    error: function(){
                        alert('Có lỗi xảy ra khi kết nối server');
                        btn.prop('disabled', false);
                    }
                });
            });
            
            // Code xử lý comment cũ của bạn có thể để ở đây...
        });
        </script>
        <?php
    }
}
$page = new DetailPage("Chi tiết tin");
$page->render();
?>
<?php
require_once '../classes/BasePage.php';

class CategoryPage extends BasePage {
    protected function renderBody() {
        // Lấy tên danh mục từ URL (VD: category.php?cat=Thể Thao)
        $catName = isset($_GET['cat']) ? $_GET['cat'] : '';

        // Nếu không có tên danh mục thì báo lỗi
        if (empty($catName)) {
            echo '<div class="alert alert-danger">Không tìm thấy danh mục yêu cầu.</div>';
            return;
        }

        echo '<h2 class="mb-4 border-bottom pb-2">Chuyên mục: <span class="text-success">'.htmlspecialchars($catName).'</span></h2>';
        
        // --- CÂU LỆNH SQL QUAN TRỌNG ---
        // Kết nối bảng articles (a) và categories (c) bằng category_id
        // Sau đó lọc (WHERE) những dòng mà tên danh mục (c.name) trùng với yêu cầu
        $sql = "SELECT a.* FROM articles a
                JOIN categories c ON a.category_id = c.category_id
                WHERE c.name = :name
                ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $catName]);
        
        echo '<div class="row">';
        // Nếu có bài viết nào tìm thấy
        if($stmt->rowCount() > 0) {
            // Lặp qua từng bài viết
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $img = $this->getImageUrl($row['image_url']);
                // In ra giao diện thẻ bài viết (Card)
                echo '
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm flex-row overflow-hidden">
                        <div style="width: 150px; flex-shrink: 0;">
                            <a href="detail.php?id='.$row['id'].'">
                                <img src="'.$img.'" class="h-100 w-100" style="object-fit: cover;">
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><a href="detail.php?id='.$row['id'].'" class="text-decoration-none text-dark fw-bold">'.$row['title'].'</a></h5>
                            <p class="card-text small text-muted flex-grow-1">'.substr($row['summary'], 0, 80).'...</p>
                            <div class="mt-2">
                                <a href="detail.php?id='.$row['id'].'" class="btn btn-sm btn-outline-primary rounded-pill">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-warning text-center">Chưa có bài viết nào trong mục "'.htmlspecialchars($catName).'".</div></div>';
        }
        echo '</div>';
    }
}

// Khởi tạo trang và vẽ giao diện
$catParam = isset($_GET['cat']) ? $_GET['cat'] : "Tin tức";
$page = new CategoryPage("Chuyên mục: " . $catParam);
$page->render();
?>
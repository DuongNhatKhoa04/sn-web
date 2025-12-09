<?php
require_once '../classes/BasePage.php';

class CategoryPage extends BasePage {
    protected function renderBody() {
        $cat = isset($_GET['cat']) ? $_GET['cat'] : 'Thời sự';
        echo '<h2 class="mb-4 border-bottom pb-2">Chuyên mục: <span class="text-success">'.htmlspecialchars($cat).'</span></h2>';
        
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$cat]);
        
        echo '<div class="row">';
        if($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $img = $this->getImageUrl($row['image_url']);
                echo '
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm flex-row overflow-hidden">
                        <img src="'.$img.'" class="card-img-left" style="width: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><a href="detail.php?id='.$row['id'].'" class="text-decoration-none">'.$row['title'].'</a></h5>
                            <p class="card-text small text-muted">'.substr($row['summary'], 0, 80).'...</p>
                            <a href="detail.php?id='.$row['id'].'" class="btn btn-sm btn-outline-primary">Xem</a>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-warning">Chưa có bài viết trong mục này.</div></div>';
        }
        echo '</div>';
    }
}
$page = new CategoryPage("Danh mục " . $_GET['cat']);
$page->render();
?>
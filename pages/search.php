<?php
require_once '../classes/BasePage.php';

class SearchPage extends BasePage {
    protected function renderBody() {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        echo '<h2 class="mb-4">Kết quả tìm kiếm: "<span class="text-danger">'.htmlspecialchars($keyword).'</span>"</h2>';
        
        $query = "SELECT * FROM articles WHERE title LIKE :key OR summary LIKE :key";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':key' => "%$keyword%"]);
        
        if ($stmt->rowCount() > 0) {
            echo '<div class="list-group">';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<a href="detail.php?id='.$row['id'].'" class="list-group-item list-group-item-action">';
                echo '<h5 class="mb-1 text-primary">'.$row['title'].'</h5>';
                echo '<p class="mb-1">'.$row['summary'].'</p>';
                echo '<small class="text-muted">Chuyên mục: '.$row['category'].'</small>';
                echo '</a>';
            }
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning">Không tìm thấy bài viết nào.</div>';
        }
    }
}
$page = new SearchPage("Tìm kiếm");
$page->render();
?>
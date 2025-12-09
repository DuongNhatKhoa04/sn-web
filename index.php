<?php
require_once 'classes/BasePage.php';

class HomePage extends BasePage {
    protected function renderBody() {
        // 1. Slider Ảnh (Dùng ảnh mẫu Online cho đẹp)
        ?>
        <div id="newsCarousel" class="carousel slide mb-5 shadow rounded overflow-hidden" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://placehold.co/1200x400/3498db/ffffff?text=Chao+Mung+Den+Voi+S-News" class="d-block w-100" alt="Slide 1">
                </div>
                <div class="carousel-item">
                    <img src="https://placehold.co/1200x400/2ecc71/ffffff?text=Cap+Nhat+Tin+Tuc+Sinh+Vien" class="d-block w-100" alt="Slide 2">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <h3 class="border-bottom pb-2 mb-4 text-primary"><i class="fa-solid fa-bolt"></i> Tin mới nhất</h3>
        <div class="row">
        <?php
            $stmt = $this->db->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT 6");
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $link = "pages/detail.php?id=" . $row['id'];
                    $img = $this->getImageUrl($row['image_url']);
                    
                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm hover-effect">
                            <img src="'.$img.'" class="card-img-top" alt="'.$row['title'].'" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-info text-dark mb-2">'.$row['category'].'</span>
                                <h5 class="card-title"><a href="'.$link.'" class="text-decoration-none text-dark">'.$row['title'].'</a></h5>
                                <p class="card-text text-muted small">'.substr($row['summary'], 0, 90).'...</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="fa-regular fa-eye"></i> '.$row['views'].'</small>
                                <a href="'.$link.'" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="alert alert-warning">Chưa có bài viết nào. Hãy thêm vào Database!</div>';
            }
        ?>
        </div>
        <?php
    }
}

// Tham số true báo hiệu đây là Root Folder
$page = new HomePage("Trang chủ - SNews", true);
$page->render();
?>
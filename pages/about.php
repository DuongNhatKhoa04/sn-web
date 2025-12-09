<?php
require_once '../classes/BasePage.php';

class AboutPage extends BasePage {
    protected function renderBody() {
        ?>
        <div class="card border-0 p-5 mb-5 shadow-sm" style="animation: fadeInUp 0.5s ease-out;">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h5 class="text-secondary text-uppercase ls-2 fw-bold mb-3">Về Dự Án</h5>
                    <h1 class="display-4 fw-bold mb-4" style="background: linear-gradient(45deg, var(--color-primary), var(--color-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        S-NEWS PROJECT
                    </h1>
                    <p class="lead text-dark mb-4">
                        S-News là trang tin tức điện tử nội bộ dành cho sinh viên, nơi cập nhật nhanh chóng các sự kiện, hoạt động và thông báo quan trọng của nhà trường.
                    </p>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted mb-3"><i class="fa-solid fa-layer-group me-2"></i>Công nghệ sử dụng:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary text-white p-2">
                                <i class="fa-brands fa-php fa-lg me-1"></i> PHP
                            </span>
                            
                            <span class="badge bg-secondary text-white p-2">
                                <i class="fa-solid fa-database me-1"></i> MySQL
                            </span>

                            <span class="badge bg-dark text-white p-2">
                                <i class="fa-brands fa-html5 me-1"></i> HTML / CSS
                            </span>

                            <span class="badge bg-info text-dark p-2 border border-info">
                                <i class="fa-brands fa-bootstrap me-1"></i> Bootstrap
                            </span>
                            
                            <span class="badge bg-warning text-dark p-2 border border-warning">
                                <i class="fa-brands fa-js me-1"></i> jQuery
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <img src="https://placehold.co/600x400/6C5DD3/ffffff?text=S-NEWS+Teamwork" class="img-fluid rounded-4 shadow-lg position-relative z-1" style="transform: rotate(2deg); border: 5px solid #fff;" alt="Team">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary rounded-4" style="transform: rotate(-3deg); opacity: 0.1; z-index: 0;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5 mt-5">
            <h2 class="fw-bold display-6">Đội Ngũ Sáng Tạo</h2>
            <p class="text-muted">Những người đứng sau sự thành công của dự án</p>
            <div style="width: 60px; height: 4px; background: linear-gradient(90deg, var(--color-primary), var(--color-secondary)); margin: 20px auto; border-radius: 2px;"></div>
        </div>

        <div class="row justify-content-center">
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center p-4 border-0 hover-shadow" style="animation: fadeInUp 0.8s ease-out; animation-delay: 0.1s;">
                    <div class="position-relative mx-auto mb-4" style="width: 140px; height: 140px;">
                        <?php 
                            $img1 = $this->getImageUrl("images/member1");
                            // Fallback nếu hàm getImageUrl trả về placeholder mặc định mà mình muốn avatar khác
                            if(strpos($img1, 'placehold.co') !== false) {
                                $img1 = "https://ui-avatars.com/api/?name=Nguyen+Van+A&background=6C5DD3&color=fff&size=150";
                            }
                        ?>
                        <img src="<?php echo $img1; ?>" class="rounded-circle w-100 h-100 shadow p-1 bg-white" style="object-fit: cover; border: 3px solid var(--color-primary);" alt="Member 1">
                        <span class="position-absolute bottom-0 end-0 bg-warning text-dark badge rounded-pill border border-white shadow-sm">Leader</span>
                    </div>
                    <h4 class="fw-bold mb-1">Nguyễn Văn A</h4>
                    <p class="text-primary fw-bold small mb-3">Fullstack Developer</p>
                    <div class="bg-light rounded-3 p-3 text-start small text-muted border-start border-4 border-primary">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fa-solid fa-check-circle text-primary me-2"></i>Thiết kế kiến trúc hệ thống</li>
                            <li class="mb-2"><i class="fa-solid fa-check-circle text-primary me-2"></i>Xây dựng CSDL (Database)</li>
                            <li><i class="fa-solid fa-check-circle text-primary me-2"></i>Code trang Chủ & Slider</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center p-4 border-0 hover-shadow" style="animation: fadeInUp 0.8s ease-out; animation-delay: 0.3s;">
                    <div class="position-relative mx-auto mb-4" style="width: 140px; height: 140px;">
                        <?php 
                            $img2 = $this->getImageUrl("images/member2.jpeg");
                            if(strpos($img2, 'placehold.co') !== false) {
                                $img2 = "https://ui-avatars.com/api/?name=Tran+Thi+B&background=FF758F&color=fff&size=150";
                            }
                        ?>
                        <img src="<?php echo $img2; ?>" class="rounded-circle w-100 h-100 shadow p-1 bg-white" style="object-fit: cover; border: 3px solid var(--color-secondary);" alt="Member 2">
                    </div>
                    <h4 class="fw-bold mb-1">Trần Thị B</h4>
                    <p class="text-secondary fw-bold small mb-3">Frontend & UI/UX</p>
                    <div class="bg-light rounded-3 p-3 text-start small text-muted border-start border-4 border-secondary">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fa-solid fa-check-circle text-secondary me-2"></i>Thiết kế giao diện (CSS)</li>
                            <li class="mb-2"><i class="fa-solid fa-check-circle text-secondary me-2"></i>Code trang Chi tiết tin</li>
                            <li><i class="fa-solid fa-check-circle text-secondary me-2"></i>Hiệu ứng Animation</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center p-4 border-0 hover-shadow" style="animation: fadeInUp 0.8s ease-out; animation-delay: 0.5s;">
                    <div class="position-relative mx-auto mb-4" style="width: 140px; height: 140px;">
                        <?php 
                            $img3 = $this->getImageUrl("images/member3.jpeg");
                            if(strpos($img3, 'placehold.co') !== false) {
                                $img3 = "https://ui-avatars.com/api/?name=Le+Van+C&background=00D2FF&color=fff&size=150";
                            }
                        ?>
                        <img src="<?php echo $img3; ?>" class="rounded-circle w-100 h-100 shadow p-1 bg-white" style="object-fit: cover; border: 3px solid var(--color-accent);" alt="Member 3">
                    </div>
                    <h4 class="fw-bold mb-1">Lê Văn C</h4>
                    <p class="text-info fw-bold small mb-3">Backend Developer</p>
                    <div class="bg-light rounded-3 p-3 text-start small text-muted border-start border-4 border-info">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fa-solid fa-check-circle text-info me-2"></i>Code trang Tìm kiếm</li>
                            <li class="mb-2"><i class="fa-solid fa-check-circle text-info me-2"></i>Xử lý phân loại Danh mục</li>
                            <li><i class="fa-solid fa-check-circle text-info me-2"></i>API Bình luận (Ajax)</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-dark text-white rounded-4 p-5 mt-5 text-center position-relative overflow-hidden" style="animation: fadeInUp 1s ease-out;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(45deg, rgba(108, 93, 211, 0.2), rgba(255, 117, 143, 0.2));"></div>
            
            <div class="position-relative z-1">
                <h3 class="fw-bold mb-3">Sẵn Sàng Hợp Tác?</h3>
                <p class="mb-4 text-white-50">Dự án luôn mở rộng để đón nhận đóng góp từ cộng đồng sinh viên.</p>
                <button class="btn btn-primary px-5 py-2 rounded-pill shadow-lg hover-scale">
                    <i class="fa-solid fa-paper-plane me-2"></i> Liên Hệ Ngay
                </button>
            </div>
        </div>
        <?php
    }
}

$page = new AboutPage("Giới thiệu dự án");
$page->render();
?>
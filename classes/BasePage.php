<?php
abstract class BasePage {
    protected $title; // Tiêu đề của trang (hiện trên tab trình duyệt)
    protected $db;    // Biến để dùng kết nối cơ sở dữ liệu
    protected $assets_folder; // Đường dẫn để tìm file ảnh/css (tránh lỗi khi ở thư mục con)

    // Hàm khởi tạo: Chạy đầu tiên khi trang web được bật lên
    public function __construct($title = "S-News", $is_root = false) {
        $this->title = $title;
        // Nếu là trang chủ (root) thì đường dẫn rỗng, nếu là trang con thì phải lùi ra ngoài 1 cấp (../)
        $this->assets_folder = $is_root ? "" : "../";
        
        // Tự động gọi file Database.php để kết nối dữ liệu ngay lập tức
        require_once __DIR__ . '/Database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // --- CÔNG CỤ TÌM ẢNH THÔNG MINH ---
    // Giúp tìm ảnh trong nhiều thư mục khác nhau, nếu không thấy thì hiện ảnh lỗi (màu xám)
    protected function getImageUrl($filename) {
        // 1. Nếu không có tên file -> Trả về ảnh giữ chỗ màu xám (Placeholder)
        if (empty($filename)) {
            return "https://placehold.co/600x400/e9ecef/6c757d?text=No+Image";
        }

        // 2. Danh sách các ngăn tủ (thư mục) cần lục lọi để tìm ảnh
        $searchFolders = [
            'images/',          
            'images/avatars/',  
            'images/banners/',  
            'images/posts/',    
        ];

        // Làm sạch tên file (chỉ lấy tên, bỏ đường dẫn thừa nếu có)
        $cleanName = basename($filename); 

        // 3. Đi từng ngăn tủ để tìm
        foreach ($searchFolders as $folder) {
            // Đường dẫn thực tế trên máy tính (để kiểm tra xem file có tồn tại không)
            $physicalPath = __DIR__ . "/../" . $folder . $cleanName;
            
            // Đường dẫn web (để hiển thị cho người dùng xem)
            $webPath = $this->assets_folder . $folder . $cleanName;

            // Nếu tìm thấy file thật -> Trả về đường dẫn ngay
            if (file_exists($physicalPath)) {
                return $webPath; 
            }
        }

        // 4. Tìm khắp nơi không thấy -> Trả về ảnh báo lỗi màu đỏ
        return "https://placehold.co/600x400/ffcccc/ff0000?text=Not+Found";
    }

    // Hàm chính: Vẽ ra toàn bộ trang web theo thứ tự Đầu -> Thân -> Chân
    public function render() {
        echo '<!DOCTYPE html><html lang="vi">';
        $this->renderHead(); // Vẽ phần khai báo (CSS, Font chữ...)
        echo '<body class="bg-light d-flex flex-column min-vh-100">';
        $this->renderHeader(); // Vẽ thanh Menu (Navbar)
        
        echo '<main class="flex-shrink-0"><div class="container py-4">';
        $this->renderBody(); // Vẽ phần nội dung chính (Cái này các trang con sẽ tự vẽ riêng)
        echo '</div></main>';
        
        $this->renderFooter(); // Vẽ chân trang (Footer)
        echo '</body></html>';
    }

    // Vẽ phần <head>: Chứa các thư viện như Bootstrap, FontAwesome, CSS riêng
    protected function renderHead() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $this->title; ?></title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="<?php echo $this->assets_folder; ?>vendor/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="<?php echo $this->assets_folder; ?>vendor/fontawesome/css/all.min.css">
            <link rel="stylesheet" href="<?php echo $this->assets_folder; ?>css/style.css">
        </head>
        <?php
    }

    // Vẽ thanh Menu điều hướng (Navbar) ở trên cùng
    protected function renderHeader() {
        $root = $this->assets_folder;
        ?>
        <nav class="navbar navbar-expand-lg navbar-light fixed-top p-3 transition-base" id="mainNav">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $root; ?>index.php">
                    <div class="brand-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span class="brand-text">S-NEWS</span>
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $root; ?>index.php">Trang chủ</a></li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Danh mục</a>
                            <ul class="dropdown-menu border-0 shadow-lg rounded-3 overflow-hidden animate__animated animate__fadeInUp">
                                <?php
                                // Kết nối vào DB để lấy danh sách các danh mục (Thể thao, Đời sống...)
                                $stmtCat = $this->db->prepare("SELECT * FROM categories");
                                $stmtCat->execute();
                                $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

                                // Nếu có danh mục thì in ra từng dòng
                                if(count($categories) > 0) {
                                    foreach($categories as $cat) {
                                        // Tạo đường dẫn bấm vào sẽ nhảy sang trang category.php
                                        $catLink = $root . 'pages/category.php?cat=' . urlencode($cat['name']);
                                        echo '<li>
                                            <a class="dropdown-item" href="'.$catLink.'">
                                                <i class="'.$cat['icon'].' me-2 '.$cat['color'].'"></i>
                                                '.$cat['name'].'
                                            </a>
                                        </li>';
                                    }
                                } else {
                                    echo '<li><span class="dropdown-item text-muted">Chưa có danh mục</span></li>';
                                }
                                ?>
                            </ul>
                        </li>

                        <li class="nav-item"><a class="nav-link" href="<?php echo $root; ?>pages/about.php">Giới thiệu</a></li>
                    </ul>

                    <form class="search-wrapper" action="<?php echo $root; ?>pages/search.php" method="GET">
                        <input class="search-input" type="search" name="keyword" placeholder="Tìm kiếm tin tức..." required>
                        <button class="search-btn" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <div style="height: 100px;"></div>
        <?php
    }

    // Vẽ chân trang (Footer) chứa bản quyền và nhúng file Javascript
    protected function renderFooter() {
        ?>
        <footer class="footer mt-auto py-3 bg-dark text-white text-center">
            <div class="container">
                <span>&copy; 2024 S-News Project. Sinh viên thực hiện.</span>
            </div>
        </footer>
        <script src="<?php echo $this->assets_folder; ?>vendor/jquery/jquery.min.js"></script>
        <script src="<?php echo $this->assets_folder; ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo $this->assets_folder; ?>js/main.js"></script>
        <?php
    }

    // Hàm trừu tượng: Bắt buộc các trang con (như Trang chủ, Chi tiết) phải tự viết nội dung cho hàm này
    abstract protected function renderBody();
}
?>
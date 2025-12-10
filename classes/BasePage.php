<?php
abstract class BasePage {
    protected $title;
    protected $db;
    protected $assets_folder; // Dùng để xác định vị trí file (./ hoặc ../)

    public function __construct($title = "S-News", $is_root = false) {
        $this->title = $title;
        $this->assets_folder = $is_root ? "" : "../";
        
        // Tu dong ket noi Database
        require_once __DIR__ . '/Database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // --- SMART IMAGE FINDER (Tu dong tim trong moi thu muc) ---
    protected function getImageUrl($filename) {
        // 1. Neu khong co ten file -> Tra ve anh Placeholder
        if (empty($filename)) {
            return "https://placehold.co/600x400/e9ecef/6c757d?text=No+Image";
        }

        // 2. Dinh nghia danh sach cac folder can tim quet (Uu tien tu trai qua phai)
        $searchFolders = [
            'images/',          
            'images/avatars/',  
            'images/banners/',  
            'images/posts/',    
        ];

        // Xu ly ten file (Loai bo duong dan cu neu lo nhap 'images/...')
        $cleanName = basename($filename); 

        // 3. Vong lap quet tung folder
        foreach ($searchFolders as $folder) {
            // Duong dan vat ly (de kiem tra)
            $physicalPath = __DIR__ . "/../" . $folder . $cleanName;
            
            // Duong dan Web (de hien thi)
            $webPath = $this->assets_folder . $folder . $cleanName;

            if (file_exists($physicalPath)) {
                return $webPath; // Tim thay thi tra ve luon
            }
        }

        // 4. Fallback: Tra ve anh bao loi neu khong tim thay o dau ca
        return "https://placehold.co/600x400/ffcccc/ff0000?text=Not+Found";
    }

    public function render() {
        echo '<!DOCTYPE html><html lang="vi">';
        $this->renderHead();
        echo '<body class="bg-light d-flex flex-column min-vh-100">';
        $this->renderHeader();
        
        echo '<main class="flex-shrink-0"><div class="container py-4">';
        $this->renderBody(); 
        echo '</div></main>';
        
        $this->renderFooter();
        echo '</body></html>';
    }

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
                                // Kết nối DB lấy danh mục
                                $stmtCat = $this->db->prepare("SELECT * FROM categories");
                                $stmtCat->execute();
                                $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

                                if(count($categories) > 0) {
                                    foreach($categories as $cat) {
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

    abstract protected function renderBody();
}
?>
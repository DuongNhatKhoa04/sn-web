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
        // Ban co the them folder moi vao day
        $searchFolders = [
            'images/',          // Thu muc goc
            'images/avatars/',  // Anh thanh vien
            'images/banners/',  // Anh slider, quang cao
            'images/posts/',    // Anh bai viet
            'images/icons/'     // Icon neu co
        ];

        // Xu ly ten file (Loai bo duong dan cu neu lo nhap 'images/...')
        $cleanName = basename($filename); 

        // 3. Vong lap quet tung folder
        foreach ($searchFolders as $folder) {
            // Duong dan vat ly tren may (de kiem tra ton tai)
            $physicalPath = __DIR__ . "/../" . $folder . $cleanName;
            
            // Duong dan URL (de hien thi tren web)
            $webPath = $this->assets_folder . $folder . $cleanName;

            if (file_exists($physicalPath)) {
                return $webPath; // Tim thay thi tra ve luon
            }
        }

        // 4. Neu tim het ma khong thay -> Tra ve Placeholder bao loi
        return "https://placehold.co/600x400/ffcccc/ff0000?text=Not+Found:+" . $cleanName;
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
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="<?php echo $root; ?>index.php">
                    <i class="fa-solid fa-newspaper me-2"></i>S-NEWS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $root; ?>index.php">Trang chủ</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Danh mục</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo $root; ?>pages/category.php?cat=Thời sự">Thời sự</a></li>
                                <li><a class="dropdown-item" href="<?php echo $root; ?>pages/category.php?cat=Công nghệ">Công nghệ</a></li>
                                <li><a class="dropdown-item" href="<?php echo $root; ?>pages/category.php?cat=Đời sống">Đời sống</a></li>
                                <li><a class="dropdown-item" href="<?php echo $root; ?>pages/category.php?cat=Thông báo">Thông báo</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $root; ?>pages/about.php">Giới thiệu</a></li>
                    </ul>
                    <form class="d-flex" action="<?php echo $root; ?>pages/search.php" method="GET">
                        <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm kiếm..." required>
                        <button class="btn btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>
        </nav>
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
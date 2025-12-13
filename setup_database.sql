-- 1. XÓA SẠCH DATABASE CŨ
DROP DATABASE IF EXISTS s_news_db;

-- 2. TẠO DATABASE MỚI
CREATE DATABASE s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- =============================================
-- TẠO CẤU TRÚC BẢNG (SCHEMA)
-- =============================================

-- BẢNG 1: USERS (Người dùng/Admin) - Cần có bảng này để làm author_id
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 2: CATEGORIES (Danh mục)
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY, -- Đổi id thành category_id cho rõ ràng
    name VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(100) DEFAULT 'fa-solid fa-folder',
    color VARCHAR(50) DEFAULT 'text-primary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 3: ARTICLES (Bài viết)
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content LONGTEXT,
    image_url VARCHAR(255),
    
    -- QUAN TRỌNG: Dùng ID để liên kết (Chuẩn hóa CSDL)
    category_id INT,
    author_id INT,
    
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Tạo khóa ngoại (Foreign Key)
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    
    FULLTEXT (title, summary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 4: COMMENTS (Bình luận)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 5: LIKES (Lượt thích) - Để đạt đủ tiêu chí 5 bảng
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    ip_address VARCHAR(50), -- Lưu IP người like để tránh spam
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 6: BANNERS (Ảnh bìa trang chủ)
CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    link_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DỮ LIỆU MẪU (SEED DATA)
-- =============================================

-- 1. Thêm User (Admin)
INSERT INTO users (username, password, full_name, role) VALUES 
('admin', '123456', 'Quản trị viên', 'admin'),

-- 2. Thêm Danh mục
INSERT INTO categories (name, icon, color) VALUES 
('Thời sự', 'fa-solid fa-newspaper', 'text-primary'), -- ID 1
('Công nghệ', 'fa-solid fa-microchip', 'text-info'),    -- ID 2
('Đời sống', 'fa-solid fa-leaf', 'text-success'),       -- ID 3
('Thể thao', 'fa-solid fa-futbol', 'text-danger'),      -- ID 4
('Du lịch', 'fa-solid fa-plane', 'text-warning');       -- ID 5

-- 3. Thêm Bài viết (Dùng category_id thay vì chữ)
INSERT INTO articles (title, summary, content, category_id, author_id, image_url, views) VALUES 
(
    'iPhone 16 ra mắt với nhiều cải tiến', 
    'Apple vừa công bố dòng iPhone mới...', 
    'Chi tiết về iPhone 16...', 
    2, 1, 'images/posts/iphone-16.jpg', 150
),
(
    'Đội tuyển Việt Nam thắng lớn', 
    'Trận đấu kịch tính tại Mỹ Đình...', 
    'Diễn biến trận đấu...', 
    4, 1, 'https://placehold.co/600x400?text=Bong+Da', 200
),
(
    'Du lịch Đà Lạt mùa mưa', 
    'Những trải nghiệm thú vị khi đi Đà Lạt...', 
    'Nội dung bài viết...', 
    5, 1, 'https://placehold.co/600x400?text=Da+Lat', 120
);

-- 4. Thêm Banner
INSERT INTO banners (image_url, title, link_url, display_order) VALUES 
('images/posts/iphone-16.jpg', 'iPhone 16 Mới Nhất', 'pages/detail.php?id=1', 1),
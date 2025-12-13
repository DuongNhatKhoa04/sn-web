-- 1. XÓA SẠCH DATABASE CŨ
DROP DATABASE IF EXISTS s_news_db;

-- 2. TẠO DATABASE MỚI
CREATE DATABASE s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- =============================================
-- TẠO CẤU TRÚC BẢNG (SCHEMA)
-- =============================================

-- BẢNG 1: USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 2: CATEGORIES
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(100) DEFAULT 'fa-solid fa-folder',
    color VARCHAR(50) DEFAULT 'text-primary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 3: ARTICLES
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content LONGTEXT,
    image_url VARCHAR(255),
    
    -- Khóa ngoại
    category_id INT,
    author_id INT,
    
    views INT DEFAULT 0,
    likes INT DEFAULT 0, -- Cột này quan trọng cho chức năng Like
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    
    FULLTEXT (title, summary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 4: COMMENTS
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG 5: BANNERS
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

-- 1. Thêm User (Sửa lỗi dấu phẩy thành chấm phẩy)
INSERT INTO users (username, password, full_name, role) VALUES 
('admin', '123456', 'Quản trị viên', 'admin');

-- 2. Thêm Danh mục
INSERT INTO categories (name, icon, color) VALUES 
('Thời sự', 'fa-solid fa-newspaper', 'text-primary'),
('Công nghệ', 'fa-solid fa-microchip', 'text-info'),
('Đời sống', 'fa-solid fa-leaf', 'text-success'),
('Thể thao', 'fa-solid fa-futbol', 'text-danger'),
('Du lịch', 'fa-solid fa-plane', 'text-warning');

-- 3. Thêm Bài viết
INSERT INTO articles (title, summary, content, category_id, author_id, image_url, views, likes) VALUES 
(
    'iPhone 16 ra mắt với nhiều cải tiến', 
    'Apple vừa công bố dòng iPhone mới...', 
    'Nội dung chi tiết về iPhone 16...', 
    2, 1, 'images/posts/iphone-16.jpg', 100, 5
),
(
    'Đội tuyển Việt Nam thắng lớn', 
    'Trận đấu kịch tính tại Mỹ Đình...', 
    'Chi tiết trận đấu...', 
    4, 1, 'https://placehold.co/600x400?text=Bong+Da', 250, 12
),
(
    'Du lịch Đà Lạt mùa mưa', 
    'Những trải nghiệm thú vị khi đi Đà Lạt...', 
    'Review chi tiết...', 
    5, 1, 'https://placehold.co/600x400?text=Da+Lat', 80, 3
);

-- 4. Thêm Banner (Thêm 2 cái cho Slider chạy đẹp)
INSERT INTO banners (image_url, title, link_url, display_order) VALUES 
('images/posts/iphone-16.jpg', 'iPhone 16 Mới Nhất', 'pages/detail.php?id=1', 1)
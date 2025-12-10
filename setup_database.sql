-- 1. XÓA SẠCH DATABASE CŨ
DROP DATABASE IF EXISTS s_news_db;

-- 2. TẠO DATABASE MỚI
CREATE DATABASE s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- 3. TẠO BẢNG DANH MỤC (CATEGORIES) - MỚI
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,      -- Tên danh mục (VD: Công nghệ)
    icon VARCHAR(100) NOT NULL,            -- Class icon FontAwesome (VD: fa-solid fa-microchip)
    color VARCHAR(50) DEFAULT 'text-dark'  -- Màu sắc (VD: text-primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TẠO BẢNG BÀI VIẾT (ARTICLES)
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content LONGTEXT,
    image_url VARCHAR(255),
    category VARCHAR(50) DEFAULT 'Tin tức', -- Cột này sẽ lưu tên danh mục
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TẠO BẢNG BÌNH LUẬN (COMMENTS)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DỮ LIỆU MẪU (SEED DATA)
-- =============================================

-- 1. Thêm Danh mục (Có Icon và Màu sắc)
INSERT INTO categories (name, icon, color) VALUES 
('Thời sự', 'fa-solid fa-newspaper', 'text-primary'),
('Công nghệ', 'fa-solid fa-microchip', 'text-info'),
('Đời sống', 'fa-solid fa-leaf', 'text-success'),
('Thông báo', 'fa-solid fa-bell', 'text-warning'),
('Thể thao', 'fa-solid fa-futbol', 'text-danger'),
('Du lịch', 'fa-solid fa-plane', 'text-primary');

-- 2. Thêm Bài viết mẫu
INSERT INTO articles (title, summary, content, category, image_url, views, likes, created_at) VALUES 
(
    'Trí tuệ nhân tạo (AI) đang thay đổi thế giới', 
    'AI đang định hình lại thị trường lao động toàn cầu.', 
    '<p>Nội dung chi tiết về AI...</p>', 
    'Công nghệ', 'ai-tech.jpg', 0, 0, NOW()
),
(
    'Mùa lúa chín tại Mù Cang Chải đẹp ngỡ ngàng', 
    'Những thửa ruộng bậc thang vàng óng trải dài...', 
    '<p>Nội dung chi tiết du lịch...</p>', 
    'Du lịch', 'mu-cang-chai.png', 0, 0, NOW()
),
(
    'Ngoại hạng Anh: Cuộc đua vô địch kịch tính', 
    'Man City và Arsenal đang tạo nên cuộc đua song mã...', 
    '<p>Nội dung thể thao...</p>', 
    'Thể thao', 'football.webp', 0, 0, NOW()
);
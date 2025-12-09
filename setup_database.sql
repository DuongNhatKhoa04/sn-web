CREATE DATABASE IF NOT EXISTS s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- Bang Bai Viet (Update content LONGTEXT);
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content LONGTEXT,
    image_url VARCHAR(255),
    category VARCHAR(50) DEFAULT 'Tin tức',
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bang Binh Luan (Moi them);
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

INSERT INTO articles (title, summary, content, category, views, likes) VALUES 
('Sinh vien CNTT che tao Robot', 'Nhom sinh vien vua ra mat san pham...', 'Noi dung chi tiet...', 'Công nghệ', 1500, 230),
('Lich thi hoc ky moi nhat 2024', 'Phong dao tao vua cong bo lich thi...', 'Noi dung chi tiet...', 'Thông báo', 8900, 1200),
('Khai truong cang tin moi', 'Nhieu mon an hap dan gia re...', 'Noi dung chi tiet...', 'Đời sống', 4500, 670);

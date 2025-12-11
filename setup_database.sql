-- 1. XÓA SẠCH DATABASE CŨ
DROP DATABASE IF EXISTS s_news_db;

-- 2. TẠO DATABASE MỚI
CREATE DATABASE s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- =============================================
-- TẠO CẤU TRÚC BẢNG (SCHEMA)
-- =============================================

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
    category VARCHAR(50), 
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Đánh index để tìm kiếm nhanh (Fulltext Search)
    FULLTEXT (title, summary),
    -- Đánh index để lọc danh mục nhanh
    INDEX (category)
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

-- 6. BẢNG BANNER (DYNAMIC SLIDER) - MỚI
-- Giúp thay đổi banner mà không cần sửa code HTML
CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    title VARCHAR(100),           -- Tiêu đề trên ảnh
    link_url VARCHAR(255),        -- Link khi click vào banner (tùy chọn)
    display_order INT DEFAULT 0,  -- Thứ tự hiển thị
    is_active BOOLEAN DEFAULT TRUE, -- 1: Hiện, 0: Ẩn
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DỮ LIỆU MẪU (SEED DATA)
-- =============================================

-- 1. Cập nhật Banner: Giới hạn 5 cái và trỏ link về bài viết
INSERT INTO banners (image_url, title, link_url, display_order, is_active) VALUES 
('https://placehold.co/1200x450/C4D9FF/ffffff?text=iPhone+16+Ra+Mat', 'Sự kiện Apple: iPhone 16 ra mắt', 'pages/detail.php?id=1', 1, TRUE),
('https://placehold.co/1200x450/C5BAFF/ffffff?text=AI+Thay+Doi+The+Gioi', 'Tương lai của AI và Chat GPT-5', 'pages/detail.php?id=2', 2, TRUE),
('https://placehold.co/1200x450/FFC4D9/ffffff?text=Son+Doong+Cave', 'Khám phá Sơn Đoòng - Quảng Bình', 'pages/detail.php?id=4', 3, TRUE),
('https://placehold.co/1200x450/FF9EAA/ffffff?text=Bong+Da+VN', 'Đội tuyển Việt Nam tại Asian Cup', 'pages/detail.php?id=8', 4, TRUE),
('https://placehold.co/1200x450/B5C0D0/ffffff?text=Gia+Xang+Giam', 'Tin vui: Giá xăng giảm mạnh', 'pages/detail.php?id=11', 5, TRUE);
-- Lưu ý: id=1, id=2... phải trùng với ID thật trong bảng articles của bạn

-- 2. Thêm Danh mục (Có Icon và Màu sắc)
INSERT INTO categories (name, icon, color) VALUES 
('Thời sự', 'fa-solid fa-newspaper', 'text-primary'),
('Công nghệ', 'fa-solid fa-microchip', 'text-info'),
('Đời sống', 'fa-solid fa-leaf', 'text-success'),
('Thông báo', 'fa-solid fa-bell', 'text-warning'),
('Thể thao', 'fa-solid fa-futbol', 'text-danger'),
('Du lịch', 'fa-solid fa-plane', 'text-primary');

-- 3. Thêm Bài viết mẫu
INSERT INTO articles (title, summary, content, category, image_url, views, likes, created_at) VALUES 
-- CÔNG NGHỆ
(
    'Apple chính thức ấn định ngày ra mắt iPhone 16', 
    'Cộng đồng iFan đang đứng ngồi không yên trước thông tin rò rỉ mới nhất về sự kiện mùa thu của Apple.', 
    'Theo các nguồn tin uy tín, Apple sẽ mang đến những nâng cấp vượt trội về camera và chip A18 Pro...', 
    'Công nghệ', 'iphone-16.jpg', 1540, 342, NOW()
),
(
    'ChatGPT-5 sẽ thông minh đến mức nào?', 
    'OpenAI đang âm thầm phát triển thế hệ tiếp theo của mô hình ngôn ngữ lớn, hứa hẹn thay đổi cách chúng ta làm việc.', 
    'Sam Altman đã tiết lộ rằng GPT-5 sẽ có khả năng suy luận logic tốt hơn và xử lý đa phương thức...', 
    'Công nghệ', 'ai-future.jpg', 2300, 560, NOW()
),
(
    'Mạng 5G bắt đầu phủ sóng rộng rãi tại Hà Nội và TP.HCM', 
    'Các nhà mạng lớn tại Việt Nam đang đẩy nhanh tiến độ thương mại hóa 5G.', 
    'Người dùng di động tại các quận trung tâm đã có thể trải nghiệm tốc độ internet siêu nhanh...', 
    'Công nghệ', '5g-vietnam.jpg', 890, 120, NOW()
),

-- DU LỊCH
(
    'Khám phá hang Sơn Đoòng - Kỳ quan trong lòng đất', 
    'Hang động lớn nhất thế giới tại Quảng Bình tiếp tục là điểm đến mơ ước của du khách quốc tế.', 
    'Chuyến thám hiểm kéo dài 4 ngày 3 đêm sẽ đưa bạn qua những khối thạch nhũ khổng lồ và rừng nguyên sinh trong hang...', 
    'Du lịch', 'son-doong.jpg', 5600, 1200, NOW()
),
(
    'Top 5 quán cà phê view đẹp nhất Đà Lạt năm 2024', 
    'Đà Lạt không chỉ có hoa mà còn có những quán cà phê săn mây cực chất.', 
    'Danh sách này bao gồm những địa điểm mới nổi với phong cách thiết kế độc đáo, phù hợp để check-in...', 
    'Du lịch', 'da-lat-cafe.jpg', 3200, 890, NOW()
),
(
    'Kinh nghiệm du lịch Phú Quốc tự túc 3 ngày 2 đêm', 
    'Bí kíp ăn chơi, ngủ nghỉ tại Đảo Ngọc với chi phí hợp lý nhất.', 
    'Đừng bỏ lỡ ngắm hoàng hôn tại Sunset Sanato và thưởng thức bún quậy Kiến Xây...', 
    'Du lịch', 'phu-quoc.jpg', 4100, 950, NOW()
),
(
    'Phố cổ Hội An lọt top điểm đến lãng mạn nhất thế giới', 
    'Vẻ đẹp trầm mặc của những ngôi nhà vàng và đèn lồng lung linh đã chinh phục du khách.', 
    'Hội An về đêm mang một vẻ đẹp huyền bí, đặc biệt là hoạt động thả đèn hoa đăng trên sông Hoài...', 
    'Du lịch', 'hoi-an.jpg', 2800, 670, NOW()
),

-- THỂ THAO
(
    'Đội tuyển Việt Nam chốt danh sách dự Asian Cup', 
    'Huấn luyện viên trưởng đã đưa ra những quyết định bất ngờ về nhân sự.', 
    'Sự vắng mặt của một số trụ cột do chấn thương đang để lại nhiều lo lắng cho người hâm mộ...', 
    'Thể thao', 'tuyen-viet-nam.jpg', 7800, 2100, NOW()
),
(
    'Messi tiếp tục tỏa sáng tại giải nhà nghề Mỹ (MLS)', 
    'Siêu sao người Argentina vừa lập cú đúp giúp Inter Miami lội ngược dòng ngoạn mục.', 
    'Dù đã lớn tuổi nhưng đẳng cấp của Leo Messi vẫn quá khác biệt so với phần còn lại của giải đấu...', 
    'Thể thao', 'messi-mls.jpg', 6500, 1800, NOW()
),
(
    'Giải chạy Marathon TP.HCM thu hút 10.000 vận động viên', 
    'Sự kiện thể thao cộng đồng lớn nhất năm đã diễn ra sôi nổi vào sáng nay.', 
    'Các vận động viên đã hoàn thành cung đường chạy qua các địa điểm biểu tượng của thành phố...', 
    'Thể thao', 'marathon-hcm.jpg', 1200, 300, NOW()
),

-- THỜI SỰ
(
    'Giá xăng dầu hôm nay: Tiếp tục giảm mạnh', 
    'Tin vui cho người dân khi giá xăng E5 RON 92 giảm sâu trong kỳ điều chỉnh mới.', 
    'Liên Bộ Tài chính - Công Thương vừa công bố giá cơ sở mới, áp dụng từ 15h chiều nay...', 
    'Thời sự', 'gia-xang.jpg', 9000, 450, NOW()
),
(
    'Dự báo thời tiết: Miền Bắc đón đợt không khí lạnh tăng cường', 
    'Nhiệt độ tại các tỉnh miền núi phía Bắc có thể xuống dưới 10 độ C.', 
    'Người dân cần chủ động giữ ấm và bảo vệ gia súc, hoa màu trước đợt rét đậm rét hại này...', 
    'Thời sự', 'thoi-tiet.jpg', 5400, 200, NOW()
),
(
    'Tiến độ dự án sân bay Long Thành đang được đẩy nhanh', 
    'Các nhà thầu đang tập trung nhân lực và máy móc để thi công ngày đêm.', 
    'Gói thầu nhà ga hành khách đã bắt đầu lộ diện hình hài, dự kiến hoàn thành đúng tiến độ...', 
    'Thời sự', 'san-bay-long-thanh.jpg', 3100, 500, NOW()
),

-- ĐỜI SỐNG
(
    'Xu hướng sống tối giản của giới trẻ hiện nay', 
    'Bỏ bớt đồ đạc, tập trung vào trải nghiệm đang là lối sống được Gen Z ưa chuộng.', 
    'Danshari hay phong cách Minimalism giúp giảm stress và tiết kiệm chi phí sinh hoạt đáng kể...', 
    'Đời sống', 'song-toi-gian.jpg', 2200, 400, NOW()
),
(
    'Cách làm món thịt kho tàu chuẩn vị ngày Tết', 
    'Bí quyết để thịt mềm, nước màu hổ phách đẹp mắt mà không cần dùng nước dừa.', 
    'Bước quan trọng nhất là ướp thịt và canh lửa sao cho mỡ trong veo, tan ngay trong miệng...', 
    'Đời sống', 'thit-kho-tau.jpg', 4500, 1100, NOW()
),
(
    'Những loại cây cảnh lọc không khí tốt nhất cho văn phòng', 
    'Cải thiện không gian làm việc với những chậu cây nhỏ xinh và dễ chăm sóc.', 
    'Cây lưỡi hổ, trầu bà, và lan ý là những lựa chọn hàng đầu để hấp thụ bức xạ máy tính...', 
    'Đời sống', 'cay-canh.jpg', 1800, 350, NOW()
),
(
    'Review sách: "Muôn kiếp nhân sinh" - Bài học về luật nhân quả', 
    'Tác phẩm của Nguyên Phong đã thức tỉnh nhiều độc giả về ý nghĩa cuộc sống.', 
    'Cuốn sách không chỉ là những câu chuyện tâm linh mà còn lồng ghép nhiều kiến thức lịch sử, khoa học...', 
    'Đời sống', 'muon-kiep-nhan-sinh.jpg', 2600, 780, NOW()
),

-- THÔNG BÁO
(
    'Thông báo lịch nghỉ Tết Nguyên Đán 2025', 
    'Ban quản trị website xin thông báo đến quý độc giả và đối tác lịch nghỉ lễ chính thức.', 
    'Chúng tôi sẽ tạm ngưng hỗ trợ trực tuyến từ ngày 28 tháng Chạp đến hết mùng 5 Tết...', 
    'Thông báo', 'lich-nghi-tet.jpg', 10500, 150, NOW()
),
(
    'Bảo trì hệ thống máy chủ định kỳ', 
    'Để nâng cao trải nghiệm người dùng, chúng tôi sẽ tiến hành nâng cấp hệ thống vào cuối tuần này.', 
    'Thời gian bảo trì dự kiến từ 0h00 đến 4h00 sáng Chủ Nhật. Mong quý độc giả thông cảm...', 
    'Thông báo', 'bao-tri.jpg', 500, 10, NOW()
),
(
    'Cảnh báo lừa đảo trực tuyến mạo danh ngân hàng', 
    'Người dùng cần cảnh giác trước các tin nhắn SMS và email giả mạo yêu cầu cung cấp mã OTP.', 
    'Tuyệt đối không click vào các đường link lạ hoặc cung cấp thông tin tài khoản cho bất kỳ ai...', 
    'Thông báo', 'canh-bao-lua-dao.jpg', 7200, 2500, NOW()
);
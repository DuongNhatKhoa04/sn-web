-- 1. XÓA SẠCH DATABASE CŨ (Nếu có, để làm lại từ đầu cho sạch)
DROP DATABASE IF EXISTS s_news_db;

-- 2. TẠO DATABASE MỚI (Xây nhà kho mới)
CREATE DATABASE s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- =============================================
-- TẠO CẤU TRÚC BẢNG (SCHEMA) - ĐÓNG CÁC KỆ HÀNG
-- =============================================

-- 3. TẠO BẢNG DANH MỤC (CATEGORIES)
-- Chứa thông tin các loại tin: Thể thao, Công nghệ...
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,      -- Tên danh mục (VD: Công nghệ)
    icon VARCHAR(100) NOT NULL,            -- Mã icon hình ảnh (VD: fa-solid fa-microchip)
    color VARCHAR(50) DEFAULT 'text-dark'  -- Mã màu sắc (VD: text-primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TẠO BẢNG BÀI VIẾT (ARTICLES)
-- Chứa nội dung tin tức
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL, -- Tiêu đề bài viết
    summary TEXT,                -- Tóm tắt ngắn
    content LONGTEXT,            -- Nội dung dài
    image_url VARCHAR(255),      -- Tên file ảnh
    category VARCHAR(50),        -- Thuộc danh mục nào
    views INT DEFAULT 0,         -- Số lượt xem
    likes INT DEFAULT 0,         -- Số lượt thích
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Ngày giờ tạo
    
    -- Đánh index để tìm kiếm nhanh (Fulltext Search)
    FULLTEXT (title, summary),
    -- Đánh index để lọc danh mục nhanh
    INDEX (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TẠO BẢNG BÌNH LUẬN (COMMENTS)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,     -- Bình luận này thuộc bài viết nào
    username VARCHAR(50) NOT NULL, -- Tên người bình luận
    content TEXT NOT NULL,       -- Nội dung bình luận
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Nếu xóa bài viết thì xóa luôn bình luận của bài đó (CASCADE)
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG BANNER (DYNAMIC SLIDER)
-- Bảng này chứa thông tin các ảnh trượt ở trang chủ
CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    title VARCHAR(100),           -- Tiêu đề trên ảnh
    link_url VARCHAR(255),        -- Link khi click vào banner (tùy chọn)
    display_order INT DEFAULT 0,  -- Thứ tự hiển thị (cái nào hiện trước)
    is_active BOOLEAN DEFAULT TRUE, -- 1: Hiện, 0: Ẩn
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DỮ LIỆU MẪU (SEED DATA) - XẾP HÀNG LÊN KỆ
-- =============================================

-- 1. Thêm Banner mẫu
INSERT INTO banners (image_url, title, link_url, display_order, is_active) VALUES 
('images/posts/iphone-16.jpg', 'Sự kiện Apple: iPhone 16 ra mắt', 'pages/detail.php?id=1', 1, TRUE),
('https://placehold.co/1200x450/C5BAFF/ffffff?text=AI+Thay+Doi+The+Gioi', 'Tương lai của AI và Chat GPT-5', 'pages/detail.php?id=2', 2, TRUE),
('https://placehold.co/1200x450/FFC4D9/ffffff?text=Son+Doong+Cave', 'Khám phá Sơn Đoòng - Quảng Bình', 'pages/detail.php?id=4', 3, TRUE),
('https://placehold.co/1200x450/FF9EAA/ffffff?text=Bong+Da+VN', 'Đội tuyển Việt Nam tại Asian Cup', 'pages/detail.php?id=8', 4, TRUE),
('https://placehold.co/1200x450/B5C0D0/ffffff?text=Gia+Xang+Giam', 'Tin vui: Giá xăng giảm mạnh', 'pages/detail.php?id=11', 5, TRUE);

-- 2. Thêm Danh mục mẫu
INSERT INTO categories (name, icon, color) VALUES 
('Thời sự', 'fa-solid fa-newspaper', 'text-primary'),
('Công nghệ', 'fa-solid fa-microchip', 'text-info'),
('Đời sống', 'fa-solid fa-leaf', 'text-success'),
('Thông báo', 'fa-solid fa-bell', 'text-warning'),
('Thể thao', 'fa-solid fa-futbol', 'text-danger'),
('Du lịch', 'fa-solid fa-plane', 'text-primary');

-- 3. Thêm Bài viết mẫu (Đây là phần nội dung chính của web)
INSERT INTO articles (title, summary, content, category, image_url, views, likes, created_at) VALUES 
-- CÔNG NGHỆ
(
    'Apple chính thức ấn định ngày ra mắt iPhone 16', 
    'Cộng đồng iFan đang đứng ngồi không yên trước thông tin rò rỉ mới nhất về sự kiện mùa thu của Apple.', 
    'Theo các nguồn tin uy tín, Apple sẽ mang đến những nâng cấp vượt trội về camera và chip A18 Pro...', 
    'Công nghệ', 'iphone-16.jpg', 0, 0, NOW()
),
(
    'ChatGPT-5 sẽ thông minh đến mức nào?', 
    'OpenAI đang âm thầm phát triển thế hệ tiếp theo của mô hình ngôn ngữ lớn, hứa hẹn thay đổi cách chúng ta làm việc.', 
    'Sam Altman đã tiết lộ rằng GPT-5 sẽ có khả năng suy luận logic tốt hơn và xử lý đa phương thức...', 
    'Công nghệ', 'ai-future.jpg', 0, 0, NOW()
),
(
    'Mạng 5G bắt đầu phủ sóng rộng rãi tại Hà Nội và TP.HCM', 
    'Các nhà mạng lớn tại Việt Nam đang đẩy nhanh tiến độ thương mại hóa 5G.', 
    'Người dùng di động tại các quận trung tâm đã có thể trải nghiệm tốc độ internet siêu nhanh...', 
    'Công nghệ', '5g-vietnam.jpg', 0, 0, NOW()
),

-- DU LỊCH
(
    'Khám phá hang Sơn Đoòng - Kỳ quan trong lòng đất', 
    'Hang động lớn nhất thế giới tại Quảng Bình tiếp tục là điểm đến mơ ước của du khách quốc tế.', 
    'Chuyến thám hiểm kéo dài 4 ngày 3 đêm sẽ đưa bạn qua những khối thạch nhũ khổng lồ và rừng nguyên sinh trong hang...', 
    'Du lịch', 'son-doong.jpg', 0, 0, NOW()
),
(
    'Top 5 quán cà phê view đẹp nhất Đà Lạt năm 2024', 
    'Đà Lạt không chỉ có hoa mà còn có những quán cà phê săn mây cực chất.', 
    'Danh sách này bao gồm những địa điểm mới nổi với phong cách thiết kế độc đáo, phù hợp để check-in...', 
    'Du lịch', 'da-lat-cafe.jpg', 0, 0, NOW()
),
(
    'Kinh nghiệm du lịch Phú Quốc tự túc 3 ngày 2 đêm', 
    'Bí kíp ăn chơi, ngủ nghỉ tại Đảo Ngọc với chi phí hợp lý nhất.', 
    'Đừng bỏ lỡ ngắm hoàng hôn tại Sunset Sanato và thưởng thức bún quậy Kiến Xây...', 
    'Du lịch', 'phu-quoc.jpg', 0, 0, NOW()
),
(
    'Phố cổ Hội An lọt top điểm đến lãng mạn nhất thế giới', 
    'Vẻ đẹp trầm mặc của những ngôi nhà vàng và đèn lồng lung linh đã chinh phục du khách.', 
    'Hội An về đêm mang một vẻ đẹp huyền bí, đặc biệt là hoạt động thả đèn hoa đăng trên sông Hoài...', 
    'Du lịch', 'hoi-an.jpg', 0, 0, NOW()
),

-- THỂ THAO
(
    'Đội tuyển Việt Nam chốt danh sách dự Asian Cup', 
    'Huấn luyện viên trưởng đã đưa ra những quyết định bất ngờ về nhân sự.', 
    'Sự vắng mặt của một số trụ cột do chấn thương đang để lại nhiều lo lắng cho người hâm mộ...', 
    'Thể thao', 'tuyen-viet-nam.jpg', 0, 0, NOW()
),
(
    'Messi tiếp tục tỏa sáng tại giải nhà nghề Mỹ (MLS)', 
    'Siêu sao người Argentina vừa lập cú đúp giúp Inter Miami lội ngược dòng ngoạn mục.', 
    'Dù đã lớn tuổi nhưng đẳng cấp của Leo Messi vẫn quá khác biệt so với phần còn lại của giải đấu...', 
    'Thể thao', 'messi-mls.jpg', 0, 0, NOW()
),
(
    'Giải chạy Marathon TP.HCM thu hút 10.000 vận động viên', 
    'Sự kiện thể thao cộng đồng lớn nhất năm đã diễn ra sôi nổi vào sáng nay.', 
    'Các vận động viên đã hoàn thành cung đường chạy qua các địa điểm biểu tượng của thành phố...', 
    'Thể thao', 'marathon-hcm.jpg', 0, 0, NOW()
),

-- THỜI SỰ
(
    'Giá xăng dầu hôm nay: Tiếp tục giảm mạnh', 
    'Tin vui cho người dân khi giá xăng E5 RON 92 giảm sâu trong kỳ điều chỉnh mới.', 
    'Liên Bộ Tài chính - Công Thương vừa công bố giá cơ sở mới, áp dụng từ 15h chiều nay...', 
    'Thời sự', 'gia-xang.jpg', 0, 0, NOW()
),
(
    'Dự báo thời tiết: Miền Bắc đón đợt không khí lạnh tăng cường', 
    'Nhiệt độ tại các tỉnh miền núi phía Bắc có thể xuống dưới 10 độ C.', 
    'Người dân cần chủ động giữ ấm và bảo vệ gia súc, hoa màu trước đợt rét đậm rét hại này...', 
    'Thời sự', 'thoi-tiet.jpg', 0, 0, NOW()
),
(
    'Tiến độ dự án sân bay Long Thành đang được đẩy nhanh', 
    'Các nhà thầu đang tập trung nhân lực và máy móc để thi công ngày đêm.', 
    'Gói thầu nhà ga hành khách đã bắt đầu lộ diện hình hài, dự kiến hoàn thành đúng tiến độ...', 
    'Thời sự', 'san-bay-long-thanh.jpg', 0, 0, NOW()
),

-- ĐỜI SỐNG
(
    'Xu hướng sống tối giản của giới trẻ hiện nay', 
    'Bỏ bớt đồ đạc, tập trung vào trải nghiệm đang là lối sống được Gen Z ưa chuộng.', 
    'Danshari hay phong cách Minimalism giúp giảm stress và tiết kiệm chi phí sinh hoạt đáng kể...', 
    'Đời sống', 'song-toi-gian.jpg', 0, 0, NOW()
),
(
    'Cách làm món thịt kho tàu chuẩn vị ngày Tết', 
    'Bí quyết để thịt mềm, nước màu hổ phách đẹp mắt mà không cần dùng nước dừa.', 
    'Bước quan trọng nhất là ướp thịt và canh lửa sao cho mỡ trong veo, tan ngay trong miệng...', 
    'Đời sống', 'thit-kho-tau.jpg', 0, 0, NOW()
),
(
    'Những loại cây cảnh lọc không khí tốt nhất cho văn phòng', 
    'Cải thiện không gian làm việc với những chậu cây nhỏ xinh và dễ chăm sóc.', 
    'Cây lưỡi hổ, trầu bà, và lan ý là những lựa chọn hàng đầu để hấp thụ bức xạ máy tính...', 
    'Đời sống', 'cay-canh.jpg', 0, 0, NOW()
),
(
    'Review sách: "Muôn kiếp nhân sinh" - Bài học về luật nhân quả', 
    'Tác phẩm của Nguyên Phong đã thức tỉnh nhiều độc giả về ý nghĩa cuộc sống.', 
    'Cuốn sách không chỉ là những câu chuyện tâm linh mà còn lồng ghép nhiều kiến thức lịch sử, khoa học...', 
    'Đời sống', 'muon-kiep-nhan-sinh.jpg', 0, 0, NOW()
),

-- THÔNG BÁO
(
    'Thông báo lịch nghỉ Tết Nguyên Đán 2025', 
    'Ban quản trị website xin thông báo đến quý độc giả và đối tác lịch nghỉ lễ chính thức.', 
    'Chúng tôi sẽ tạm ngưng hỗ trợ trực tuyến từ ngày 28 tháng Chạp đến hết mùng 5 Tết...', 
    'Thông báo', 'lich-nghi-tet.jpg', 0, 0, NOW()
),
(
    'Bảo trì hệ thống máy chủ định kỳ', 
    'Để nâng cao trải nghiệm người dùng, chúng tôi sẽ tiến hành nâng cấp hệ thống vào cuối tuần này.', 
    'Thời gian bảo trì dự kiến từ 0h00 đến 4h00 sáng Chủ Nhật. Mong quý độc giả thông cảm...', 
    'Thông báo', 'bao-tri.jpg', 0, 0, NOW()
),
(
    'Cảnh báo lừa đảo trực tuyến mạo danh ngân hàng', 
    'Người dùng cần cảnh giác trước các tin nhắn SMS và email giả mạo yêu cầu cung cấp mã OTP.', 
    'Tuyệt đối không click vào các đường link lạ hoặc cung cấp thông tin tài khoản cho bất kỳ ai...', 
    'Thông báo', 'canh-bao-lua-dao.jpg', 0, 0, NOW()
);
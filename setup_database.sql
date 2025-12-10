-- 1. XÓA SẠCH DATABASE CŨ (LÀM MỚI TỪ ĐẦU)
DROP DATABASE IF EXISTS s_news_db;

-- 2. TẠO DATABASE MỚI (CHUẨN TIẾNG VIỆT)
CREATE DATABASE s_news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE s_news_db;

-- 3. TẠO BẢNG BÀI VIẾT (ARTICLES)
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content LONGTEXT, -- Dùng LONGTEXT để chứa bài viết dài vô tư
    image_url VARCHAR(255),
    category VARCHAR(50) DEFAULT 'Tin tức',
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TẠO BẢNG BÌNH LUẬN (COMMENTS)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. THÊM 10 BÀI VIẾT MẪU (ĐA DẠNG CHỦ ĐỀ & ĐỊNH DẠNG ẢNH)
INSERT INTO articles (title, summary, content, category, image_url, views, likes, created_at) VALUES 
(
    'Trí tuệ nhân tạo (AI) đang thay đổi cách chúng ta làm việc như thế nào?', 
    'AI không còn là chuyện viễn tưởng. Từ ChatGPT đến Midjourney, công nghệ này đang định hình lại thị trường lao động toàn cầu.', 
    '<p>Trí tuệ nhân tạo (AI) đang thâm nhập vào mọi ngóc ngách của đời sống. Các công cụ như ChatGPT giúp soạn thảo văn bản nhanh chóng, trong khi các AI vẽ tranh đang thách thức giới nghệ thuật.</p><p>Tuy nhiên, các chuyên gia cũng cảnh báo về nguy cơ mất việc làm ở một số lĩnh vực lặp lại. Điều quan trọng là sinh viên cần trang bị kỹ năng sử dụng AI thay vì lo sợ nó.</p>', 
    'Công nghệ', 
    'images/posts/ai-tech.jpg', 
    1540, 120, NOW()
),
(
    'Khám phá vẻ đẹp mùa lúa chín tại Mù Cang Chải', 
    'Những thửa ruộng bậc thang vàng óng trải dài đến tận chân trời là cảnh sắc tuyệt vời mà bạn không thể bỏ qua tháng này.', 
    '<p>Mù Cang Chải (Yên Bái) vào tháng 9, tháng 10 là thời điểm đẹp nhất trong năm. Du khách sẽ được đắm mình trong hương lúa mới, ngắm nhìn những "nấc thang lên thiên đường" được nhuộm màu vàng ruộm.</p><p>Đừng quên thưởng thức xôi nếp nương và gà đồi nướng, những đặc sản nổi tiếng của vùng cao Tây Bắc.</p>', 
    'Du lịch', 
    'images/posts/mu-cang-chai.png', 
    2300, 450, NOW() - INTERVAL 1 DAY
),
(
    'Kết quả Ngoại hạng Anh: Cuộc đua vô địch kịch tính đến phút chót', 
    'Manchester City và Arsenal đang tạo nên cuộc đua song mã hấp dẫn nhất lịch sử giải đấu cao nhất xứ sở sương mù.', 
    '<p>Vòng đấu vừa qua chứng kiến những bất ngờ lớn khi các đội top đầu đều gặp khó khăn. Arsenal đã có chiến thắng nghẹt thở ở phút bù giờ, trong khi Man City sẩy chân đáng tiếc.</p><p>Người hâm mộ đang rất mong chờ trận đối đầu trực tiếp giữa hai đội vào cuối tuần này.</p>', 
    'Thể thao', 
    'images/posts/football.webp', 
    5600, 890, NOW() - INTERVAL 2 DAY
),
(
    '5 thói quen nhỏ vào buổi sáng giúp bạn tràn đầy năng lượng', 
    'Chỉ cần thay đổi một chút thói quen sau khi thức dậy, bạn sẽ có một ngày làm việc và học tập hiệu quả gấp đôi.', 
    '<p>1. Uống một ly nước ấm ngay khi thức dậy.<br>2. Dành 10 phút tập thể dục nhẹ hoặc thiền.<br>3. Không kiểm tra điện thoại ngay lập tức.<br>4. Ăn sáng đầy đủ dinh dưỡng.<br>5. Lên danh sách việc cần làm (To-do list).</p>', 
    'Sức khỏe', 
    'images/posts/healthy-morning.gif', 
    3100, 210, NOW() - INTERVAL 3 DAY
),
(
    'Sinh viên năm cuối và nỗi lo thất nghiệp: Cần chuẩn bị gì?', 
    'Thị trường tuyển dụng đang ngày càng khắt khe. Bằng giỏi liệu có đủ để bạn tìm được công việc mơ ước?', 
    '<p>Ngoài kiến thức chuyên môn, các nhà tuyển dụng hiện nay đánh giá rất cao kỹ năng mềm (giao tiếp, làm việc nhóm) và khả năng ngoại ngữ. Sinh viên nên chủ động đi thực tập sớm để tích lũy kinh nghiệm thực tế ngay khi còn ngồi trên ghế nhà trường.</p>', 
    'Giáo dục', 
    'images/posts/job-interview.jpg', 
    4200, 340, NOW() - INTERVAL 4 DAY
),
(
    'Giá vàng hôm nay biến động mạnh, nhà đầu tư lo lắng', 
    'Thị trường vàng trong nước và thế giới đang có những diễn biến khó lường trước các thông tin kinh tế mới.', 
    '<p>Sáng nay, giá vàng SJC đã giảm nhẹ so với hôm qua, tuy nhiên chênh lệch mua vào - bán ra vẫn ở mức cao. Các chuyên gia khuyên người dân nên thận trọng khi mua tích trữ vào thời điểm nhạy cảm này.</p>', 
    'Kinh tế', 
    'images/posts/gold-market.jpg', 
    1200, 50, NOW() - INTERVAL 5 DAY
),
(
    'Review phim: "Đất Rừng Phương Nam" - Hùng vĩ và xúc động', 
    'Một tác phẩm điện ảnh Việt Nam đáng xem, tái hiện lại vẻ đẹp hào hùng của vùng đất Nam Bộ xưa.', 
    '<p>Bộ phim đã gây ấn tượng mạnh với bối cảnh được đầu tư công phu và diễn xuất chân thực của dàn diễn viên. Tuy còn một số tranh cãi về trang phục, nhưng không thể phủ nhận đây là một bước tiến của điện ảnh nước nhà.</p>', 
    'Giải trí', 
    'images/posts/film-review.png', 
    6700, 1500, NOW() - INTERVAL 6 DAY
),
(
    'Cảnh báo thủ đoạn lừa đảo mới qua mạng xã hội', 
    'Cơ quan công an vừa phát đi cảnh báo về hình thức lừa đảo "việc nhẹ lương cao" đang nhắm vào sinh viên.', 
    '<p>Các đối tượng thường mạo danh các sàn thương mại điện tử lớn, dụ dỗ nạn nhân nạp tiền làm nhiệm vụ để nhận hoa hồng. Ban đầu chúng sẽ trả thưởng đầy đủ, nhưng khi số tiền nạp lớn, chúng sẽ chặn liên lạc và chiếm đoạt.</p>', 
    'Pháp luật', 
    'images/posts/scam-alert.jpg', 
    8900, 2100, NOW() - INTERVAL 1 HOUR
),
(
    'NASA phát hiện hành tinh có thể có nước lỏng', 
    'Kính viễn vọng James Webb vừa gửi về những hình ảnh chấn động từ một hệ sao cách chúng ta 120 năm ánh sáng.', 
    '<p>Hành tinh mang tên K2-18b nằm trong vùng "có thể sống được" của ngôi sao chủ. Các phân tích quang phổ cho thấy dấu hiệu của khí Metan và CO2, gợi ý về sự tồn tại của một đại dương nước lỏng trên bề mặt.</p>', 
    'Khoa học', 
    'images/posts/space-nasa.webp', 
    2100, 180, NOW() - INTERVAL 7 DAY
),
(
    'Mẹo vặt: Cách làm món sườn xào chua ngọt "bất bại"', 
    'Công thức đơn giản, dễ làm nhưng đảm bảo ngon như ngoài hàng, ai vụng về cũng có thể thành công.', 
    '<p>Bí quyết nằm ở tỷ lệ pha nước sốt: 5 thìa nước, 4 thìa đường, 3 thìa giấm, 2 thìa mắm, 1 thìa tương ớt. Sườn cần được chần qua và chiên sơ trước khi sốt để giữ được độ ngọt thịt.</p>', 
    'Đời sống', 
    'images/posts/food-suon-xao.jpg', 
    5400, 670, NOW() - INTERVAL 8 DAY
);

-- 6. THÊM VÀI BÌNH LUẬN MẪU
INSERT INTO comments (article_id, username, content) VALUES 
(1, 'Minh Tuấn', 'Bài viết rất hay và ý nghĩa!'),
(1, 'Lan Anh', 'AI đáng sợ thật, nhưng cũng rất hữu ích.'),
(3, 'Fan MU', 'Năm nay MC lại vô địch thôi, đội hình quá mạnh.'),
(8, 'Công An Phường', 'Mọi người hãy cảnh giác, đừng tham lam nhé!');
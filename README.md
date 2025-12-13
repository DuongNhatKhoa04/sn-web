# TÀI LIỆU KỸ THUẬT & HƯỚNG DẪN SỬ DỤNG S-NEWS

## 1. Cấu Trúc Thư Mục (Project Structure)

Sau khi chạy tool cài đặt và chép mã nguồn, dự án sẽ nằm tại: `C:\xampp\htdocs\s-news` với cấu trúc như sau:

```text
sn-web/
├── admin/                  # [ADMIN] Khu vực quản trị (MỚI)
│   ├── add_article.php     # Giao diện thêm bài viết (Tích hợp CKEditor & Upload)
│   ├── login.php           # Trang đăng nhập bảo mật
│   ├── logout.php          # Xử lý đăng xuất
│   └── process_add.php     # Xử lý logic thêm bài viết vào CSDL
├── api/                    # [API] Xử lý AJAX (Không load lại trang)
│   ├── add_comment.php     # API thêm bình luận
│   └── api_like.php        # API xử lý Thả tim (Lưu vào bảng likes)
├── classes/                # [LOGIC] Các lớp xử lý chính (OOP)
│   ├── Article.php         # Quản lý bài viết (Lấy danh sách, chi tiết, tìm kiếm)
│   ├── BasePage.php        # Khung giao diện chung (Header, Footer)
│   └── Database.php        # Kết nối CSDL MySQL (PDO)
├── css/                    # [GIAO DIỆN] CSS đã tách nhỏ (Modular CSS)
│   ├── style.css           # File gốc import các file con
│   ├── layout.css          # Bố cục chính
│   ├── responsive.css      # Xử lý giao diện Mobile
│   └── ...
├── images/                 # Kho chứa ảnh
│   └── posts/              # Chứa ảnh bài viết được upload từ Admin
├── pages/                  # Các trang hiển thị (Front-end)
│   ├── category.php        # Trang lọc tin theo danh mục
│   ├── detail.php          # Trang xem chi tiết bài viết
│   └── search.php          # Trang kết quả tìm kiếm
├── index.php               # TRANG CHỦ
└── setup_database.sql      # File khởi tạo CSDL
```

---

## 2. Cơ Sở Dữ Liệu (Database Schema)

Hệ thống sử dụng 5 bảng dữ liệu được thiết kế chuẩn hóa, có quan hệ ràng buộc (Foreign Key) để đảm bảo toàn vẹn dữ liệu.

### 2.1. Bảng `users` (Người dùng & Admin)
* **Chức năng:** Quản lý tài khoản đăng nhập vào trang quản trị.
* **Lưu ý nhập liệu:**
    * `role`: Chỉ chấp nhận giá trị `'admin'` (quản trị viên) hoặc `'user'` (người dùng thường).
    * Chỉ tài khoản có `role = 'admin'` mới truy cập được trang `admin/`.

### 2.2. Bảng `categories` (Danh mục tin)
* **Chức năng:** Quản lý các thể loại tin (Thể thao, Công nghệ...).
* **Lưu ý nhập liệu:**
    * `category_id`: Là khóa chính (Primary Key) dùng để liên kết với bài viết.
    * `icon`: Dùng mã class của FontAwesome (VD: `fa-solid fa-futbol`).
    * `color`: Dùng mã class màu text của Bootstrap (VD: `text-danger`, `text-primary`).

### 2.3. Bảng `articles` (Bài viết)
* **Chức năng:** Lưu trữ nội dung tin tức chính.
* **Lưu ý QUAN TRỌNG:**
    * `category_id`: Phải nhập **ID (số nguyên)** của danh mục tương ứng trong bảng `categories` (Không nhập tên text như cũ).
    * `author_id`: Phải nhập **ID** của tài khoản trong bảng `users`.
    * `image_url`: Đường dẫn tương đối của ảnh (VD: `images/posts/hinh-anh.jpg`).

### 2.4. Bảng `comments` (Bình luận)
* **Chức năng:** Lưu trữ bình luận của độc giả.
* **Cơ chế:** Khi xóa một bài viết, các bình luận thuộc bài viết đó sẽ tự động bị xóa theo (Cơ chế `ON DELETE CASCADE`).

### 2.5. Bảng `banners` (Slider trang chủ)
* **Chức năng:** Quản lý ảnh chạy slide (Carousel) ở đầu trang chủ.
* **Lưu ý nhập liệu:**
    * `link_url`: **BẮT BUỘC** điền đúng định dạng đường dẫn nội bộ: `pages/detail.php?id=[Mã_Bài_Viết]`.
    * Ví dụ: `pages/detail.php?id=15` (Sẽ mở bài viết có ID là 15 khi bấm vào banner).

---

## 3. Hướng Dẫn Cài Đặt (Installation Guide)

Vui lòng làm theo đúng thứ tự các bước sau để web chạy ổn định:

### Bước 1: Tải và Cài đặt XAMPP
- Tải XAMPP phiên bản hỗ trợ PHP 7.4 hoặc 8.x (Khuyên dùng bản **8.0.30**).
- Cài đặt xong, mở **XAMPP Control Panel**.
- Bấm **Start** hai dòng đầu tiên: `Apache` và `MySQL`.

### Bước 2: Khởi tạo Project
1. Vào thư mục `C:\xampp\htdocs\`.
2. Tạo một thư mục mới tên là `s-news`.
3. Copy toàn bộ mã nguồn (các thư mục `admin`, `classes`, `css`, `pages`...) vào trong thư mục `s-news` vừa tạo.

### Bước 3: Nhập Cơ sở dữ liệu (Import Database)
1. Mở trình duyệt, truy cập: `http://localhost/phpmyadmin/`
2. Bấm **New (Mới)** ở cột trái -> Đặt tên database là `s_news_db` -> Bấm **Create**.
3. Chọn database vừa tạo -> Bấm tab **Import (Nhập)**.
4. Bấm **Choose File** -> Chọn file `setup_database.sql` trong thư mục dự án.
5. Bấm **Import** (hoặc **Go**) ở cuối trang để tạo bảng và dữ liệu mẫu.

### Bước 4: Hoàn tất
- Truy cập trang chủ: `http://localhost/s-news/`

---

## 4. Hướng Dẫn Quản Trị & Chỉnh Sửa Nội Dung

### 4.1. Chỉnh sửa trang Giới thiệu (About)
Nội dung trang About là tĩnh (không nằm trong Database), bạn sửa trực tiếp trong code.
- **File cần sửa:** `pages/about.php` (Dùng Notepad++ hoặc VS Code).
- **Thay đổi ảnh:** Copy ảnh vào `images/avatars/`, sau đó sửa tên file trong hàm `getImageUrl("ten-anh.jpg")`.
- **Thay đổi nội dung:** Sửa văn bản tiếng Việt nằm giữa các thẻ HTML.

### 4.2. Quản lý Bài viết (Qua trang Admin)
Hệ thống đã tích hợp trang quản trị riêng, không cần thao tác trong Database.

#### Đăng nhập Admin
1. Truy cập: `http://localhost/s-news/admin/login.php`
2. Nhập tài khoản quản trị (Mặc định trong DB):
   - **User:** `admin`
   - **Pass:** `123456`
   *(Lưu ý: Chỉ tài khoản có quyền `admin` mới đăng nhập được)*.

#### Các chức năng quản lý
Sau khi đăng nhập, bạn sẽ được chuyển đến **Dashboard**:
1.  **Thêm bài viết:**
    - Bấm nút **"Thêm bài mới"**.
    - Nhập tiêu đề, chọn danh mục, tải ảnh đại diện.
    - Soạn thảo nội dung bài viết bằng công cụ Word (CKEditor).
    - Bấm **Đăng bài viết**.
2.  **Sửa bài viết:**
    - Tại danh sách bài viết, bấm nút **Sửa (Icon màu vàng)**.
    - Thay đổi thông tin và bấm **Lưu thay đổi**.
3.  **Xóa bài viết:**
    - Bấm nút **Xóa (Icon màu đỏ)**.
    - Xác nhận xóa (Banner liên quan đến bài viết cũng sẽ tự động bị xóa).

### 4.3. Quản lý khác (Banner, User)
Hiện tại các chức năng này thực hiện qua phpMyAdmin:
- **Banner:** Bảng `banners`.
- **Tài khoản:** Bảng `users` (Có thể thêm user mới và set cột `role` thành `admin` hoặc `user`).

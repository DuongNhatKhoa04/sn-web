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

Vui lòng làm theo đúng thứ tự các bước sau:

### Bước 1: Tải và Cài đặt XAMPP

- Tải XAMPP 8.0.30 (**bắt buộc đúng phiên bản**).
- Gợi ý: Tìm bản `XAMPP Windows x64 8.0.30` trên trang chủ Apache Friends.
- Cài đặt xong, **chưa cần bật server** vội.

### Bước 2: Chạy Script khởi tạo

1. Để 2 file `setup-solution.cmd` và `download.cmd` chung một thư mục bất kỳ.
2. Chạy file `setup-solution.cmd`.
3. Đợi script chạy xong.  
   Script sẽ tự động:
   - Tạo thư mục `C:\xampp\htdocs\s-news`
   - Tải các thư viện cần thiết (Bootstrap, jQuery, FontAwesome).

### Bước 3: Copy Mã nguồn

1. Giải nén file code của bạn.
2. Copy toàn bộ nội dung (các file `.php`, folder `css`, `images`, `js`...)  
   và dán vào thư mục:  
   `C:\xampp\htdocs\s-news`
3. Chọn **"Replace" (Ghi đè)** nếu được hỏi.

### Bước 4: Khởi động Server

1. Mở **XAMPP Control Panel**.
2. Bấm **Start** dòng `Apache`.
3. Bấm **Start** dòng `MySQL`.

### Bước 5: Nhập Cơ sở dữ liệu (Import Database)

1. Mở trình duyệt vào: `http://localhost/phpmyadmin/index.php`
2. Bấm **New (Mới)** ở cột trái, tạo database tên: `s_news_db`.
3. Chọn database vừa tạo, bấm tab **Import (Nhập)**.
4. Bấm **Choose File**, chọn file `setup_database.sql` trong thư mục  
   `C:\xampp\htdocs\s-news`.
5. Bấm **Import** (hoặc **Go**) ở cuối trang.

### Bước 6: Hoàn tất

- Truy cập: `http://localhost/s-news/` để sử dụng.

---

## 4. Hướng Dẫn Chỉnh Sửa Nội Dung

### 4.1. Chỉnh sửa trang Giới thiệu (About)

Nội dung trang About **không nằm trong Database** mà nằm trong file code.

- File cần sửa: `pages/about.php`  
  (Dùng Notepad++ hoặc VS Code để mở).

#### Cách thay đổi ảnh thành viên

1. Tìm đoạn:  
   `getImageUrl("tên-file-cũ.jpg")`
2. Sửa thành tên ảnh của bạn, ví dụ:  
   `getImageUrl("hinh-moi.png")` (nhớ kèm đuôi file).
3. Lưu ý: Ảnh phải bỏ vào thư mục: `images/avatars/`.

#### Cách thay đổi thông tin

- Tìm và sửa trực tiếp các đoạn văn bản:
  - Tên (VD: `Nguyen Van A`)
  - Vai trò (VD: `Developer`)
- Sửa phần nhiệm vụ trong các thẻ `<li>...</li>`  
  (Nên tóm tắt thành **3 mục** cho đẹp).

---

### 4.2. Quản lý Bài viết & Banner (Qua phpMyAdmin)

- Truy cập: `http://localhost/phpmyadmin/index.php`
- Chọn database: `s_news_db`.

#### Thêm mới

1. Chọn bảng (`articles`, `banners`...).
2. Chọn tab **Insert (Chèn)**.
3. Điền thông tin.
4. Bấm **Go** để lưu.

#### Sửa / Xóa

1. Chọn bảng.
2. Chọn tab **Browse (Duyệt)**.
3. Tìm dòng cần chỉnh.
4. Bấm **Edit (Sửa)** hoặc **Delete (Xóa)**.

# TÀI LIỆU KỸ THUẬT & HƯỚNG DẪN SỬ DỤNG S-NEWS

## 1. Cấu Trúc Thư Mục (Project Structure)

Sau khi chạy tool cài đặt và chép mã nguồn, dự án sẽ nằm tại: `C:\xampp\htdocs\s-news` với cấu trúc như sau:

```text
s-news/
├── api/                        # Xử lý AJAX (Bình luận, Like)
│   ├── add_comment.php         # API xử lý thêm bình luận
│   └── api_like.php            # API xử lý lượt thích
├── classes/                    # Các lớp xử lý chính (OOP)
│   ├── Article.php             # Xử lý bài viết
│   ├── BasePage.php            # Lớp giao diện cha
│   └── Database.php            # Kết nối CSDL
├── css/                        # Style giao diện (style.css)
├── images/                     # Kho ảnh (Quan trọng: Bỏ ảnh đúng folder)
│   ├── avatars/                # Ảnh đại diện thành viên (cho trang About)
│   ├── banners/                # Ảnh banner slider (cho trang chủ)
│   └── posts/                  # Ảnh thumbnail bài viết
├── js/                         # Script xử lý frontend (main.js)
├── pages/                      # Các trang con
│   ├── about.php               # Trang giới thiệu (Cần sửa code để thay nội dung)
│   ├── category.php            # Trang danh mục
│   ├── detail.php              # Trang chi tiết bài viết
│   └── search.php              # Trang tìm kiếm
├── vendor/                     # Thư viện (Tự động tải bởi download.cmd)
│   ├── bootstrap/              # Bootstrap 5.3.2
│   ├── fontawesome/            # FontAwesome 6.4.2
│   └── jquery/                 # jQuery 3.7.1
├── index.php                   # Trang chủ
├── setup_database.sql          # File khởi tạo CSDL
├── setup-solution.cmd          # Script tạo folder dự án & tải thư viện
├── download.cmd                # Script hỗ trợ tải file
└── README.md                   # Tài liệu hướng dẫn này
```

---

## 2. Cơ Sở Dữ Liệu (Database Schema)

Dưới đây là mô tả các bảng dữ liệu và LƯU Ý QUAN TRỌNG khi nhập liệu để tránh lỗi web.

### 2.1. Bảng `banners` (Slider trang chủ)

- Chức năng: Quản lý ảnh chạy slide ở trang chủ.

Lưu ý nhập liệu:

- `image_url`: Điền **tên file ảnh** (VD: `banner1.jpg`). Ảnh phải có trong `images/banners/`.
- `link_url`: **BẮT BUỘC** phải đúng định dạng:  
  `pages/detail.php?id=[id bài post]`  
  Ví dụ: `pages/detail.php?id=15` (Trỏ về bài viết có ID là `15`).

### 2.2. Bảng `articles` (Bài viết)

- Chức năng: Chứa nội dung tin tức.

Lưu ý nhập liệu:

- `image_url`: Điền **tên file ảnh** (VD: `tin-moi.jpg`). Ảnh phải có trong `images/posts/`.
- `category`: Tên danh mục (VD: `"Thể thao"`). **Phải trùng khớp** với tên trong bảng `categories`.

### 2.3. Bảng `categories` (Danh mục)

- Chức năng: Quản lý các thể loại tin.

Lưu ý nhập liệu:

- `icon`: Dùng class FontAwesome (VD: `fa-solid fa-futbol`).
- `color`: Dùng class màu Bootstrap (VD: `text-danger`, `text-primary`).

### 2.4. Bảng `comments` (Bình luận)

- Chức năng: Lưu trữ bình luận của người dùng (thường được thêm tự động từ giao diện web).

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

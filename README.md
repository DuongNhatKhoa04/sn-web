# S-NEWS: WEBSITE TIN TỨC SINH VIÊN

![Version](https://img.shields.io/badge/Version-1.0%20(Offline)-blue)
![Environment](https://img.shields.io/badge/Server-WAMP%20Port%208080-green)
![Tech](https://img.shields.io/badge/Tech-PHP%20OOP%20|%20MySQL-orange)

## 1. TỔNG QUAN DỰ ÁN

### 1.1. Giới thiệu
**S-News (Student News)** là hệ thống website tin tức cơ bản, được xây dựng nhằm phục vụ nhu cầu cập nhật thông tin học đường và xã hội cho sinh viên. Dự án được phát triển với mục tiêu tối ưu hóa khả năng đọc hiểu mã nguồn, dễ dàng bảo trì và hoạt động hoàn toàn trong môi trường mạng nội bộ (Offline/Localhost).

### 1.2. Phạm vi yêu cầu
* **Mô hình triển khai:** Web Application chạy trên Localhost.
* **Yêu cầu kỹ thuật:** Không phụ thuộc Internet (Thư viện Bootstrap/jQuery được tải về máy).
* **Công nghệ:**
    * PHP Thuần (theo mô hình OOP).
    * MySQL (Cơ sở dữ liệu).
    * HTML5 / CSS3 / jQuery / Bootstrap 5.

---

## 2. KIẾN TRÚC HỆ THỐNG

### 2.1. Sơ đồ Cấu trúc & Nguyên lý hoạt động
Hệ thống sử dụng mô hình **Page Controller** kết hợp **Template Method Pattern**.

* **Lớp `BasePage` (Abstract):** Đóng vai trò là khung sườn (Template). Nó định nghĩa cấu trúc chuẩn của một trang web (gồm Header - Body - Footer).
* **Các trang con (`pages/*.php`):** Kế thừa từ `BasePage` và chỉ tập trung xử lý nội dung riêng biệt (phần Body).

### 2.2. Cơ sở dữ liệu (Database Schema)
* **Tên CSDL:** `s_news_db`
* **Bảng chính:** `articles`

| Tên trường | Kiểu dữ liệu | Mô tả |
| :--- | :--- | :--- |
| `id` | `INT` (PK) | Mã bài viết (Tự tăng) |
| `title` | `VARCHAR(255)` | Tiêu đề bài viết |
| `summary` | `TEXT` | Tóm tắt ngắn gọn |
| `content` | `TEXT` | Nội dung chi tiết bài viết |
| `image_url` | `VARCHAR(255)` | Đường dẫn hình ảnh minh họa |
| `category` | `VARCHAR(50)` | Danh mục (Thời sự, Công nghệ...) |
| `views` | `INT` | Số lượt xem |
| `likes` | `INT` | Số lượt thích |

---

## 3. THIẾT KẾ KỸ THUẬT & OOP

### 3.1. Class Database (`classes/Database.php`)
* **Nhiệm vụ:** Đóng gói việc kết nối tới MySQL.
* **Lợi ích:** Tăng tính bảo mật và dễ bảo trì. Nếu thay đổi mật khẩu hoặc tên Database, chỉ cần sửa duy nhất file này.
* **Nguyên lý:** Đơn nhiệm (Single Responsibility Principle).

### 3.2. Class BasePage (`classes/BasePage.php`)
* **Nhiệm vụ:**
    * Tự động sinh mã HTML cho Header (Menu) và Footer.
    * Tự động nhúng file CSS/JS từ thư mục `vendor/` (chế độ Offline).
    * Tự động xử lý đường dẫn tương đối (`../` hoặc `./`) tùy vị trí file gọi nó.
* **Lợi ích:** Giúp giao diện đồng nhất tuyệt đối trên tất cả các trang.

---

## 4. PHÂN CÔNG CHỨC NĂNG (TEAMWORK)

| Thành viên | UI Phụ trách | Chức năng chi tiết |
| :--- | :--- | :--- |
| **Thành viên 1** | **Search** & **Category** | - Xử lý tìm kiếm SQL: `WHERE title LIKE %key%` <br> - Hiển thị danh sách tin theo danh mục. |
| **Thành viên 2** | **Detail** & **Contact** | - Hiển thị chi tiết bài viết theo ID. <br> - Xử lý form bình luận bằng jQuery (Client-side). |
| **Thành viên 3** | **Index** & **About** | - Tích hợp Slider chạy ảnh (Offline). <br> - Hiển thị tin mới nhất trang chủ. <br> - Thiết lập cấu trúc dự án ban đầu. |

---

## 5. HƯỚNG DẪN CÀI ĐẶT & VẬN HÀNH

Do dự án được đặt tại ổ **D:** và chạy chế độ **Offline**, vui lòng tuân thủ quy trình sau:

### Bước 1: Chuẩn bị Môi trường
1. Cài đặt **WampServer 3** (phiên bản 64-bit).
2. Cấu hình WAMP chạy **Port 8080**:
   * Mở file `httpd.conf` trong Apache.
   * Đổi `Listen 80` thành `Listen 8080`.
   * Đổi `ServerName localhost:80` thành `ServerName localhost:8080`.

### Bước 2: Cài đặt Mã nguồn
1. Chạy file script khởi tạo tự động (nếu có) tại `D:\s-news`.
2. **Quan trọng (Offline Mode):** Tải các thư viện sau và đặt vào thư mục `vendor/` theo đúng cấu trúc:
   * `vendor/bootstrap/css/bootstrap.min.css`
   * `vendor/bootstrap/js/bootstrap.bundle.min.js`
   * `vendor/jquery/jquery.min.js`

### Bước 3: Cấu hình Alias (Bắt buộc cho ổ D:)
Vì WAMP cài ở ổ C nhưng code nằm ở ổ D, cần tạo Alias:
1. Click chuột trái icon WAMP -> **Apache** -> **Alias directories** -> **Add an alias**.
2. Nhập tên alias: `s-news`
3. Nhập đường dẫn: `D:/s-news/`

### Bước 4: Cấu hình Database
1. Truy cập: `http://localhost:8080/phpmyadmin`
2. Chọn tab **Import**.
3. Chọn file `setup_database.sql` nằm trong thư mục dự án (`D:\s-news\`).
4. Nhấn **Go** để khởi tạo bảng và dữ liệu mẫu.

### Bước 5: Chạy ứng dụng
Mở trình duyệt và truy cập:
> **http://localhost:8080/s-news/**
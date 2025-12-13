<?php
class Database {
    // Thông tin để mở cửa kho dữ liệu (Bạn sửa lại nếu khác)
    private $host = "localhost";      // Địa chỉ máy chủ (thường là localhost)
    private $db_name = "s_news_db";   // Tên kho dữ liệu (Database Name)
    private $username = "root";       // Tên đăng nhập vào kho (XAMPP mặc định là root)
    private $password = "";           // Mật khẩu (XAMPP mặc định là rỗng)
    public $conn;                     // Biến lưu trữ kết nối đang mở

    // Hàm lấy kết nối (Giống như việc xin chìa khóa)
    public function getConnection() {
        $this->conn = null; // Ban đầu chưa có chìa khóa

        try {
            // Thử kết nối vào MySQL bằng thư viện PDO (PHP Data Objects)
            // Lệnh này giống như gõ cửa: "Cho tôi vào kho s_news_db với tên root"
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            // Cài đặt chế độ báo lỗi: Nếu có lỗi thì báo ngay lập tức (Exception)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Nếu kết nối thất bại (sai pass, sai tên db...) thì báo lỗi ra màn hình
            echo "Lỗi kết nối CSDL: " . $exception->getMessage();
        }

        // Trả về cái chìa khóa (biến kết nối) để các file khác dùng
        return $this->conn;
    }
}
?>
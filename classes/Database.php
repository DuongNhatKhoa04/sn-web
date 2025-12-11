<?php
class Database {
    // Thông tin đăng nhập vào "nhà kho" dữ liệu
    private $host = "localhost";    // Địa chỉ nhà kho (thường là máy hiện tại)
    private $db_name = "s_news_db"; // Tên của nhà kho (Cơ sở dữ liệu)
    private $username = "root";     // Tên người giữ kho (Mặc định là root)
    private $password = "";         // Mật khẩu (Mặc định để trống)
    public $conn;                   // Biến này sẽ giữ kết nối sau khi mở cửa thành công

    // Hàm thực hiện hành động mở cửa kết nối
    public function getConnection() {
        $this->conn = null; // Ban đầu chưa có kết nối
        try {
            // Thử kết nối với các thông tin đã khai báo ở trên
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Đặt chế độ hiển thị phông chữ tiếng Việt không bị lỗi (UTF-8)
            $this->conn->exec("set names utf8mb4");
            // Nếu có lỗi thì báo ngay lập tức để biết mà sửa
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Nếu kết nối thất bại, in ra màn hình lỗi gì
            echo "Loi ket noi: " . $exception->getMessage();
        }
        // Trả về kết nối đã mở thành công để các file khác sử dụng
        return $this->conn;
    }
}
?>
<?php
require_once '../classes/BasePage.php';
class AboutPage extends BasePage {
    protected function renderBody() {
        // ... (Copy nội dung file About mình đã gửi ở câu trả lời trước vào đây) ...
        // Để ngắn gọn, mình giữ nguyên cấu trúc cũ vì nó đã chuẩn rồi.
        echo '<div class="text-center"><h1>Giới thiệu S-NEWS</h1><p>Dự án môn học PHP...</p></div>';
        // (Bạn dán phần Code HTML About đẹp đẹp lúc nãy vào đây nhé)
    }
}
$page = new AboutPage("Giới thiệu");
$page->render();
?>
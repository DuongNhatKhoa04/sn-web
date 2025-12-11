$(document).ready(function() {
    // Khi form bình luận được Submit (người dùng bấm Gửi)
    $('#commentForm').submit(function(e) {
        e.preventDefault(); // Chặn việc tải lại trang mặc định
        var btn = $(this).find('button');
        var originalText = btn.html();
        
        // Khóa nút lại và hiện biểu tượng xoay xoay (loading)
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        // Gửi dữ liệu ngầm sang file add_comment.php
        $.ajax({
            url: '../api/add_comment.php',
            type: 'POST',
            data: {
                article_id: $('#article_id').val(),
                username: $('#username').val(),
                content: $('#content').val()
            },
            dataType: 'json',
            success: function(res) {
                // Khi server trả lời xong:
                btn.prop('disabled', false).html(originalText); // Mở khóa nút
                if (res.status === 'success') {
                    location.reload(); // Nếu thành công, tải lại trang để hiện bình luận mới
                } else {
                    alert(res.message); // Nếu lỗi, hiện thông báo
                }
            },
            error: function() {
                alert('Lỗi kết nối!'); // Nếu mất mạng hoặc lỗi server
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
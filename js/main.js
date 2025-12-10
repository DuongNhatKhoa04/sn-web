$(document).ready(function() {
    $('#commentForm').submit(function(e) {
        e.preventDefault();
        var btn = $(this).find('button');
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

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
                btn.prop('disabled', false).html(originalText);
                if (res.status === 'success') {
                    location.reload(); // Reload trang de hien comment moi
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('Lỗi kết nối!');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});

// HÀM XỬ LÝ LIKE KHÔNG CẦN LOAD LẠI TRANG
function toggleLike(btn, articleId) {
    // Tìm thẻ icon và thẻ số lượng bên trong nút bấm
    const icon = btn.querySelector('i');
    const countSpan = btn.querySelector('.like-count');
    
    // Hiệu ứng bấm nút ngay lập tức để UX mượt hơn
    btn.classList.add('active');
    
    // Kiểm tra xem đang ở trang chủ hay trang con để gọi API đúng đường dẫn
    // Nếu đang ở root thì api/api_like.php, nếu ở pages/ thì ../api/api_like.php
    let apiUrl = 'api/api_like.php';
    if (window.location.pathname.includes('/pages/')) {
        apiUrl = '../api/api_like.php';
    }

    // Gửi request lên server
    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: articleId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số like mới từ server trả về
            countSpan.innerText = data.new_likes;
            
            // Đổi icon tim rỗng thành tim đặc
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
            // Thêm màu đỏ
            btn.classList.add('text-danger');
        } else {
            console.error('Lỗi khi like:', data.message);
        }
    })
    .catch(error => {
        console.error('Lỗi kết nối:', error);
        // Nếu file api chưa đúng đường dẫn, thử đường dẫn dự phòng
        if (apiUrl === 'api/api_like.php') {
             // Thử gọi lại với đường dẫn khác (tuỳ cấu trúc thư mục của bạn)
        }
    });
}
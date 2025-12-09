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
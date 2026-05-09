<div class="modal" id="support-modal" aria-hidden="true">
	<div class="modal__dialog">
		<button class="modal__close" data-modal-close aria-label="Đóng">&times;</button>
		<h3>Yêu cầu hỗ trợ</h3>
		<p class="helper-text">Gặp vấn đề? Hãy cho chúng tôi biết để được hỗ trợ nhanh nhất.</p>
		<!-- Alert box hiển thị bởi JS -->
		<div id="issue-alert" style="display:none; margin-bottom:0.8rem;"></div>
		<form id="issueForm" class="form-stack">
			<div class="form-group">
				<label for="support-issue">Chủ đề</label>
				<input type="text" id="support-issue" name="issue" placeholder="Vấn đề bạn gặp phải" required>
			</div>
			<div class="form-group">
				<label for="support-description">Mô tả chi tiết</label>
				<textarea id="support-description" name="description" rows="5" placeholder="Mô tả vấn đề của bạn để chúng tôi hỗ trợ tốt hơn" required></textarea>
			</div>
			<button type="submit" id="issue-submit-btn" class="btn w-100">Gửi yêu cầu</button>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form      = document.getElementById('issueForm');
    var alertBox  = document.getElementById('issue-alert');
    var btn       = document.getElementById('issue-submit-btn');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        var token = localStorage.getItem('jwt_token');
        if (!token) {
            // Chưa đăng nhập: đóng modal support, mở modal signin
            var supportModal = document.getElementById('support-modal');
            if (supportModal) supportModal.classList.remove('is-visible');
            var signinModal = document.getElementById('signin-modal');
            if (signinModal) signinModal.classList.add('is-visible');
            return;
        }

        var payload = {
            issue:       document.getElementById('support-issue').value.trim(),
            description: document.getElementById('support-description').value.trim()
        };

        btn.disabled = true;
        btn.textContent = 'Đang gửi...';
        alertBox.style.display = 'none';

        try {
            var res = await fetch((window.BASE_API_URL || '/tour1/api/') + 'user/issues', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(payload)
            });
            var result = await res.json();

            if (result.success) {
                alertBox.className = 'alert success';
                alertBox.innerHTML = '<strong>Thành công:</strong> ' + result.message;
                alertBox.style.display = 'block';
                form.reset();
                if (typeof showToast === 'function') showToast(result.message, 'success');
                // Tự đóng modal sau 2 giây
                setTimeout(function () {
                    var modal = document.getElementById('support-modal');
                    if (modal) modal.classList.remove('is-visible');
                    alertBox.style.display = 'none';
                }, 2000);
            } else {
                alertBox.className = 'alert error';
                alertBox.innerHTML = '<strong>Lỗi:</strong> ' + (result.message || 'Đã xảy ra lỗi.');
                alertBox.style.display = 'block';
            }
        } catch (err) {
            alertBox.className = 'alert error';
            alertBox.innerHTML = '<strong>Lỗi:</strong> Không thể kết nối máy chủ.';
            alertBox.style.display = 'block';
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Gửi yêu cầu';
        }
    });
});
</script>


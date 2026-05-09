<?php
$iid = intval($_GET['iid'] ?? 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cập nhật ghi chú</title>
	<link rel="stylesheet" href="../admin/css/style.css">
    <script src="js/auth-guard.js"></script>
</head>
<body style="padding:2rem; background:#f5f7fb;">
	<div class="card" style="max-width:600px;margin:0 auto;">
		<h2>Cập nhật ghi chú yêu cầu</h2>
        <div id="issue-alert" style="display:none; margin-top:1rem;"></div>
		
		<div id="issue-content" style="margin-top:1.5rem;">
            <p style="text-align:center;">Đang tải dữ liệu...</p>
        </div>

		<div id="issue-form-container" style="display:none; margin-top:1rem;">
    		<form id="updateIssueForm" class="form-stack">
    			<div class="form-group">
    				<label for="remark">Ghi chú xử lý</label>
    				<textarea id="remark" rows="6" required placeholder="Nhập ghi chú xử lý của Admin..."></textarea>
    			</div>
    			<button type="submit" id="saveBtn" class="btn btn-primary">Lưu ghi chú & Đánh dấu đã xử lý</button>
    			<button type="button" class="btn btn-ghost" onclick="window.close();">Đóng</button>
    		</form>
        </div>

        <div id="issue-readonly" style="display:none; margin-top:1rem;">
            <p><strong>Ghi chú của Admin:</strong> <span id="readonly-remark"></span></p>
            <p><strong>Ngày xử lý:</strong> <span id="readonly-date"></span></p>
            <button type="button" class="btn btn-ghost" onclick="window.close();">Đóng</button>
        </div>
	</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const issueId = <?php echo $iid; ?>;
    const content = document.getElementById('issue-content');
    const formContainer = document.getElementById('issue-form-container');
    const readOnlyContainer = document.getElementById('issue-readonly');
    const alertBox = document.getElementById('issue-alert');
    const token = localStorage.getItem('jwt_token');

    if (!issueId) {
        content.innerHTML = '<div class="alert error">Mã yêu cầu không hợp lệ.</div>';
        return;
    }

    async function fetchIssue() {
        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/issues', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();
            
            if (result.success) {
                const issue = result.data.find(i => i.id == issueId);
                if (issue) {
                    renderIssue(issue);
                } else {
                    content.innerHTML = '<div class="empty-state">Không tìm thấy yêu cầu.</div>';
                }
            } else {
                content.innerHTML = '<div class="alert error">' + result.message + '</div>';
            }
        } catch (error) {
            content.innerHTML = '<div class="alert error">Lỗi kết nối máy chủ.</div>';
        }
    }

    function renderIssue(issue) {
        content.innerHTML = `
            <table class="table-details" style="width:100%; border-collapse:collapse; margin-bottom:1rem;">
                <tr><th style="text-align:left; padding:5px; border-bottom:1px solid #eee;">Người gửi:</th><td style="padding:5px; border-bottom:1px solid #eee;">${issue.FullName || 'Khách'} (${issue.UserEmail})</td></tr>
                <tr><th style="text-align:left; padding:5px; border-bottom:1px solid #eee;">Chủ đề:</th><td style="padding:5px; border-bottom:1px solid #eee;">${issue.Issue}</td></tr>
                <tr><th style="text-align:left; padding:5px; border-bottom:1px solid #eee;">Nội dung:</th><td style="padding:5px; border-bottom:1px solid #eee;">${issue.Description}</td></tr>
            </table>
        `;

        if (issue.AdminRemark) {
            document.getElementById('readonly-remark').textContent = issue.AdminRemark;
            document.getElementById('readonly-date').textContent = issue.AdminremarkDate || 'N/A';
            readOnlyContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'block';
        }
    }

    document.getElementById('updateIssueForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        const remark = document.getElementById('remark').value;

        btn.disabled = true;
        btn.textContent = 'Đang lưu...';

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/issues/' + issueId, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ remark: remark })
            });
            const result = await response.json();

            if (result.success) {
                alertBox.className = 'alert success';
                alertBox.textContent = result.message;
                alertBox.style.display = 'block';
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                alertBox.className = 'alert error';
                alertBox.textContent = result.message;
                alertBox.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Lưu ghi chú';
            }
        } catch (error) {
            alertBox.className = 'alert error';
            alertBox.textContent = 'Lỗi khi cập nhật.';
            alertBox.style.display = 'block';
            btn.disabled = false;
        }
    });

    fetchIssue();
});
</script>
</body>
</html>

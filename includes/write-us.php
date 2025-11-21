<div class="modal" id="support-modal" aria-hidden="true">
	<div class="modal__dialog">
		<button class="modal__close" data-modal-close aria-label="Đóng">&times;</button>
		<h3>Yêu cầu hỗ trợ</h3>
		<p class="helper-text">Gặp vấn đề? Hãy cho chúng tôi biết để được hỗ trợ nhanh nhất.</p>
		<form method="post" action="submit-issue.php" class="form-stack">
			<div class="form-group">
				<label for="support-issue">Chủ đề</label>
				<input type="text" id="support-issue" name="issue" placeholder="Vấn đề bạn gặp phải" required>
			</div>
			<div class="form-group">
				<label for="support-description">Mô tả chi tiết</label>
				<textarea id="support-description" name="description" rows="5" placeholder="Mô tả vấn đề của bạn để chúng tôi hỗ trợ tốt hơn" required></textarea>
			</div>
			<button type="submit" name="submit" class="btn w-100">Gửi yêu cầu</button>
		</form>
	</div>
</div>


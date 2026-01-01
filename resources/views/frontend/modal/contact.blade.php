<!-- Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="contactModalLabel">Thông tin liên hệ</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="contactForm" method="POST" action="{{ route('contact.store') }}">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label for="name">Họ và tên</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="Họ và tên" required>
					</div>
					<div class="form-group">
						<label for="phone">Số điện thoại</label>
						<input type="text" class="form-control" id="phone" name="phone" placeholder="Số điện thoại" required>
						<div class="invalid-feedback">Số điện thoại không hợp lệ</div>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email">
					</div>
					<div class="form-group">
						<label for="address">Địa chỉ</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ" required>
					</div>
					<div class="form-group">
						<label for="message">Ghi chú</label>
						<textarea class="form-control" id="message" name="message"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
					<button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Gửi thông tin</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!DOCTYPE html>
<html lang="vi">
<body>
    <h2>Thông báo: Có liên hệ mới</h2>
    <p>Một khách hàng vừa để lại thông tin liên hệ trên website với nội dung như sau:</p>
    <hr>
    <ul>
        <li><strong>Họ tên:</strong> {{ $contact->name }}</li>
        <li><strong>Điện thoại:</strong> {{ $contact->phone }}</li>
        <li><strong>Email:</strong> {{ $contact->email ?? 'Không cung cấp' }}</li>
        <li><strong>Địa chỉ:</strong> {{ $contact->address ?? 'Không cung cấp' }}</li>
        <li><strong>Ghi chú:</strong><br>{!! nl2br(e($contact->message)) !!}</li>
    </ul>
    <hr>
    <p>Vui lòng kiểm tra và liên hệ lại với khách hàng.</p>
</body>
</html>
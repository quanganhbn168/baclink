<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chào mừng bạn</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #ddd; border-radius: 5px; max-width: 600px; margin: auto; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Xin chào, {{ $customerName }}!</h1>
        <p>Cảm ơn bạn đã tin tưởng và đăng ký sử dụng dịch vụ của chúng tôi. Chúng tôi rất vui khi được đồng hành cùng bạn.</p>
        <p>Trân trọng,<br>Đội ngũ {{ config('app.name') }}</p>
    </div>
</body>
</html>
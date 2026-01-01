<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}">
    <style>
        body, html {
            height: 100%;
        }

        .login-left {
            background: url('/images/setting/login-bg.jpg') center center / cover no-repeat;
            height: 100vh;
        }

        .login-right {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo img {
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container-fluid h-100">
        <div class="row h-100 no-gutters">
            <!-- Bên trái: hình ảnh -->
            <div class="col-md-6 d-none d-md-block login-left"></div>

            <!-- Bên phải: form đăng nhập -->
            <div class="col-md-6 login-right">
                <div class="login-form">
                    <div class="login-logo">
                        <img src="{{asset($setting->logo)}}" alt="Logo">
                    </div>
                    <form method="POST" action="{{route('admin.login.submit')}}">
                        @csrf
                        <h3 class="text-center mb-4">Đăng nhập</h3>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                required
                            >
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="{{asset('/js/jquery-3.7.1.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/bootstrap/js/bootstrap.min.js')}}?{{time()}}"></script>
</body>
</html>

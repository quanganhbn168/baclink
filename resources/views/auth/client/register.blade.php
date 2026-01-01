@extends('layouts.master')
@section('title','Đăng ký tài khoản')

@push('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
{{-- Thêm một chút CSS inline để chỉnh alert cho đẹp hơn nếu cần --}}
<style>
    .alert-custom {
        border-left: 4px solid;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    .alert-danger-custom {
        border-left-color: #dc3545;
        background-color: #fff5f5;
        color: #b02a37;
    }
    .alert-success-custom {
        border-left-color: #198754;
        background-color: #f0fdf4;
        color: #146c43;
    }
</style>
@endpush

@section("content")
<div id="breadcrumb" class="bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light m-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đăng ký tài khoản</li>
            </ol>
        </nav>
    </div>
</div>

<div id="wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-box bg-white p-4 border shadow-sm rounded"> <h3 class="text-center mb-4 font-weight-bold text-uppercase">ĐĂNG KÝ TÀI KHOẢN</h3>

                    {{-- ======================================================= --}}
                    {{-- KHU VỰC HIỂN THỊ THÔNG BÁO (Thêm mới vào đây) --}}
                    {{-- ======================================================= --}}
                    
                    {{-- 1. Thông báo Lỗi tổng quát (Session error) --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-custom alert-dismissible fade show shadow-sm mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle mr-2" style="font-size: 1.2rem;"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- 2. Thông báo Thành công (Session success) --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-custom alert-dismissible fade show shadow-sm mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle mr-2" style="font-size: 1.2rem;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- 3. Tổng hợp lỗi Validate (Nếu có nhiều lỗi) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-danger-custom alert-dismissible fade show shadow-sm mb-3" role="alert">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-bug mr-2"></i>
                                <strong>Vui lòng kiểm tra lại dữ liệu:</strong>
                            </div>
                            <ul class="mb-0 pl-3 small" style="list-style-type: disc;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    {{-- ======================================================= --}}

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            {{-- Thêm icon vào input để đẹp hơn --}}
                            <div class="input-group">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Họ và tên" value="{{ old('name') }}" required>
                            </div>
                            @error('name')
                                <small class="text-danger mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            {{-- Thêm icon vào input để đẹp hơn --}}
                            <div class="input-group">
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Số điện thoại" value="{{ old('phone') }}" required>
                            </div>
                            @error('phone')
                                <small class="text-danger mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required>
                             @error('email')
                                <small class="text-danger mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Mật khẩu" required>
                             @error('password')
                                <small class="text-danger mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                        </div>

                        <button type="submit" class="btn btn-login w-100 my-3 font-weight-bold shadow-sm">
                            <i class="fas fa-user-plus mr-1"></i> ĐĂNG KÝ
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <span class="text-muted">Bạn đã có tài khoản? </span>
                        <a href="{{ route('login') }}" class="font-weight-bold text-primary" style="text-decoration: none;">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
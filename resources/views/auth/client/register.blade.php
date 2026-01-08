@extends('layouts.master')
@section('title', 'Đăng ký Hội viên')
@push('css')
    @vite(['resources/css/custom/auth.css'])
@endpush
@section('content')
<div class="register-page py-5 bg-light">
    <div class="container custom-container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h1 class="font-weight-bold text-uppercase" style="color: var(--blue); font-family: inherit;">Thông tin đăng ký gia nhập hội<br><span style="color: var(--gold);">BACLINK</span></h1>
                            <p class="text-muted mt-3">
                                Để đăng ký gia nhập Hội, doanh nghiệp vui lòng đọc Thông tin Quyền và Nghĩa vụ Hội viên tại: 
                                <a href="#" class="text-primary">Đường dẫn này</a>
                            </p>
                            <p>Doanh nghiệp vui lòng đăng ký các thông tin sau:</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            {{-- Thông tin doanh nghiệp --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Tên Công ty <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="Nhập tên công ty đầy đủ">
                                @error('company_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Người đại diện --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Danh xưng <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio" id="honorific_mr" name="honorific" value="Ông" class="custom-control-input" {{ old('honorific', 'Ông') == 'Ông' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="honorific_mr">Ông</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="honorific_mrs" name="honorific" value="Bà" class="custom-control-input" {{ old('honorific') == 'Bà' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="honorific_mrs">Bà</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Tên chủ doanh nghiệp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Họ và tên người đại diện">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Chức danh Công ty <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') }}" placeholder="Ví dụ: Giám đốc, Chủ tịch HĐQT">
                                @error('position')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="font-weight-bold">Số điện thoại người đại diện <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label class="font-weight-bold">Email người đại diện</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mật khẩu (Required for User creation) --}}
                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="font-weight-bold">Mật khẩu đăng nhập <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label class="font-weight-bold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            {{-- Nhóm ngành --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold d-block mb-2">Nhóm ngành sản xuất <span class="text-danger">*</span></label>
                                @php
                                    $sectors = [
                                        'Điện, điện tử',
                                        'Công nghệ thông tin, kinh tế số',
                                        'Cơ khí, chế tạo',
                                        'Chế biến nông sản, thực phẩm',
                                        'Hóa chất, nhựa dược phẩm',
                                        'Dệt may, da giày',
                                        'Vật liệu xây dựng',
                                        'Sản phẩm thủ công mỹ nghệ'
                                    ];
                                @endphp
                                @foreach($sectors as $sector)
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="sector_{{ $loop->index }}" name="business_sector" value="{{ $sector }}" class="custom-control-input" {{ (old('business_sector') == $sector) || ($loop->first && is_null(old('business_sector'))) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sector_{{ $loop->index }}">{{ $sector }}</label>
                                    </div>
                                @endforeach
                                @error('business_sector')
                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Thông tin chi tiết --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Giới thiệu về Công ty <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('company_intro') is-invalid @enderror" name="company_intro" rows="5">{{ old('company_intro') }}</textarea>
                                @error('company_intro')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Sản phẩm nổi bật <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('featured_products') is-invalid @enderror" name="featured_products" value="{{ old('featured_products') }}" placeholder="Liệt kê các sản phẩm chính">
                                @error('featured_products')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Website Công ty <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website') }}" placeholder="https://...">
                                @error('website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <hr class="my-5">

                            {{-- Trợ lý --}}
                            <h5 class="font-weight-bold mb-4">Thông tin Trợ lý / Thư ký (Nếu có)</h5>
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Họ và tên (Trợ lý/ Thư ký)</label>
                                <input type="text" class="form-control" name="assistant_name" value="{{ old('assistant_name') }}">
                            </div>

                             <div class="form-group mb-4">
                                <label class="font-weight-bold">Số điện thoại (Trợ lý/ Thư ký)</label>
                                <input type="text" class="form-control" name="assistant_phone" value="{{ old('assistant_phone') }}">
                            </div>

                             <div class="form-group mb-5">
                                <label class="font-weight-bold">Email (Trợ lý/ Thư ký)</label>
                                <input type="email" class="form-control" name="assistant_email" value="{{ old('assistant_email') }}">
                            </div>

                            <div class="text-center">
<button type="submit" class="btn btn-gold btn-lg px-5 font-weight-bold text-uppercase">Đăng ký hội viên</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

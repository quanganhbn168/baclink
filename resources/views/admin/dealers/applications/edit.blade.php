@extends('layouts.admin')

@section('title', 'Chi tiết Hồ sơ Đại lý')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chi tiết Hồ sơ <small>#{{ $application->id }}</small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dealer-applications.index') }}">Đăng ký Đại lý</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin đăng ký</h3>
                    </div>
                    
                    <form action="{{ route('admin.dealer-applications.update', $application->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            {{-- Hiển thị lỗi --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $application->name) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $application->phone) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $application->email) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="company">Tên Công ty / Cửa hàng</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" id="company" name="company" class="form-control" value="{{ old('company', $application->company) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Địa chỉ</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $application->address) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Lời nhắn / Ghi chú</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Nội dung khách hàng để lại...">{{ old('message', $application->message) }}</textarea>
                            </div>

                            <div class="form-group bg-light p-3 border rounded">
                                <label class="mb-2">Trạng thái xử lý</label>
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio mr-4">
                                        <input class="custom-control-input" type="radio" id="status0" name="status" value="0" {{ $application->status == 0 ? 'checked' : '' }}>
                                        <label for="status0" class="custom-control-label text-warning">Chờ xử lý</label>
                                    </div>
                                    <div class="custom-control custom-radio mr-4">
                                        <input class="custom-control-input" type="radio" id="status1" name="status" value="1" {{ $application->status == 1 ? 'checked' : '' }}>
                                        <label for="status1" class="custom-control-label text-success">Đã duyệt</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="status2" name="status" value="2" {{ $application->status == 2 ? 'checked' : '' }}>
                                        <label for="status2" class="custom-control-label text-danger">Hủy / Từ chối</label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
                            <a href="{{ route('admin.dealer-applications.index') }}" class="btn btn-default float-right">Quay lại danh sách</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

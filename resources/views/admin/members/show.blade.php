@extends('layouts.admin')

@section('title', 'Chi tiết Hội viên')

@section('content_header')
    <h1>Chi tiết Hội viên: {{ $member->company_name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thông tin đăng ký</h3>
                    <div class="card-tools">
                         <a href="{{ route('admin.members.index') }}" class="btn btn-default btn-sm">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-building mr-1"></i> Tên Công ty</strong>
                            <p class="text-muted">{{ $member->company_name }}</p>
                            <hr>

                            <strong><i class="fas fa-user-tie mr-1"></i> Người đại diện</strong>
                            <p class="text-muted">
                                {{ $member->honorific }} {{ $member->representative_name }} <br>
                                <small>Chức vụ: {{ $member->position }}</small>
                            </p>
                            <hr>

                            <strong><i class="fas fa-phone mr-1"></i> Liên hệ</strong>
                            <p class="text-muted">
                                SĐT: {{ $member->phone }}<br>
                                Email: {{ $member->user ? $member->user->email : 'Không có email' }}<br>
                                Website: {{ $member->website }}
                            </p>
                            <hr>
                             <strong><i class="fas fa-user-friends mr-1"></i> Trợ lý (nếu có)</strong>
                            <p class="text-muted">
                                Tên: {{ $member->assistant_name ?? '---' }}<br>
                                SĐT: {{ $member->assistant_phone ?? '---' }}<br>
                                Email: {{ $member->assistant_email ?? '---' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-industry mr-1"></i> Nhóm ngành sản xuất</strong>
                            <p class="text-muted">{{ $member->business_sector }}</p>
                            <hr>

                            <strong><i class="fas fa-info-circle mr-1"></i> Giới thiệu công ty</strong>
                            <p class="text-muted">{!! nl2br(e($member->company_intro)) !!}</p>
                            <hr>

                             <strong><i class="fas fa-cubes mr-1"></i> Sản phẩm tiêu biểu</strong>
                            <p class="text-muted">{!! nl2br(e($member->featured_products)) !!}</p>
                            <hr>

                            <strong><i class="fas fa-clock mr-1"></i> Ngày đăng ký</strong>
                            <p class="text-muted">{{ $member->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hội viên này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger float-right">
                            <i class="fas fa-trash"></i> Xóa Hội viên này
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@extends('layouts.master')
@push('css')
    @vite(['resources/css/custom/post.css'])
@endpush
@section('title', $career->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">{{ $career->name }}</h1>
            
            <div class="mb-4">
                <h3>Mô tả công việc</h3>
                {!! $career->description !!}
            </div>
            
            <div class="mb-4">
                <h3>Yêu cầu ứng viên</h3>
                {!! $career->requirements !!}
            </div>

            <div class="mb-4">
                <h3>Quyền lợi được hưởng</h3>
                {!! $career->benefits !!}
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Thông tin chung</div>
                <div class="card-body">
                    <p><strong>Mức lương:</strong> {{ $career->salary ?? 'Thỏa thuận' }}</p>
                    <p><strong>Số lượng:</strong> {{ $career->quantity ?? 'Không giới hạn' }}</p>
                    <p><strong>Kinh nghiệm:</strong> {{ $career->experience ?? 'Không yêu cầu' }}</p>
                    <p><strong>Hạn nộp hồ sơ:</strong> {{ $career->deadline ? $career->deadline->format('d/m/Y') : 'Không thời hạn' }}</p>
                    <a href="#apply-form" class="btn btn-primary w-100">Ứng tuyển ngay</a>
                </div>
            </div>

            {{-- Có thể thêm form ứng tuyển ở đây --}}
            <div class="card mt-4" id="apply-form">
                <div class="card-header">Form ứng tuyển</div>
                <div class="card-body">
                    {{-- Form HTML... --}}
                    <p>Gửi CV về email: contact@tantiengroup.vn</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

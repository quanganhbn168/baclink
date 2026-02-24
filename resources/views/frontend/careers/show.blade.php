@extends('layouts.master')
@push('css')
    @vite(['resources/css/custom/post.css'])
@endpush
@section('title', $career->name)
@section('meta_description', Str::limit(strip_tags($career->description), 160))
@section('meta_image', $setting->share_image)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">{{ $career->name }}</h1>
            
            <div class="mb-4">
                <h3>{{ __('Mô tả công việc') }}</h3>
                {!! $career->description !!}
            </div>
            
            <div class="mb-4">
                <h3>{{ __('Yêu cầu ứng viên') }}</h3>
                {!! $career->requirements !!}
            </div>

            <div class="mb-4">
                <h3>{{ __('Quyền lợi được hưởng') }}</h3>
                {!! $career->benefits !!}
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">{{ __('Thông tin chung') }}</div>
                <div class="card-body">
                    <p><strong>{{ __('Mức lương') }}:</strong> {{ $career->salary ?? __('Thỏa thuận') }}</p>
                    <p><strong>{{ __('Số lượng') }}:</strong> {{ $career->quantity ?? __('Không giới hạn') }}</p>
                    <p><strong>{{ __('Kinh nghiệm') }}:</strong> {{ $career->experience ?? __('Không yêu cầu') }}</p>
                    <p><strong>{{ __('Hạn nộp hồ sơ') }}:</strong> {{ $career->deadline ? $career->deadline->format('d/m/Y') : __('Không thời hạn') }}</p>
                    <a href="#apply-form" class="btn btn-primary w-100">{{ __('Ứng tuyển ngay') }}</a>
                </div>
            </div>

            {{-- Có thể thêm form ứng tuyển ở đây --}}
            <div class="card mt-4" id="apply-form">
                <div class="card-header">{{ __('Form ứng tuyển') }}</div>
                <div class="card-body">
                    {{-- Form HTML... --}}
                    <p>{{ __('Gửi CV về email') }}: contact@tantiengroup.vn</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.master')
@section('title', __('Tuyển dụng') . ' - ' . $setting->name)
@section('meta_description', __('Tuyển dụng') . ' - ' . $setting->name)
@section('meta_image', $setting->share_image)

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ __('Tin tuyển dụng') }}</h1>
    <div class="row">
        @forelse($careers as $career)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $career->name }}</h5>
                        <p class="card-text"><strong>{{ __('Hạn nộp') }}:</strong> {{ $career->deadline ? $career->deadline->format('d/m/Y') : __('Không thời hạn') }}</p>
                        <p class="card-text"><strong>{{ __('Số lượng') }}:</strong> {{ $career->quantity ?? __('Không giới hạn') }}</p>
                        <a href="{{ route('frontend.careers.show', $career) }}" class="btn btn-primary">{{ __('Xem chi tiết') }}</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>{{ __('Hiện tại chưa có tin tuyển dụng nào.') }}</p>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">
        {{ $careers->links() }}
    </div>
</div>
@endsection

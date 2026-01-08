@extends('layouts.master')
@section('title', 'Tuyển dụng')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Tin tuyển dụng</h1>
    <div class="row">
        @forelse($careers as $career)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $career->name }}</h5>
                        <p class="card-text"><strong>Hạn nộp:</strong> {{ $career->deadline ? $career->deadline->format('d/m/Y') : 'Không thời hạn' }}</p>
                        <p class="card-text"><strong>Số lượng:</strong> {{ $career->quantity ?? 'Không giới hạn' }}</p>
                        <a href="{{ route('frontend.careers.show', $career) }}" class="btn btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Hiện tại chưa có tin tuyển dụng nào.</p>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">
        {{ $careers->links() }}
    </div>
</div>
@endsection

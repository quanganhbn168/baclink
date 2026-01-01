{{-- Giả sử bạn có layout chung là 'frontend.layouts.app' --}}
@extends('layouts.master') 

@section('title', 'Kết quả tìm kiếm cho: ' . $keyword)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="search-header mb-4">
                <h1 class="h2">Kết quả tìm kiếm</h1>
                <p class="lead">
                    Tìm thấy <strong class="text-danger">{{ $results->total() }}</strong> kết quả cho từ khóa: <strong>"{{ $keyword }}"</strong>
                </p>
            </div>

            <div class="search-results">
                @forelse($results as $result)
                    <div class="card mb-3 shadow-sm product-search-item">
                        <div class="row no-gutters">
                            <div class="col-md-2 p-2">
                                <a href="{{ route('frontend.slug.handle', $result->slug ?? $result->id) }}">
                                    <img src="{{ optional($result->mainImage())->url() ?? asset('images/no-image.png') }}" class="card-img" alt="{{ $result->name }}" style="max-height: 100px; object-fit: contain;">
                                </a>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h5 class="card-title mb-0" style="font-size: 1.1rem;">
                                            <a href="{{ route('frontend.slug.handle', $result->slug ?? $result->id) }}" class="text-decoration-none text-dark font-weight-bold">
                                                {{ $result->name }}
                                            </a>
                                        </h5>
                                        <span class="badge badge-primary">Sản phẩm</span>
                                    </div>
                                    <p class="card-text text-muted mb-2 small">
                                        <span class="text-danger font-weight-bold">{{ number_format($result->price ?? 0) }}đ</span>
                                        @if($result->old_price)
                                            <del class="text-muted ml-2 small">{{ number_format($result->old_price) }}đ</del>
                                        @endif
                                    </p>
                                    <p class="card-text text-muted small mb-0">
                                        {{ Str::limit(strip_tags($result->description), 120) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i> Rất tiếc, không tìm thấy kết quả nào phù hợp với từ khóa của bạn.
                    </div>
                @endforelse
            </div>

            {{-- Hiển thị link phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $results->links() }}
            </div>

        </div>
    </div>
</div>
@endsection
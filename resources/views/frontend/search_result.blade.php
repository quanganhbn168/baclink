@extends('layouts.master') 

@section('title', 'Kết quả tìm kiếm cho: ' . $keyword)

{{-- Push styles --}}
@push('css')
    @vite(['resources/css/custom/search.css', 'resources/css/custom/premium_bg.css'])
@endpush

@section('content')
{{-- Header Section with Dot Grid Pattern --}}
<div class="search-header-wrapper bg-pattern-grid text-center">
    <div class="container container-custom">
        <h1 class="h2 font-weight-bold mb-2">ĐANG TÌM KIẾM</h1>
        <div class="search-title h4">
            "{{ $keyword }}"
        </div>
        <p class="text-muted mb-0">
            Tìm thấy <strong class="text-gold">{{ $results->total() }}</strong> kết quả phù hợp
        </p>
    </div>
</div>

<div class="container container-custom py-5 bg-white min-vh-50">
    @if($results->count() > 0)
        <div class="search-results-grid">
            @foreach($results as $result)
            <div class="search-card-wrapper h-100">
                <div class="search-card shadow-sm">
                    <div class="search-card-img-wrapper">
                        {{-- Badge (Optional logic: News vs Product) --}}
                        @if(isset($result->price))
                            <span class="search-card-badge">Sản phẩm</span>
                        @else
                            <span class="search-card-badge" style="background: var(--blue);">Tin tức</span>
                        @endif

                        <a href="{{ route('frontend.slug.handle', $result->slug ?? $result->id) }}" class="d-block h-100">
                            <img src="{{ optional($result->mainImage())->url() ?? asset('images/setting/no-image.png') }}" 
                                 class="search-card-img" 
                                 alt="{{ $result->name }}">
                        </a>
                    </div>
                    
                    <div class="search-card-body">
                        <h3 class="search-card-title">
                            <a href="{{ route('frontend.slug.handle', $result->slug ?? $result->id) }}">
                                {{ $result->name }}
                            </a>
                        </h3>
                        
                        <p class="search-card-desc">
                            {{ Str::limit(strip_tags($result->description ?? $result->content), 100) }}
                        </p>

                        @if(isset($result->price))
                        <div class="search-card-price">
                            {{ number_format($result->price) }}đ
                            @if($result->old_price)
                                <del class="text-muted small font-weight-normal ml-2">{{ number_format($result->old_price) }}đ</del>
                            @endif
                        </div>
                        @else
                        <div class="mt-auto">
                            <a href="{{ route('frontend.slug.handle', $result->slug ?? $result->id) }}" class="text-gold small font-weight-bold text-decoration-none">
                                Xem chi tiết <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $results->links() }}
        </div>

    @else
        {{-- Zero State --}}
        <div class="search-no-result text-center bg-texture-noise mt-3">
            <div class="search-icon-large text-muted opacity-50 mb-3">
                <i class="fas fa-search"></i>
            </div>
            <h4 class="font-weight-bold opacity-75">Không tìm thấy kết quả nào</h4>
            <p class="text-muted">Rất tiếc, chúng tôi không tìm thấy nội dung phù hợp cho từ khóa <strong>"{{ $keyword }}"</strong>.</p>
            <a href="{{ route('home') }}" class="btn btn-gold rounded-pill px-4 mt-3 shadow-sm">
                <i class="fas fa-home mr-2"></i> Quay về trang chủ
            </a>
        </div>
    @endif
</div>
@endsection

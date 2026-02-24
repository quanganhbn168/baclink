@extends('layouts.master')
@section('title', ($category ? $category->getTranslation('name') : __('Tin tức')) . ' - ' . $setting->getTranslation('name'))

@push('css')
    @vite(['resources/css/custom/post.css'])
@endpush

@section('content')
<main class="post-detail-wrapper bg-white">
    <div class="container container-custom">
        <!-- Breadcrumb -->
        <x-frontend.breadcrumb :items="[
            ['label' => __('Tin tức'), 'url' => route('frontend.slug.handle', 'tin-tuc')],
            ['label' => $category ? $category->getTranslation('name') : __('Tin tức'), 'url' => '']
        ]" />

        <div class="row">
            <!-- Left Column: Post List -->
            <div class="col-lg-9">
                <h1 class="sidebar-heading text-blue" style="font-size: 24px; border-bottom: none; margin-bottom: 30px;">{{ $category ? $category->getTranslation('name') : __('Tin tức') }}</h1>

                <div class="cate-post-list">
                    @forelse($posts as $index => $post)
                        @if($index == 0 && $posts->currentPage() == 1)
                            <!-- Featured First Item (Image Left, Text Right) -->
                            <div class="cate-featured-item">
                                <div class="cf-thumb">
                                    <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                                        <img src="{{ optional($post->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $post->getTranslation('title') }}">
                                    </a>
                                </div>
                                <div class="cf-info">
                                    <h2 class="cf-title">
                                        <a href="{{ route('frontend.slug.handle', $post->slug) }}">{{ $post->getTranslation('title') }}</a>
                                    </h2>
                                    <div class="cf-excerpt">
                                        {{ Str::limit(strip_tags($post->getTranslation('description')), 250) }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Standard Item (Text Left, Image Right) -->
                            <div class="cate-standard-item">
                                <div class="cs-info">
                                    <h3 class="cs-title">
                                        <a href="{{ route('frontend.slug.handle', $post->slug) }}">{{ $post->getTranslation('title') }}</a>
                                    </h3>
                                    <p class="cs-excerpt">
                                        {{ Str::limit(strip_tags($post->getTranslation('description')), 200) }}
                                    </p>
                                </div>
                                <div class="cs-thumb">
                                    <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                                        <img src="{{ optional($post->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $post->getTranslation('title') }}">
                                    </a>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="alert alert-info">{{ __('Hiện chưa có bài viết nào trong danh mục này.') }}</div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="custom-pagination">
                    {{ $posts->links('pagination::bootstrap-4') }}
                </div>

                <!-- Ads Banner -->
                <div class="ads-banner-section mt-5">
                    <div class="ads-banner-wrapper">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1200&h=250" alt="Quảng cáo">
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-3">
                <aside class="post-sidebar">
                    <!-- Red CTA Card -->
                    <a href="{{ route('register') }}" class="sidebar-cta-card">
                        <div class="card-icon"><i class="fas fa-id-card"></i></div>
                        <div class="card-content">
                            <span class="card-title">{{ __('Đăng ký hội viên') }}</span>
                            <span class="card-desc">{{ __('Hội Công nghiệp chủ lực Thành phố Bắc Ninh') }}</span>
                        </div>
                    </a>

                    <!-- Trending Section -->
                    <div class="sidebar-trending">
                        <h3 class="sidebar-heading">{{ __('Xem nhiều') }}</h3>
                        @foreach($trendingPosts as $trend)
                            <div class="trending-item">
                                <div class="trending-thumb">
                                    <a href="{{ route('frontend.slug.handle', $trend->slug) }}">
                                        <img src="{{ optional($trend->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $trend->getTranslation('title') }}">
                                    </a>
                                </div>
                                <div class="trending-info">
                                    <h4 style="margin-bottom: 5px;"><a href="{{ route('frontend.slug.handle', $trend->slug) }}">{{ Str::limit($trend->getTranslation('title'), 60) }}</a></h4>
                                    <div class="trending-date" style="font-size: 11px; color: #999;">{{ $trend->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>
@endsection

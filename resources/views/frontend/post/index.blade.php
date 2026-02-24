@extends('layouts.master')
@section('title', 'Tin tức - ' . $setting->name)

@push('css')
    @vite(['resources/css/custom/post.css'])
@endpush

@section('content')
<main class="post-detail-wrapper bg-white">
    <div class="container container-custom">
        <!-- Breadcrumb -->
        <x-frontend.breadcrumb :items="[
            ['label' => __('Tin tức'), 'url' => '']
        ]" />

        <h1 class="member-page-title text-blue" style="text-align: left; margin: 20px 0 40px; padding-bottom: 0;">{{ __('TIN MỚI - SỰ KIỆN') }}</h1>

        <div class="row">
            <!-- Left Column: Aggregated News -->
            <div class="col-lg-9">
                <div class="news-aggregate-grid">
                    @foreach($categories as $cat)
                        @if($cat->posts->isNotEmpty())
                            <div class="cate-block">
                                <div class="block-header">
                                    <h3>{{ $cat->getTranslation('name') }}</h3>
                                    <a href="{{ route('frontend.slug.handle', $cat->slug) }}" class="view-all">{{ __('Tất cả') }} ></a>
                                </div>
                                
                                @php $firstPost = $cat->posts->first(); @endphp
                                <div class="main-item-agg">
                                    <div class="agg-thumb">
                                        <a href="{{ route('frontend.slug.handle', $firstPost->slug) }}">
                                            <img src="{{ optional($firstPost->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $firstPost->getTranslation('title') }}">
                                        </a>
                                    </div>
                                    <h4 class="agg-title">
                                        <a href="{{ route('frontend.slug.handle', $firstPost->slug) }}">{{ $firstPost->getTranslation('title') }}</a>
                                    </h4>
                                </div>

                                <ul class="sub-items-agg">
                                    @foreach($cat->posts->skip(1) as $subPost)
                                        <li>
                                            <a href="{{ route('frontend.slug.handle', $subPost->slug) }}">{{ $subPost->getTranslation('title') }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
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

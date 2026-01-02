@extends('layouts.master')
@section('title', $post->title)
@section('meta_description', $post->meta_description ?? $post->description)
@section('meta_image', optional($post->mainImage())->url() ?? $setting->meta_image)
@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ $post->slug_url ?? url()->current() }}"
  },
  "headline": "{{ $post->title }}",
  "description": "{{ Str::limit(strip_tags($post->summary ?? $post->content), 160) }}",
  "image": [
    "{{ optional($post->mainImage())->url() ?? asset($setting->meta_image) }}" 
  ],
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@type": "Person",
    "name": "{{ $post->user->name ?? 'Admin' }}",
    "url": "{{ url('/') }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ $setting->name }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset($setting->logo) }}"
    }
  }
}
</script>
@endpush
@push('css')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
    .toc-container {
        border: 1px solid #aaa;
        background: #f9f9f9;
        padding: 16px;
        margin-bottom: 24px;
        max-height: 300px;
        overflow-y: auto;
    }
    .toc-container h3 { font-size: 18px; margin-bottom: 10px; }
    .toc-container ul { list-style: decimal inside; padding-left: 0; }
    .toc-container ul ul { list-style-type: decimal; margin-left: 20px; }
    </style>
@endpush
@section('content')
<main class="post-detail-wrapper bg-white">
    <div class="container container-custom">
        <!-- Breadcrumb -->
        <nav class="post-breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            @if($post->category)
                <span>»</span>
                <a href="{{ route('frontend.slug.handle', $post->category->slug) }}">{{ $post->category->name }}</a>
            @endif
            <span>»</span>
            <span class="active">{{ Str::limit($post->title, 50) }}</span>
        </nav>

        <div class="row">
            <!-- Left Column: Content -->
            <div class="col-lg-9">
                <h1 class="post-title">{{ $post->title }}</h1>
                
                <div class="post-meta">
                    <i class="far fa-clock"></i>
                    <span>{{ $post->created_at->format('H:i') }} thứ {{ $post->created_at->dayOfWeek == 0 ? 'chủ nhật' : ($post->created_at->dayOfWeek + 1) }} ngày {{ $post->created_at->format('d/m/Y') }}</span>
                </div>

                <div class="post-main-content">
                    <!-- Share sidebar -->
                    <aside class="post-share-sidebar">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="share-btn share-fb"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="share-btn share-gp"><i class="fab fa-google-plus-g"></i></a>
                        <a href="#" class="share-btn share-tw"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="share-btn share-zl"><img src="https://img.icons8.com/ios-filled/20/ffffff/zalo.png" style="width: 15px;" alt="Zalo"/></a>
                        <a href="javascript:window.print()" class="share-btn share-pr"><i class="fas fa-print"></i></a>
                        <a href="mailto:?subject={{ $post->title }}&body={{ url()->current() }}" class="share-btn share-em"><i class="fas fa-envelope"></i></a>
                    </aside>

                    <!-- Article Body -->
                    <article class="post-article">
                        @if($post->description)
                            <div class="post-intro">
                                {{ $post->description }}
                            </div>
                        @endif

                        <div class="post-content-body ck-content">
                            {!! $post->content !!}
                        </div>
                    </article>
                </div>

                <!-- Related Posts -->
                <div class="related-post-section mt-5">
                    <div class="related-posts-list">
                        @foreach($relatedPosts as $related)
                            <div class="related-post-horizontal">
                                <div class="rp-thumb">
                                    <a href="{{ route('frontend.slug.handle', $related->slug) }}">
                                        <img src="{{ optional($related->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $related->title }}">
                                    </a>
                                </div>
                                <div class="rp-info">
                                    <span class="rp-label">TIN LIÊN QUAN</span>
                                    <h4 class="rp-title">
                                        <a href="{{ route('frontend.slug.handle', $related->slug) }}">{{ $related->title }}</a>
                                    </h4>
                                    <p class="rp-excerpt">{{ Str::limit(strip_tags($related->description), 180) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Ads Banner -->
                <div class="ads-banner-section mb-5">
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
                            <span class="card-title">Đăng ký hội viên</span>
                            <span class="card-desc">Hội Công nghiệp chủ lực Thành phố Bắc Ninh</span>
                        </div>
                    </a>

                    <!-- Trending Section -->
                    <div class="sidebar-trending">
                        <h3 class="sidebar-heading">Xem nhiều</h3>
                        @foreach($trendingPosts as $trend)
                            <div class="trending-item">
                                <div class="trending-thumb">
                                    <a href="{{ route('frontend.slug.handle', $trend->slug) }}">
                                        <img src="{{ optional($trend->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $trend->title }}">
                                    </a>
                                </div>
                                <div class="trending-info">
                                    <h4><a href="{{ route('frontend.slug.handle', $trend->slug) }}">{{ Str::limit($trend->title, 60) }}</a></h4>
                                    <div class="trending-date">{{ $trend->created_at->format('d/m/Y') }}</div>
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
@push('js')
@endpush

@extends('layouts.master')
@section('title', $field->name)
@section('meta_description', $field->meta_description ?? $field->summary)
@section('meta_image', optional($field->mainImage())->url() ?? $setting->meta_image)

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $field->slug_url ?? url()->current() }}"
      },
      "headline": "{{ $field->name }}",
      "description": "{{ Str::limit(strip_tags($field->summary ?? $field->content), 160) }}",
      "image": [
        "{{ optional($field->mainImage())->url() ?? asset($setting->meta_image) }}"
      ],
      "datePublished": "{{ $field->created_at->toIso8601String() }}",
      "dateModified": "{{ $field->updated_at->toIso8601String() }}",
      "publisher": {
        "@type": "Organization",
        "name": "{{ $setting->name }}",
        "logo": {
          "@type": "ImageObject",
          "url": "{{ asset($setting->logo) }}"
        }
      }
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Trang chủ",
          "item": "{{ url('/') }}"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Lĩnh vực hoạt động",
          "item": "{{ route('frontend.fields.index') }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $field->name }}",
          "item": "{{ url()->current() }}"
        }
      ]
    }
  ]
}
</script>
@endpush

@push('css')
    @vite(['resources/css/custom/post.css'])
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
            <span>»</span>
            <a href="{{ route('frontend.fields.index') }}">Lĩnh vực</a>
            @if($field->category)
                <span>»</span>
                <a href="#">{{ $field->category->name }}</a>
            @endif
            <span>»</span>
            <span class="active">{{ Str::limit($field->name, 50) }}</span>
        </nav>

        <div class="row">
            <!-- Left Column: Content -->
            <div class="col-lg-9">
                <h1 class="post-title">{{ $field->name }}</h1>
                
                <div class="post-meta">
                    <i class="far fa-clock"></i>
                    <span>Cập nhật: {{ $field->updated_at->format('d/m/Y') }}</span>
                </div>

                <div class="post-main-content">
                    <!-- Share sidebar -->
                    <aside class="post-share-sidebar">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="share-btn share-fb"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="share-btn share-gp"><i class="fab fa-google-plus-g"></i></a>
                        <a href="#" class="share-btn share-tw"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="share-btn share-zl"><img src="https://img.icons8.com/ios-filled/20/ffffff/zalo.png" style="width: 15px;" alt="Zalo"/></a>
                        <a href="javascript:window.print()" class="share-btn share-pr"><i class="fas fa-print"></i></a>
                        <a href="mailto:?subject={{ $field->name }}&body={{ url()->current() }}" class="share-btn share-em"><i class="fas fa-envelope"></i></a>
                    </aside>

                    <!-- Article Body -->
                    <article class="post-article">
                        @if($field->summary)
                            <div class="post-intro">
                                {{ $field->summary }}
                            </div>
                        @endif

                        <div class="post-content-body ck-content">
                            {!! $field->content !!}
                        </div>
                    </article>
                </div>

                <!-- Related Posts (Using passed $relatedPosts) -->
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
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
                @endif

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
                    @if(isset($trendingPosts) && $trendingPosts->count() > 0)
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
                    @endif
                </aside>
            </div>
        </div>
    </div>
</main>
@endsection
@push('js')
<script>
    // Copy toggle logic from post detail if needed, or remove if unused
    document.querySelectorAll('.post-content-body table').forEach(table => {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
</script>
@endpush

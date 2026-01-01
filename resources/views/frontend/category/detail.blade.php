@extends('layouts.master')
@section('title', $post->title)
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
    .toc-container h3 {
        font-size: 18px;
        margin-bottom: 10px;
    }
    .toc-container ul {
        list-style: decimal inside;
        padding-left: 0;
    }
    .toc-container ul ul {
        list-style-type: decimal;
        margin-left: 20px;
    }
    </style>
@endpush

@section('content')
    <section class="section py-4">
        <div class="container">
            <div class="col-12 col-md-9">
                {{-- Breadcrumb --}}
                <x-frontend.breadcrumb :items="[
                    ['label' => 'Bài viết', 'url' => route('frontend.post.postByCate', $post->category->slug)],
                    ['label' => $post->title]
                ]" />

                {{-- Tiêu đề + ảnh đại diện --}}
                <h1 class="mb-3">{{ $post->title }}</h1>

                @if($post->image)
                <div class="mb-4">
                    <img src="{{ asset($post->image) }}" class="img-fluid w-100" alt="{{ $post->title }}">
                </div>
                @endif

                {{-- Mục lục tự động (TOC) --}}
                <div class="toc-container" id="toc-wrapper">
                    <h3>📚 Nội dung</h3>
                    <div id="table-of-contents"></div>
                </div>

                {{-- Nội dung bài viết --}}
                <div class="post-content">
                    {!! $post->content !!}
                </div>

                {{-- Bài viết liên quan --}}
                @if($relatedPosts->count())
                <div class="mt-5">
                    <h3>Bài viết liên quan</h3>
                    <div class="swiper related-post-swiper">
                        <div class="swiper-wrapper">
                            @foreach($relatedPosts as $related)
                            <div class="swiper-slide">
                                <a href="{{ route('frontend.post.detail', $related->slug) }}" class="d-block">
                                    <img src="{{ asset($related->image ?? 'images/setting/no-image.png') }}" class="img-fluid mb-2" alt="{{ $related->title }}">
                                    <h6>{{ $related->title }}</h6>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                @endif
            </div>
            <div class="d-none d-md-block col-md-3">
                <div class="endow">
                    <h3 class="endow-title">Nhận ưu đãi tháng /</h3>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    {{-- TOC script --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const content = document.querySelector('.post-content');
        const tocContainer = document.getElementById('table-of-contents');
        if (!content || !tocContainer) return;

        const headers = content.querySelectorAll('h2, h3');
        if (headers.length === 0) return;

        let toc = '<ul>';
        let lastLevel = 2;

        headers.forEach((header, index) => {
            const id = 'heading-' + index;
            header.id = id;

            const level = parseInt(header.tagName.substr(1));
            const text = header.textContent;

            if (level > lastLevel) toc += '<ul>';
            else if (level < lastLevel) toc += '</ul>';

            toc += `<li><a href="#${id}">${text}</a></li>`;
            lastLevel = level;
        });

        toc += '</ul>';
        tocContainer.innerHTML = toc;
    });
    </script>
    <script>
    new Swiper(".related-post-swiper", {
        slidesPerView: 2,
        spaceBetween: 20,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 4 },
        },
    });
    </script>
@endpush
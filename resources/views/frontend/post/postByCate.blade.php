@extends('layouts.master')
@section('title', 'Tin tức')

@push('css')
<link rel="stylesheet" href="{{asset('css/product.css')}}">
<style>
    .post-item_title{ }
    .post-item_title a{ font-size: 1.2rem; }
</style>
@endpush

@section('content')
<div class="post-wrapper">
    <div class="collection-banner">
        {{-- Banner: ưu tiên bannerImage() nếu có, fallback $category->banner, cuối cùng là ảnh mặc định --}}
        <img
            src="{{ optional($category->bannerImage())->url() ?: ($category->banner ? asset($category->banner) : asset($setting->banner)) }}"
            alt="Tin tức"
            loading="lazy">
    </div>

    <div class="container py-5">
        <div class="category-section mb-5">
            <h2 class="custom-section-title">
                <a href="{{ route('frontend.slug.handle', $category->slug) }}">{{ $category->name }}</a>
            </h2>

            <div class="postByCateList">
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="post-item">
                                <div class="post-item_image">
                                    <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                                        {{-- Ảnh bài viết: mainImage() -> bannerImage() -> $post->image -> no-image --}}
                                        <img
                                            src="{{ optional($post->mainImage())->url()
                                                ?? optional($post->bannerImage())->url()
                                                ?? ($post->image ? asset($post->image) : asset('images/no-image.png')) }}"
                                            alt="{{ $post->title }}"
                                            loading="lazy">
                                    </a>
                                </div>
                                <div class="post-item_info">
                                    <h3 class="post-item_title">
                                        <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    <div class="info_description">
                                        {{ Str::limit($post->description, 100, '...') }}
                                    </div>
                                    <a class="read-more-link" href="{{ route('frontend.slug.handle', $post->slug) }}">
                                        Xem thêm <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

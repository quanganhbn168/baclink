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
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Tin tức</h2>
            <div class="des">
                {{ $post->description }}
            </div>
        </div>
        <div class="col-12 col-md-6">
            <img src="{{ optional($post->mainImage())->url() }}" alt="{{ $post->title }}">
        </div>
    </div>
</div>    
<div class="container">
    <div class="row mt-4">
        <div class="col-12 col-md-8 justify-content-center mx-auto">
            <h2 class="section-title mt-3">{{ $post->title }}</h2>
            <div class="ck-content">
                {!! $post->content !!}
            </div>
        </div>
    </div>
    <div class="related-post">
        <h3 class="text-center section-title">Bài viết liên quan</h3>
        <div class="row">
            @foreach($relatedPosts as $related)
            <div class="col-12 col-md-4 mb-3">
                @include('partials.frontend.post_item', ['post' => $related])
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
@push('js')
@endpush

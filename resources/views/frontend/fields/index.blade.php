@extends('layouts.master')
@section('title', $pageTitle)

@push('css')
<style>
    .field-banner {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/setting/hero_bg_new.jpg') }}');
        background-size: cover;
        background-position: center;
        padding: 100px 0;
        color: #fff;
    }
    .field-category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .field-category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .field-category-img {
        height: 200px;
        object-fit: cover;
    }
    .category-title {
        color: var(--blue);
        font-weight: 700;
        transition: color 0.3s;
    }
    .field-category-card:hover .category-title {
        color: var(--red);
    }
</style>
@endpush

@section('content')

{{-- Breadcrumbs --}}
<div class="bg-light py-3">
    <div class="container container-custom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Page Header --}}
<div class="field-banner text-center">
    <div class="container">
        <h1 class="display-4 font-weight-bold mb-3">{{ $pageTitle }}</h1>
        <p class="lead">BACLINK - Hệ sinh thái kết nối và phát triển doanh nghiệp bền vững</p>
    </div>
</div>

<section class="section py-5">
    <div class="container container-custom">
        <div class="row">
            @foreach($field_categories as $field_category)
            <div class="col-6 col-md-4 mb-4">
                <div class="card field-category-card h-100 shadow-sm">
                    <a href="{{ route('frontend.slug.handle', $field_category->slug) }}">
                        <img src="{{ optional($field_category->mainImage())->url() ?? asset('images/setting/no-image.png') }}" 
                             alt="{{ $field_category->name }}" 
                             class="card-img-top field-category-img">
                    </a>
                    <div class="card-body text-center">
                        <h5 class="card-title mb-0">
                            <a href="{{ route('frontend.slug.handle', $field_category->slug) }}" class="category-title text-decoration-none">
                                {{ $field_category->name }}
                            </a>
                        </h5>
                        @if($field_category->fields->count() > 0)
                            <p class="text-muted small mt-2 mb-0">{{ $field_category->fields->count() }} lĩnh vực</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

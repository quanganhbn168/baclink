@extends('layouts.master')
@section('title', 'Dự án')

@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<div class="banner">
    <img src="{{asset("images/setting/cover01.jpg")}}" alt="Dự án của Cnet POS">
</div>
<section class="section section-featured-project">
    <div class="container">
        <h2 class="custom-section-title">
            DỰ án tiêu biểu
        </h2>

        {{-- Bắt đầu khối dự án tiêu biểu --}}
        <div class="featured-project-card-wrapper">
            <a href="{{ route('frontend.slug.handle', $projectFeature->slug) }}" class="featured-project">
                <div class="row g-0 h-100">
                    {{-- Cột hình ảnh bên trái --}}
                    <div class="col-lg-7">
                        <div class="featured-project__image">
                            <img src="{{ optional($projectFeature->mainImage())->url() }}" alt="{{ $projectFeature->name }}">
                        </div>
                    </div>

                    {{-- Cột thông tin bên phải --}}
                    <div class="col-lg-5">
                        <div class="featured-project__info">
                            <div class="featured-project__item">
                                <span class="featured-project__label">Tên dự án:</span>
                                <h3 class="featured-project__value project-name">{{ $projectFeature->name }}</h3>
                            </div>
                            <div class="featured-project__item">
                                <span class="featured-project__label">Chủ đầu tư:</span>
                                <p class="featured-project__value">{{ $projectFeature->investor }}</p>
                            </div>
                            <div class="featured-project__item">
                                <span class="featured-project__label">Địa chỉ:</span>
                                <p class="featured-project__value">{{ $projectFeature->address }}</p>
                            </div>
                            <div class="featured-project__item">
                                <span class="featured-project__label">Năm thực hiện:</span>
                                <p class="featured-project__value">{{ $projectFeature->year }}</p>
                            </div>
                            <div class="featured-project__item">
                                <span class="featured-project__label">Giá trị gói thầu:</span>
                                <p class="featured-project__value">{{ number_format($projectFeature->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Kết thúc khối dự án tiêu biểu --}}

    </div>
</section>
<section class="section section-other-projects">
    <div class="container">
        <h2 class="custom-section-title">
            Những dự án tiêu biểu khác
        </h2>
        <div class="row">
            @foreach($projects as $project)
            <div class="col-12 col-md-6 mb-3">
                <x-reusable-card :item="$project"/>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
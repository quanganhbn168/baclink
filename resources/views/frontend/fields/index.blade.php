@extends('layouts.master')
@section('title', $pageTitle)

@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')

{{-- Phần Banner --}}
<div class="banner">
    @isset($category)
        <img src="{{ optional($category->bannerImage()->url() ?? '') }}" alt="{{ $category->name }}">
    @else
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img src="{{ asset('images/setting/contractors-bg-1.png') }}" alt="Lĩnh vực hoạt động">
                </div>
                <div class="col-md-7">
                    <h3 class="">
                        Cnet Group Là đơn vị thi công uy tín với đội ngũ 50+ nhân sự có chuyên môn cao.
                    </h3>
                    <div>
                        tổng thầu Tư vấn, thiết kế, thi công, bảo trì bảo dưỡng về Xây dựng, Cơ điện và Nội thất cho các công trình dân dụng, nhà máy, khách sạn, chung cư cao tầng tại Bắc Ninh, Bắc Giang và các tỉnh lân cận. Với hơn 10 năm kinh nghiệm cùng các đội ngũ kỹ sư lành nghề, phương trâm làm việc lấy uy tín, trách nhiệm, coi trọng khách hàng là mục tiêu lớn nhất mà Tân Tiến luôn hướng tới. Tầm nhìn của Tân Tiến Group sẽ trở thành nhà thầu cung cấp các dịch vụ về Xây dựng, Cơ điện và Nội thất tốt nhất miền Bắc. Quý đối tác có nhu cầu hãy liên hệ với Tân Tiến để nhận được những công trình có chất lượng tốt nhất.
                    </div>
                    <a href="tel:{{ $setting->phone }}" class="btn btn-primary rounded-pill btn-crossover">
                        <span class="btn-crossover-text">Gọi ngay</span>
                        <span class="btn-crossover-icon">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    @endisset
</div>

{{-- Phần Lĩnh vực hoạt động --}}
<section class="section section-field">
    @isset($category)
        <h2 class="section-title"><a href="{{ route('frontend.slug.handle', $category->slug) }}">{{ $category->name }}</a></h2>
    @else
        <h2 class="section-title"><a href="{{ route('frontend.fields.index') }}">Lĩnh vực hoạt động</a></h2>
    @endisset

    <div class="container">
        <div class="row">
            @foreach($field_categories as $field_category)
            <div class="col-6 col-md-4">
                <div class="field-category-item">
                    <div class="field-category-item__image">
                        <a href="{{ route('frontend.slug.handle', $field_category->slug) }}">
                            <img src="{{ asset($field_category->image) }}" alt="{{ $field_category->name }}">
                        </a>
                    </div>
                    <div class="field-category-item__name">
                        <a href="{{ route('frontend.slug.handle', $field_category->slug) }}">
                            {{ $field_category->name }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
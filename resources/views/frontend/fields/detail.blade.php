@extends('layouts.master')
@section('title', $pageTitle)

@push('css')
    {{-- CSS cho Breadcrumb và icon Font Awesome --}}
    <style>
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            list-style: none; /* Bỏ dấu chấm đầu dòng của ol */
        }
        .breadcrumb-item {
            display: inline;
        }
        .breadcrumb-item a {
            text-decoration: none;
            color: #007bff;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            content: "\f105";
            padding: 0 0.5rem;
            color: #6c757d;
        }

        /* --- CSS CHO GIAO DIỆN 2 CỘT STICKY --- */
        /* Chỉ áp dụng cho màn hình desktop (rộng hơn 992px) */
        @media (min-width: 992px) {
            .sidebar-sticky {
                position: sticky;
                top: 120px; /* Khoảng cách từ mép trên khi scroll */
                height: calc(100vh - 20px); /* Giới hạn chiều cao để có thể scroll nội bộ nếu mục lục quá dài */
                overflow-y: auto; /* Thêm thanh cuộn cho mục lục nếu nó dài hơn chiều cao màn hình */
            }
        }
    </style>
@endpush

@section('content')
<div class="container py-4">
    
    {{-- PHẦN BREADCRUMB (giữ nguyên) --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item">
                    <a href="{{ route('frontend.slug.handle', $breadcrumb->slug) }}">{{ $breadcrumb->name }}</a>
                </li>
            @endforeach
            <li class="breadcrumb-item active" aria-current="page">{{ $field->name }}</li>
        </ol>
    </nav>
    <hr class="d-lg-none"> {{-- Chỉ hiển thị đường kẻ ngang trên mobile --}}


    <div class="row">
        {{-- ====================================================== --}}
        {{-- CỘT BÊN TRÁI (SIDEBAR - MỤC LỤC CHO DESKTOP) --}}
        {{-- ====================================================== --}}
        <div class="col-lg-4 d-none d-lg-block">
            <div class="sidebar-sticky">
                {{-- Dùng component Mục lục đã tạo --}}
                <x-table-of-contents :content="$field->content" />
            </div>
        </div>

        {{-- ====================================================== --}}
        {{-- CỘT BÊN PHẢI (NỘI DUNG CHÍNH) --}}
        {{-- ====================================================== --}}
        <div class="col-lg-8">
            <article class="post-detail">
                {{-- TIÊU ĐỀ BÀI VIẾT --}}
                <h1>{{ $field->name }}</h1>

                {{-- THÔNG TIN BÀI VIẾT (Ví dụ: ngày đăng, tác giả...) --}}
                <p class="text-muted">
                    <i class="fa-solid fa-calendar"></i> Cập nhật lần cuối: {{ $field->updated_at->format('d/m/Y') }}
                </p>
                
                {{-- MỤC LỤC CHO GIAO DIỆN MOBILE --}}
                {{-- Component này sẽ tự động ẩn/hiện theo code chúng ta đã làm ở bước trước --}}
                <div class="d-lg-none mb-4">
                    <x-table-of-contents :content="$field->content" />
                </div>

                {{-- NỘI DUNG CHI TIẾT --}}
                <div class="post-content">
                    {{-- Dùng helper để thêm ID vào các thẻ h2 và render HTML --}}
                    {!! \App\Helpers\ContentHelper::addIdsToHeadings($field->content) !!}
                </div>

            </article>
        </div>
    </div>
</div>
@endsection
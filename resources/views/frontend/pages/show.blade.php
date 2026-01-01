@extends('layouts.master')

@section('title', $page->title)

@section('content')
<div class="container-custom py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-3">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="font-weight-bold mb-4 text-uppercase text-center border-bottom pb-3">
                        {{ $page->title }}
                    </h1>
                    
                    {{-- Áp dụng CSS chuẩn cho nội dung từ CKEditor --}}
                    <div class="ck-content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
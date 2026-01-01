@extends('layouts.master')
@section('title', 'Giới thiệu')
@section('meta_image',$setting->share_image)
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-light px-3 py-2">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i> Trang chủ</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $intro->name }}</li>
    </ol>
</nav>
<section class="section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="section-title text-uppercase">Giới thiệu chung về {{$setting->name}}</h1>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-12 col-md-9">
                    {!!$intro->content!!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.master')
@section('title', $currentCategory? $currentCategory->name : 'Sản phẩm')
@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
<style>
.category-card {
    border-radius: 14px;
    overflow: hidden;
}
.category-card__header {
    background: #1e2fae;
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
}
.list-group-item {
    background: #eef2f7;
    border: 0;
}
.category-link {
    color: #111;
    display: block;
}
.category-link.active {
    font-weight: 700;
}
.category-link:hover {
    text-decoration: none;
}
</style>
@endpush
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-light px-3 py-2">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i> Trang chủ</a>
        </li>
        <li class="breadcrumb-item {{ $currentCategory ? '' : 'active' }}" aria-current="page">
            @if($currentCategory)
                <a href="{{ route('products.index') }}">Sản phẩm</a>
            @else
                Sản phẩm
            @endif
        </li>
        @if($currentCategory)
            <li class="breadcrumb-item active" aria-current="page">{{ $currentCategory->name }}</li>
        @endif
    </ol>
</nav>
<div class="container-fluid">
    <h2 class="section-title mb-3">
        {{ $currentCategory ? $currentCategory->name : 'Sản phẩm' }}
    </h2>
    <div class="row">
        {{-- Sidebar --}}
        <div class="d-none d-lg-block col-lg-3">
            <aside>
                <div class="card category-card">
                    <div class="card-header category-card__header">Danh mục sản phẩm</div>
                    <ul class="list-group list-group-flush">
                        {{-- Danh mục gốc --}}
                        @foreach($categories as $cat)
                            <li class="list-group-item p-0">
                                <a class="d-block px-3 py-2 category-link {{ $currentCategory && $currentCategory->id === $cat->id ? 'active' : '' }}"
                                   href="{{ route('frontend.slug.handle', $cat->slug) }}">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
        {{-- Main --}}
        <div class="col-12 col-lg-9">
            <div class="product-wrapper">
                {{-- Filter bar --}}
                <form class="d-flex align-items-center justify-content-between mb-3" method="GET">
                    @if($currentCategory)
                        <input type="hidden" name="category_slug" value="{{ $currentCategory->slug }}">
                    @endif
                    <p class="mb-0 text-muted">
                        {{ number_format($products->total()) }} sản phẩm
                    </p>
                    <div class="form-inline">
                        <label class="mr-2">Sắp xếp:</label>
                        <select class="form-control" name="sort" onchange="this.form.submit()">
                            <option value="new"        {{ $sort==='new'        ? 'selected':'' }}>Mới nhất</option>
                            <option value="old"        {{ $sort==='old'        ? 'selected':'' }}>Cũ nhất</option>
                            <option value="name_asc"   {{ $sort==='name_asc'   ? 'selected':'' }}>Tên A-Z</option>
                            <option value="price_asc"  {{ $sort==='price_asc'  ? 'selected':'' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ $sort==='price_desc' ? 'selected':'' }}>Giá giảm dần</option>
                        </select>
                    </div>
                </form>
                {{-- Grid --}}
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-6 col-md-4 mb-4">
                            @include("partials.frontend.product_item",["product"=>$product])
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light mb-0">Chưa có sản phẩm phù hợp.</div>
                        </div>
                    @endforelse
                </div>
                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
{{-- không cần JS thêm cho trang này --}}
@endpush

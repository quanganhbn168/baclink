@extends('layouts.master')


@section('title', $category->name)

@push('css')
@vite(['resources/css/custom/product.css'])
@endpush

@section("content")
<div id="breadcrumb" class="bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light">
                
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div id="wrapper">
    <div class="container">
        
        <h2 class="text-uppercase">{{ $category->name }}</h2>
        <div class="banner mb-4">
            
            <img src="{{ asset($category->banner ?? 'https://placehold.co/1920x300/005a9c/FFFFFF?text=Collection+Banner') }}" alt="{{ $category->name }}">
        </div>

        <div class="row">
            
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="aside-filter">
                    <div class="aside-title">
                        <h3 class="title-head"><i class="fa-solid fa-filter"></i> Bộ lọc sản phẩm</h3>
                    </div>
                    <div class="aside-content">
                        
                        <div class="aside-item">
                            <div class="aside-item_title">Loại sản phẩm</div>
                            <ul class="aside-item_content">
                                @foreach($categories as $catFilter)
                                <li>
                                    <div class="form-check">
                                        
                                        <input class="form-check-input" type="checkbox" value="{{ $catFilter->slug }}" id="cat_{{ $catFilter->id }}" {{ $catFilter->id == $category->id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_{{ $catFilter->id }}">{{ $catFilter->name }}</label>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        
                        <div class="aside-item">
                            <div class="aside-item_title">Khoảng giá</div>
                            <ul class="aside-item_content">
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="duoi-1tr" id="price1">
                                        <label class="form-check-label" for="price1">Dưới 1,000,000đ</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1tr-5tr" id="price2">
                                        <label class="form-check-label" for="price2">Từ 1,000,000đ - 5,000,000đ</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            
            <div class="col-lg-9 col-md-12">
                <div class="sort-products d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-outline-dark d-lg-none">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                    <div class="sort-by ms-auto">
                        <label for="sort-options" class="form-label me-2">Sắp xếp theo:</label>
                        <select class="form-select d-inline-block w-auto" id="sort-options">
                            <option value="latest">Mới nhất</option>
                            <option value="price-asc">Giá: Tăng dần</option>
                            <option value="price-desc">Giá: Giảm dần</option>
                            <option value="name-asc">Tên: A-Z</option>
                        </select>
                    </div>
                </div>

                
                <div class="product-widget_wrapper">
                        <div class="row">  
                            @forelse ($products as $product)
                            <div class="col-lg-4 col-md-6 col-6">
                                
                                @include('partials.frontend.product_item',['product'=>$product])
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <p>Không tìm thấy sản phẩm nào trong danh mục này.</p>
                                    <a href="{{ route('home') }}" class="btn btn-primary">Quay về Trang chủ</a>
                                </div>
                            </div>
                            @endforelse
                        </div> 
                    </div>

                
                <nav class="pagination-wrapper d-flex justify-content-center mt-4">
                    
                    {{ $products->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    
    
    
    document.getElementById('sort-options').addEventListener('change', function() {
        
        const currentUrl = new URL(window.location.href);
        
        currentUrl.searchParams.set('sort', this.value);
        
        window.location.href = currentUrl.toString();
    });
</script>
@endpush

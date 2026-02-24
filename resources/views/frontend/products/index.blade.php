@push('css')
@vite(['resources/css/custom/product.css'])
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
<div class="container-fluid mt-3">
    @php
        $breadcrumbs = [];
        if($currentCategory) {
            $breadcrumbs[] = ['label' => __('Sản phẩm'), 'url' => route('products.index')];
            $breadcrumbs[] = ['label' => $currentCategory->getTranslation('name'), 'url' => ''];
        } else {
            $breadcrumbs[] = ['label' => __('Sản phẩm'), 'url' => ''];
        }
    @endphp
    <x-frontend.breadcrumb :items="$breadcrumbs" />
</div>
<div class="container-fluid">
    <h2 class="section-title mb-3">
        {{ $currentCategory ? $currentCategory->name : __('Sản phẩm') }}
    </h2>
    <div class="row">
        {{-- Sidebar --}}
        <div class="d-none d-lg-block col-lg-3">
            <aside>
                <div class="card category-card">
                    <div class="card-header category-card__header">{{ __('Danh mục sản phẩm') }}</div>
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
                        {{ number_format($products->total()) }} {{ __('sản phẩm') }}
                    </p>
                    <div class="form-inline">
                        <label class="mr-2">{{ __('Sắp xếp') }}:</label>
                        <select class="form-control" name="sort" onchange="this.form.submit()">
                            <option value="new"        {{ $sort==='new'        ? 'selected':'' }}>{{ __('Mới nhất') }}</option>
                            <option value="old"        {{ $sort==='old'        ? 'selected':'' }}>{{ __('Cũ nhất') }}</option>
                            <option value="name_asc"   {{ $sort==='name_asc'   ? 'selected':'' }}>{{ __('Tên A-Z') }}</option>
                            <option value="price_asc"  {{ $sort==='price_asc'  ? 'selected':'' }}>{{ __('Giá tăng dần') }}</option>
                            <option value="price_desc" {{ $sort==='price_desc' ? 'selected':'' }}>{{ __('Giá giảm dần') }}</option>
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
                            <div class="alert alert-light mb-0">{{ __('Chưa có sản phẩm phù hợp.') }}</div>
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

@extends('layouts.admin')

@section('title', 'Sản phẩm')
@section('content_header_title', 'Quản lý sản phẩm')

@push('css')
<style>
    .table-products .thumb{width:56px;height:56px;object-fit:cover;border-radius:6px}
    .table-products .td-name{max-width:320px}
    .table-products .price-old{text-decoration:line-through;opacity:.7;margin-right:.25rem}
    .table-products .price-new{font-weight:700}
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc</h3>
            <div class="card-tools">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Thêm sản phẩm
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row">
                <div class="col-md-4">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên/Slug/SKU..." />
                </div>
                <div class="col-md-3">
                    <x-form.select
                        name="category_id"
                        label="Danh mục"
                        :options="$filterCategories ?? []"
                        :selected="request('category_id')"
                        placeholder="-- Tất cả danh mục --" />
                </div>
                <div class="col-md-2">
                    <x-form.select
                        name="status"
                        label="Trạng thái"
                        :options="['1' => 'Hiển thị', '0' => 'Ẩn']"
                        :selected="request('status')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-2">
                    <x-form.select
                        name="is_home"
                        label="Trang chủ"
                        :options="['1' => 'Có', '0' => 'Không']"
                        :selected="request('is_home')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-1">
                    <label class="d-block">&nbsp;</label>
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách sản phẩm</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('admin.products.index') }}">
                    @foreach(request()->except('per_page') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                        @foreach([10,20,30,50,100] as $pp)
                            <option value="{{ $pp }}" {{ (int)request('per_page', 20) === $pp ? 'selected' : '' }}>{{ $pp }}/trang</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0 table-products">
                <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:72px">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th style="width:180px">Giá</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:110px">Trang chủ</th>
                    <th style="width:150px">Tạo lúc</th>
                    <th style="width:120px" class="text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($products as $index => $product)
                    @php
                        $row = ($products->currentPage() - 1) * $products->perPage() + $index + 1;

                        // ===== HÌNH ẢNH: ưu tiên hệ Media (thumbnail) -> main -> fallback ảnh cũ -> ảnh mặc định
                        $imgModel = method_exists($product, 'mainImage') ? $product->mainImage() : null;
                        $thumbUrl = $imgModel ? ($imgModel->url('thumbnail') ?: $imgModel->url()) : null;
                        $thumbUrl = $thumbUrl ?: ($product->image ? asset($product->image) : asset('images/setting/no-image.png'));

                        // ===== GIÁ
                        $price = (float) ($product->price ?? 0);
                        $priceDiscount = (float) ($product->price_discount ?? 0);
                        $hasDiscount = $price > 0 && $priceDiscount > 0 && $priceDiscount < $price;
                        $displayPrice = $hasDiscount ? $priceDiscount : $price;
                    @endphp
                    <tr>
                        <td>{{ $row }}</td>
                        <td>
                            <img class="thumb" src="{{ $thumbUrl }}" alt="{{ $product->name }}">
                        </td>
                        <td class="td-name">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="font-weight-bold">{{ $product->name }}</a>
                            <div class="small text-muted">Slug: {{ $product->slug }}</div>
                            @if(!empty($product->code))
                                <div class="small text-muted">Mã: {{ $product->code }}</div>
                            @endif
                        </td>
                        <td>{{ optional($product->category)->name ?? '—' }}</td>
                        <td>
                            @if ($hasDiscount)
                                <span class="price-old">{{ number_format($price, 0, ',', '.') }}đ</span>
                                <span class="price-new text-danger">{{ number_format($displayPrice, 0, ',', '.') }}đ</span>
                            @else
                                <span class="price-new">
                                    {{ $displayPrice > 0 ? number_format($displayPrice, 0, ',', '.') . 'đ' : 'Liên hệ' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <x-boolean-toggle model="Product" :record="$product" field="status" />
                        </td>
                        <td>
                            <x-boolean-toggle model="Product" :record="$product" field="is_home" />
                        </td>
                        <td>{{ optional($product->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirmDelete(this);">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">Chưa có dữ liệu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($products instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer clearfix">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('js')
<script>
    // dùng SweetAlert global nếu có, fallback confirm()
    function confirmDelete(formEl){
        if(!window.Swal){ return confirm('Xoá mục này?'); }
        event.preventDefault();
        Swal.fire({
            title:'Bạn chắc chắn?',
            text:'Hành động này không thể hoàn tác.',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Vâng, xoá!',
            cancelButtonText:'Huỷ'
        }).then(r=>{ if(r.isConfirmed) formEl.submit(); });
        return false;
    }
</script>
@endpush

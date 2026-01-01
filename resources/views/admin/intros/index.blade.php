@extends('layouts.admin')

@section('title', 'Giới thiệu (Intro)')
@section('content_header_title', 'Quản lý Intro')

@push('css')
<style>
    .table-intros .thumb{width:56px;height:56px;object-fit:cover;border-radius:6px}
    .table-intros .td-title{max-width:420px}
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
                <a href="{{ route('admin.intros.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Thêm Intro
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.intros.index') }}" class="row">
                <div class="col-md-5">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tiêu đề/Slug..." />
                </div>
                <div class="col-md-3">
                    <x-form.select
                        name="status"
                        label="Trạng thái"
                        :options="['1' => 'Hiển thị', '0' => 'Ẩn']"
                        :selected="request('status')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Intro</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('admin.intros.index') }}">
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
            <table class="table table-hover table-striped mb-0 table-intros">
                <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:72px">Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:160px">Tạo lúc</th>
                    <th style="width:120px" class="text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($intros as $index => $intro)
                    @php
                        $row = ($intros->currentPage() - 1) * $intros->perPage() + $index + 1;
                        $img = method_exists($intro,'mainImage') ? $intro->mainImage() : null;
                        $thumbUrl = $img ? ($img->url('thumbnail') ?: $img->url()) : asset('images/setting/no-image.png');
                    @endphp
                    <tr>
                        <td>{{ $row }}</td>
                        <td><img class="thumb" src="{{ $thumbUrl }}" alt="{{ $intro->title }}"></td>
                        <td class="td-title">
                            <a href="{{ route('admin.intros.edit', $intro->id) }}" class="font-weight-bold">{{ $intro->title }}</a>
                        </td>
                        <td class="small text-muted">{{ $intro->slug }}</td>
                        <td>
                            <x-boolean-toggle model="Intro" :record="$intro" field="status" />
                        </td>
                        <td>{{ optional($intro->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.intros.edit', $intro->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.intros.destroy', $intro->id) }}" method="POST" class="d-inline-block" onsubmit="return confirmDelete(this);">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Chưa có Intro nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($intros instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer clearfix">
                {{ $intros->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('js')
<script>
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

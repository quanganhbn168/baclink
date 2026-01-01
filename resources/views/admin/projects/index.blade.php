@extends('layouts.admin')

@section('title', 'Dự án')
@section('content_header_title', 'Quản lý dự án')

@push('css')
<style>
    .table-projects .thumb{width:56px;height:56px;object-fit:cover;border-radius:6px}
    .table-projects .td-name{max-width:360px}
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
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Thêm dự án
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="row">
                <div class="col-md-4">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên/Slug..." />
                </div>
                <div class="col-md-3">
                    <x-form.select
                        name="project_category_id"
                        label="Danh mục"
                        :options="$filterCategories ?? []"
                        :selected="request('project_category_id')"
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
            <h3 class="card-title">Danh sách dự án</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('admin.projects.index') }}">
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
            <table class="table table-hover table-striped mb-0 table-projects">
                <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:72px">Ảnh</th>
                    <th>Tên dự án</th>
                    <th>Danh mục</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:110px">Trang chủ</th>
                    <th style="width:150px">Tạo lúc</th>
                    <th style="width:120px" class="text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($projects as $index => $project)
                    @php
                        $row = ($projects->currentPage() - 1) * $projects->perPage() + $index + 1;
                        $img = method_exists($project,'mainImage') ? $project->mainImage() : null;
                        $thumbUrl = $img ? ($img->url('thumbnail') ?: $img->url()) : null;
                        $thumbUrl ??= $project->image ? asset($project->image) : asset('images/setting/no-image.png');
                    @endphp
                    <tr>
                        <td>{{ $row }}</td>
                        <td><img class="thumb" src="{{ $thumbUrl }}" alt="{{ $project->name }}"></td>
                        <td class="td-name">
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="font-weight-bold">{{ $project->name }}</a>
                            <div class="small text-muted">Slug: {{ $project->slug }}</div>
                        </td>
                        <td>{{ optional($project->category)->name ?? '—' }}</td>
                        <td>
                            <x-boolean-toggle model="Project" :record="$project" field="status" />
                        </td>
                        <td>
                            <x-boolean-toggle model="Project" :record="$project" field="is_home" />
                        </td>
                        <td>{{ optional($project->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="d-inline-block" onsubmit="return confirmDelete(this);">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">Chưa có dữ án nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer clearfix">
                {{ $projects->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('js')
<script>
    function confirmDelete(formEl){
        if(!window.Swal){ return confirm('Xoá mục này?'); }
        event.preventDefault();
        Swal.fire({title:'Bạn chắc chắn?',text:'Hành động này không thể hoàn tác.',icon:'warning',showCancelButton:true,confirmButtonText:'Vâng, xoá!',cancelButtonText:'Huỷ'})
            .then(r=>{ if(r.isConfirmed) formEl.submit(); });
        return false;
    }
</script>
@endpush

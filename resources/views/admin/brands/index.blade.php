@extends('layouts.admin')

@section('title', 'Danh sách thương hiệu')
@section('content_header', 'Danh sách thương hiệu')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm thương hiệu</a>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th>Ảnh</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th width="120">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->slug }}</td>
                    <td>
                        @if($item->image)
                            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" width="50">
                        @endif
                    </td>
                    <td>
                        <x-boolean-toggle model="Brand" :record="$item" field="status" />
                    </td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.brands.edit', $item) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.brands.destroy', $item) }}" method="POST" class="d-inline form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger delete">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $brands->links() }}
    </div>
</div>
@endsection

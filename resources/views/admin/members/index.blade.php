@extends('layouts.admin')

@section('title', 'Danh sách Hội viên')

@section('content_header')
    <h1>Danh sách Hội viên</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Hội viên đã đăng ký</h3>
            <div class="card-tools d-flex align-items-center">
                <x-admin.bulk-action-bar model="member" />
                <form action="{{ route('admin.members.index') }}" method="GET" class="ml-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="keyword" class="form-control float-right" placeholder="Tìm kiếm..." value="{{ request('keyword') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th class="text-center" width="40">
                            <input type="checkbox" id="checkAll" class="custom-checkbox">
                        </th>
                        <th>Tên Công ty</th>
                        <th>Đại diện</th>
                        <th>Số điện thoại</th>
                        <th>Ngành nghề</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="custom-checkbox check-item" value="{{ $member->id }}">
                            </td>
                            <td>{{ Str::limit($member->company_name, 30) }}</td>
                            <td>{{ $member->representative_name }}</td>
                            <td>{{ $member->phone }}</td>
                            <td>{{ $member->business_sector }}</td>
                            <td>{{ $member->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hội viên này? Tài khoản đăng nhập cũng sẽ bị xóa!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có hội viên nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $members->links() }}
        </div>
    </div>
@stop

@extends('layouts.admin')

@section('title', 'Quản lý Hội viên')
@section('content_header_title', 'Danh sách Hội viên')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách Hội viên</h3>
        <div class="card-tools">
            <x-admin.bulk-action-bar model="user" />
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Thêm hội viên mới
            </a>
        </div>
    </div>
    <div class="card-body">
        {{-- Bộ lọc --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.members.index') }}" class="form-inline">
                    <div class="input-group input-group-sm" style="width: 350px;">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm kiếm tên, email, công ty...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    @if(request('keyword'))
                        <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-link text-muted ml-2">Xóa lọc</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center" width="40">
                            <input type="checkbox" id="checkAll" class="custom-checkbox">
                        </th>
                        <th width="80">Avatar</th>
                        <th>Thông tin Hội viên</th>
                        <th>Doanh nghiệp</th>
                        <th>Chức vụ / Ngành nghề</th>
                        <th style="width: 120px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $key => $member)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="custom-checkbox check-item" value="{{ $member->id }}">
                            </td>
                            <td>
                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" 
                                     class="img-circle border"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $member->name }}</div>
                                <div class="small text-muted"><i class="fas fa-envelope mr-1"></i> {{ $member->email }}</div>
                                <div class="small text-muted"><i class="fas fa-phone mr-1"></i> {{ $member->phone ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-danger">{{ $member->dealerProfile->company_name ?? 'N/A' }}</div>
                                @if($member->dealerProfile->website)
                                    <div class="small"><a href="{{ $member->dealerProfile->website }}" target="_blank"><i class="fas fa-globe mr-1"></i> Website</a></div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $member->dealerProfile->position ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $member->dealerProfile->business_sector ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Anh có chắc muốn xóa hội viên này không?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-users-slash fa-3x mb-3"></i><br>
                                Không tìm thấy hội viên nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="card-footer clearfix bg-white border-top-0">
                <div class="float-right">
                    {{ $members->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

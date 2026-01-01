@extends('layouts.admin')

@section('title', 'Danh sách liên hệ')
@section('content_header', 'Liên hệ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách liên hệ</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Họ và tên</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Nội dung</th>
                    <th>Ngày gửi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->address }}</td>
                        <td>{{ $contact->phone }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->message ?? $contact->content }}</td>
                        <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            {{-- Xem chi tiết nếu cần --}}
                            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline-block" 
                                  onsubmit="return confirm('Bạn có chắc muốn xoá liên hệ này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Chưa có liên hệ nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Nếu dùng phân trang --}}
    <div class="card-footer">
        {{ $contacts->links() }}
    </div>
</div>
@endsection

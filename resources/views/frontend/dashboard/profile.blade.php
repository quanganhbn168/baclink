@extends('frontend.dashboard.layout')

@section('title', 'Thông tin cá nhân')

@section('dashboard_content')
    <h3 class="mb-4">Thông tin cá nhân</h3>

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4 text-center">
                <img id="avatar-preview" src="{{ asset( Auth::user()->avatar ) }}" 
                     alt="Avatar" class="profile-avatar mb-3">
                <input type="file" name="avatar" id="avatar" class="form-control" onchange="previewAvatar(event)">
                @error('avatar') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                </div>
                 <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}">
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <hr>
                <h5 class="mt-4">Đổi mật khẩu</h5>
                <p class="text-muted"><small>Để trống nếu bạn không muốn thay đổi mật khẩu.</small></p>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="password" name="password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </form>
@endsection

@push('js')
<script>
    // Xem trước avatar khi người dùng chọn file mới
    function previewAvatar(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('avatar-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
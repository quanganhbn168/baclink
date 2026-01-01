@csrf
<div class="row">
    <div class="col-md-8">
        {{-- Row 1: Name and Email --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                {{-- EMAIL: Bỏ dấu * và thuộc tính required --}}
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Row 2: Phone and Address --}}
        <div class="row">
             <div class="col-md-6 mb-3">
                {{-- SỐ ĐIỆN THOẠI: Thêm dấu * và thuộc tính required --}}
                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address ?? '') }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        {{-- Các phần còn lại của form (Mật khẩu, Vai trò) giữ nguyên --}}
        {{-- ... --}}
        
        {{-- Row 3: Password --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="generate-password-btn" title="Tạo mật khẩu ngẫu nhiên">
                            <i class="fas fa-random"></i>
                        </button>
                        <span class="input-group-text" id="toggle-password-span" style="cursor: pointer;" title="Hiện/Ẩn mật khẩu">
                            <i class="fas fa-eye" id="toggle-password-icon"></i>
                        </span>
                    </div>
                </div>
                @if(isset($user))
                    <small class="form-text text-muted">Để trống nếu không muốn thay đổi mật khẩu.</small>
                @endif
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    <div class="input-group-append">
                         <span class="input-group-text" id="toggle-password-confirmation-span" style="cursor: pointer;" title="Hiện/Ẩn mật khẩu">
                            <i class="fas fa-eye" id="toggle-password-confirmation-icon"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        {{-- Roles Assignment --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Gán vai trò</label>
             @error('roles')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror
            <div class="p-3 border rounded" style="max-height: 300px; overflow-y: auto;">
                @if(isset($roles) && !$roles->isEmpty())
                    @foreach($roles as $roleName)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $roleName }}" id="role-{{ $roleName }}"
                                   @if(is_array(old('roles', $userRoles ?? [])) && in_array($roleName, old('roles', $userRoles ?? []))) checked @endif>
                            <label class="form-check-label" for="role-{{ $roleName }}">
                                {{ $roleName }}
                            </label>
                        </div>
                    @endforeach
                @else
                     <p class="text-muted">Không có vai trò nào (thuộc guard web) để gán.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Cập nhật' : 'Tạo mới' }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy bỏ</a>
</div>

{{-- SCRIPT XỬ LÝ MẬT KHẨU (Đã cập nhật ID cho nút mới) --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const generateBtn = document.getElementById('generate-password-btn');

    // Sửa lại ID của nút toggle cho khớp với cấu trúc mới
    const toggleSpan = document.getElementById('toggle-password-span');
    const toggleIcon = document.getElementById('toggle-password-icon');
    const toggleConfirmationSpan = document.getElementById('toggle-password-confirmation-span');
    const toggleConfirmationIcon = document.getElementById('toggle-password-confirmation-icon');

    // 1. Chức năng tạo mật khẩu ngẫu nhiên (Không đổi)
    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            const randomPassword = generateStrongPassword();
            passwordInput.value = randomPassword;
            passwordConfirmationInput.value = randomPassword;
        });
    }

    // 2. Chức năng hiện/ẩn mật khẩu (Sửa lại để lắng nghe sự kiện trên thẻ span)
    if (toggleSpan) {
        toggleSpan.addEventListener('click', function () {
            togglePasswordVisibility(passwordInput, toggleIcon);
        });
    }
    
    if (toggleConfirmationSpan) {
        toggleConfirmationSpan.addEventListener('click', function () {
            togglePasswordVisibility(passwordConfirmationInput, toggleConfirmationIcon);
        });
    }

    // Hàm helper để tạo mật khẩu mạnh (Không đổi)
    function generateStrongPassword(length = 12) {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
        let password = "";
        for (let i = 0, n = charset.length; i < length; ++i) {
            password += charset.charAt(Math.floor(Math.random() * n));
        }
        return password;
    }

    // Hàm helper để thay đổi trạng thái hiện/ẩn (Đổi class cho Font Awesome)
    function togglePasswordVisibility(inputField, iconElement) {
        if (inputField.type === "password") {
            inputField.type = "text";
            iconElement.classList.remove("fa-eye");
            iconElement.classList.add("fa-eye-slash");
        } else {
            inputField.type = "password";
            iconElement.classList.remove("fa-eye-slash");
            iconElement.classList.add("fa-eye");
        }
    }
});
</script>
@endpush
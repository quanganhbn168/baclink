@csrf

<div class="row">
    {{-- User Account Info --}}
    <div class="col-md-6 border-right">
        <h6 class="font-weight-bold mb-3 text-info"><i class="bi bi-person mr-1"></i> Thông tin tài khoản</h6>
        
        <div class="mb-3">
            <label class="form-label font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $member->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $member->email ?? '') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Số điện thoại</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $member->phone ?? '') }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Mật khẩu @if(!isset($member)) <span class="text-danger">*</span> @endif</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" @if(!isset($member)) required @endif>
            @if(isset($member))
                <small class="text-muted">Để trống nếu không muốn đổi mật khẩu.</small>
            @endif
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Nhập lại mật khẩu @if(!isset($member)) <span class="text-danger">*</span> @endif</label>
            <input type="password" class="form-control" name="password_confirmation" @if(!isset($member)) required @endif>
        </div>

        <div class="mb-3">
            <x-admin.form.media-input
                name="avatar_original_path"
                label="Ảnh đại diện (Avatar)"
                :multiple="false"
                :value="isset($member) ? $member->mainImage()?->original_path : null"
            />
        </div>
    </div>

    {{-- Dealer Profile Info --}}
    <div class="col-md-6">
        <h6 class="font-weight-bold mb-3 text-info"><i class="bi bi-building mr-1"></i> Thông tin doanh nghiệp</h6>
        
        <div class="mb-3">
            <x-admin.form.media-input
                name="logo_original_path"
                label="Logo công ty"
                :multiple="false"
                :value="isset($member) && $member->dealerProfile ? $member->dealerProfile->mainImage()?->original_path : null"
            />
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Tên công ty / Doanh nghiệp</label>
            <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name', $member->dealerProfile->company_name ?? '') }}">
            @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Mã số thuế</label>
            <input type="text" class="form-control @error('tax_id') is-invalid @enderror" name="tax_id" value="{{ old('tax_id', $member->dealerProfile->tax_id ?? '') }}">
            @error('tax_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Chức vụ</label>
            <input type="text" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position', $member->dealerProfile->position ?? '') }}" placeholder="Vd: Giám đốc, Quản lý...">
            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Lĩnh vực kinh doanh</label>
            <input type="text" class="form-control @error('business_sector') is-invalid @enderror" name="business_sector" value="{{ old('business_sector', $member->dealerProfile->business_sector ?? '') }}">
            @error('business_sector') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Địa chỉ</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', $member->dealerProfile->address ?? '') }}">
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Website (URL)</label>
            <input type="url" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website', $member->dealerProfile->website ?? '') }}" placeholder="https://example.com">
            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="mt-4 text-right">
    <hr>
    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary px-4">Hủy</a>
    <button type="submit" class="btn btn-primary px-5">{{ isset($member) ? 'Cập nhật' : 'Tạo mới' }}</button>
</div>

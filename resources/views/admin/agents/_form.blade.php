<div class="card-body">
    {{-- Hiển thị lỗi tổng quan nếu có --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- Cột Trái: Thông tin đăng nhập & Cá nhân --}}
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-shield"></i> Tài khoản & Cá nhân</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $agent->name ?? '') }}" placeholder="Nhập họ tên đại lý">
                    </div>

                    <div class="form-group">
                        <label>Email đăng nhập <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $agent->email ?? '') }}" placeholder="email@example.com">
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại (Login & LH) <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $agent->phone ?? '') }}" placeholder="0912345678">
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu @if(isset($agent)) <small class="text-muted">(Để trống nếu không đổi)</small> @else <span class="text-danger">*</span> @endif</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>

        {{-- Cột Phải: Thông tin Doanh nghiệp & Profile --}}
        <div class="col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building"></i> Hồ sơ Đại lý</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tên Công ty / Cửa hàng</label>
                        <input type="text" name="company_name" class="form-control" 
                               value="{{ old('company_name', $agent->dealerProfile->company_name ?? '') }}" placeholder="Công ty TNHH ABC...">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Mã số thuế</label>
                                <input type="text" name="tax_id" class="form-control" 
                                       value="{{ old('tax_id', $agent->dealerProfile->tax_id ?? '') }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Mức chiết khấu (%)</label>
                                <div class="input-group">
                                    <input type="number" name="discount_rate" class="form-control" min="0" max="100"
                                           value="{{ old('discount_rate', $agent->dealerProfile->discount_rate ?? 0) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ kinh doanh</label>
                        <input type="text" name="address" class="form-control" 
                               value="{{ old('address', $agent->dealerProfile->address ?? '') }}">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Số Zalo (Nếu khác)</label>
                                <input type="text" name="zalo_phone" class="form-control" 
                                       value="{{ old('zalo_phone', $agent->dealerProfile->zalo_phone ?? '') }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Facebook ID / Link</label>
                                <input type="text" name="facebook_id" class="form-control" 
                                       value="{{ old('facebook_id', $agent->dealerProfile->facebook_id ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú quản trị (Nội bộ)</label>
                        <textarea name="admin_note" class="form-control" rows="2">{{ old('admin_note', $agent->dealerProfile->admin_note ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-footer">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thông tin</button>
    <a href="{{ route('admin.agents.index') }}" class="btn btn-default float-right">Quay lại danh sách</a>
</div>
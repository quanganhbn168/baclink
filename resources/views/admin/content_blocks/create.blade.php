@extends('layouts.admin')
@section('title', 'Thêm Khối nội dung mới')

@section('content')
<form action="{{ route('admin.content-blocks.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Thông tin khối nội dung</h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Cột trái --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Chọn Khu vực hiển thị <span class="text-danger">*</span></label>
                        <select name="section" class="form-control">
                            @foreach(App\Enums\ContentSection::cases() as $section)
                                <option value="{{ $section->value }}" {{ old('section') == $section->value ? 'selected' : '' }}>
                                    {{ $section->label() }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Chọn đúng khu vực để hiển thị đúng chỗ trên website.</small>
                    </div>

                    <div class="form-group">
                        <label>Tiêu đề (Title)</label>
                        <input type="text" name="title" class="form-control" placeholder="VD: Năm kinh nghiệm, Giao hàng nhanh..." value="{{ old('title') }}">
                    </div>

                    <div class="form-group">
                        <label>Phụ đề / Số liệu (Subtitle)</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="VD: 5+, 100%, 24/7..." value="{{ old('subtitle') }}">
                    </div>
                </div>

                {{-- Cột phải --}}
                <div class="col-md-6">
                     <div class="form-group">
                        <label>Icon (FontAwesome Class)</label>
                        <div class="input-group">
                            <input type="text" name="icon" class="form-control" placeholder="VD: fas fa-users" value="{{ old('icon') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                            </div>
                        </div>
                        <small class="text-muted">Lấy mã icon tại FontAwesome.</small>
                    </div>

                    <div class="form-group">
                        <label>Hoặc Upload Ảnh mới</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Chọn file ảnh...</label>
                        </div>
                        
                        {{-- Khu vực hiển thị ảnh Preview --}}
                        <div class="mt-2">
                             <img id="preview-img" src="#" alt="Preview" 
                                  class="img-thumbnail mt-2" style="height: 100px; display: none;">
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label>Thứ tự hiển thị</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label>Mô tả ngắn (Description)</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Đường dẫn (URL - Tùy chọn)</label>
                        <input type="text" name="url" class="form-control" placeholder="VD: /chinh-sach-bao-mat" value="{{ old('url') }}">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" checked>
                            <label class="custom-control-label" for="isActive">Kích hoạt hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
            <a href="{{ route('admin.content-blocks.index') }}" class="btn btn-default">Quay lại</a>
        </div>
    </div>
</form>
@endsection

@push('js')
<script src="{{ asset('vendor/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        // Kích hoạt plugin bs-custom-file-input của AdminLTE (nếu có dùng)
        bsCustomFileInput.init();

        // Logic Preview ảnh & Hiển thị tên file thủ công (phòng hờ plugin không chạy)
        $('.custom-file-input').on('change', function () {
            // 1. Hiển thị tên file
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
            
            // 2. Preview ảnh
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview-img').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush

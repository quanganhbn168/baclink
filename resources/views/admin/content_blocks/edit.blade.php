@extends('layouts.admin')
@section('title', 'Cập nhật Khối nội dung')

@section('content')
<form action="{{ route('admin.content-blocks.update', $contentBlock) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- Quan trọng: Báo cho Laravel đây là request Update --}}
    
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title">Cập nhật nội dung #{{ $contentBlock->id }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Cột trái --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Chọn Khu vực hiển thị <span class="text-danger">*</span></label>
                        <select name="section" class="form-control">
                            @foreach(App\Enums\ContentSection::cases() as $section)
                                <option value="{{ $section->value }}" 
                                    {{ $contentBlock->section->value == $section->value ? 'selected' : '' }}>
                                    {{ $section->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tiêu đề (Title)</label>
                        <input type="text" name="title" class="form-control" 
                               value="{{ old('title', $contentBlock->title) }}">
                    </div>

                    <div class="form-group">
                        <label>Phụ đề / Số liệu (Subtitle)</label>
                        <input type="text" name="subtitle" class="form-control" 
                               value="{{ old('subtitle', $contentBlock->subtitle) }}">
                    </div>
                </div>

                {{-- Cột phải --}}
                <div class="col-md-6">
                     <div class="form-group">
                        <label>Icon (FontAwesome Class)</label>
                        <div class="input-group">
                            <input type="text" name="icon" class="form-control" 
                                   value="{{ old('icon', $contentBlock->icon) }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="{{ $contentBlock->icon ?? 'fas fa-icons' }}"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hoặc Upload Ảnh mới</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Chọn file ảnh...</label>
                        </div>
                        
                        {{-- Khu vực hiển thị ảnh --}}
                        <div class="mt-2">
                            @if($contentBlock->image)
                                <p class="text-sm text-muted mb-1">Ảnh hiện tại:</p>
                                <img src="{{ asset('storage/' . $contentBlock->image) }}" 
                                     class="img-thumbnail" style="height: 100px" id="current-img">
                            @endif
                            
                            {{-- Ảnh Preview khi chọn mới --}}
                            <img id="preview-img" src="#" alt="Preview" 
                                 class="img-thumbnail mt-2" style="height: 100px; display: none;">
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label>Thứ tự hiển thị</label>
                        <input type="number" name="sort_order" class="form-control" 
                               value="{{ old('sort_order', $contentBlock->sort_order) }}">
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label>Mô tả ngắn (Description)</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $contentBlock->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Đường dẫn (URL)</label>
                        <input type="text" name="url" class="form-control" 
                               value="{{ old('url', $contentBlock->url) }}">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" 
                                   {{ $contentBlock->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="isActive">Kích hoạt hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Cập nhật</button>
            <a href="{{ route('admin.content-blocks.index') }}" class="btn btn-default">Hủy bỏ</a>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        // Logic hiển thị tên file và preview ảnh
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
            
            // Preview
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview-img').attr('src', e.target.result).show();
                    $('#current-img').hide(); // Ẩn ảnh cũ đi cho đỡ rối
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
<script src="{{ asset('vendor/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

@endpush
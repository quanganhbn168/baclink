@csrf
<div class="form-group">
    <label for="name">Tên thuộc tính</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
           value="{{ old('name', $attribute->name ?? '') }}" required>
    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="type">Kiểu hiển thị (cho khách hàng)</label>
    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
        @php $currentType = old('type', $attribute->type ?? 'button'); @endphp
        <option value="button" @selected($currentType == 'button')>Nút bấm (Button)</option>
        <option value="dropdown" @selected($currentType == 'dropdown')>Dropdown</option>
        <option value="color_swatch" @selected($currentType == 'color_swatch')>Ô màu (Color Swatch)</option>
        <option value="image_swatch" @selected($currentType == 'image_swatch')>Ô hình ảnh (Image Swatch)</option>
    </select>
</div>

{{-- PHẦN MỚI: CHECKBOX PHÂN LOẠI --}}
<div class="form-group">
    <div class="form-check">
        <input type="hidden" name="is_variant_defining" value="0">
        <input class="form-check-input" type="checkbox" name="is_variant_defining" value="1" id="is_variant_defining" 
            @checked(old('is_variant_defining', $attribute->is_variant_defining ?? false))>
        <label class="form-check-label" for="is_variant_defining">
            Dùng để tạo biến thể sản phẩm?
        </label>
    </div>
    <small class="form-text text-muted">
        Tick vào nếu thuộc tính này dùng để tạo ra các phiên bản khác nhau của sản phẩm (VD: Màu sắc, Kích cỡ).
        Bỏ trống nếu chỉ dùng làm thông số kỹ thuật để lọc (VD: Hãng, Chất liệu).
    </small>
</div>

<button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Lưu' }}</button>
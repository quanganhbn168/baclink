<div class="form-group">
    @if($label)
        <label for="{{ $inputId }}">
            {{ $label }} @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif

    {{-- Dùng $finalValue đã xử lý từ Class --}}
    <input type="hidden" name="{{ $name }}" value='{!! $finalValue !!}'>

    {{-- Preview (dùng $previewId từ Class) --}}
    <div id="{{ $previewId }}" class="mb-2"></div>

    {{-- Input giả (dùng $inputId, $name, $previewId, $multiple, $required từ Class) --}}
    <input
        type="file"
        id="{{ $inputId }}"
        class="form-control"
        data-picker="media"
        data-name="{{ $name }}"
        data-preview="#{{ $previewId }}"
        data-multiple="{{ $multiple ? 1 : 0 }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
    />

    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @else
        <small class="form-text text-muted">Click để chọn ảnh từ thư viện (tab Ảnh có sẵn).</small>
    @endif

    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Phần CSS này vẫn giữ nguyên --}}
@once
@push('css')
<style>
.media-item {
     position: relative;
     overflow: hidden;
}
.media-item.selected {
     border: 3px solid #28a745 !important;
     box-shadow: 0 0 0 3px rgba(40,167,69,.12) inset, 0 6px 18px rgba(0,0,0,.10);
}
.media-item .checkmark { display: none !important; }
.media-item.selected::after {
     content: "";
     position: absolute;
     right: 0;
     bottom: 0;
     border-left: 52px solid transparent;
     border-bottom: 52px solid #28a745;
     z-index: 1;
}
.media-item.selected::before {
     content: "✓";
     position: absolute;
     right: 8px;
     bottom: 5px;
     font-size: 18px;
     line-height: 1;
     color: #fff;
     font-weight: 700;
     text-shadow: 0 1px 2px rgba(0,0,0,.3);
     z-index: 2;
}
.media-item.selected:hover {
     transform: translateY(-1px);
}
</style>
@endpush
@endonce

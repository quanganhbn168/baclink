{{-- x-form.select2 --}}
@props([
    'name',
    'label' => '',
    'options' => [],      // array|Collection; id=>text hoặc Collection(model có ->id, ->name|->title)
    'selected' => null,   // string|array|null
    'required' => false,
    'placeholder' => '--- Chọn ---',
    'multiple' => false,
    'max' => null,        // số lượng tối đa (chỉ áp dụng khi multiple)
    'closeOnSelect' => true,
])

@php
    // Chuẩn hoá selected (ưu tiên old)
    $selected = old($name, $selected);

    if ($multiple) {
        $selected = is_array($selected) ? array_map('strval', $selected) : (strlen((string)$selected) ? [(string)$selected] : []);
    } else {
        $selected = (string)($selected ?? '');
    }

    // Chuẩn hoá options về [id => text]
    $normalized = [];
    if ($options instanceof \Illuminate\Support\Collection) $options = $options->all();

    foreach ($options as $k => $v) {
        if (is_object($v)) {
            $id   = (string)($v->id ?? $k);
            $text = $v->name ?? $v->title ?? (string)$v;
            $normalized[$id] = $text;
        } else {
            // $v là text, $k là id
            $normalized[(string)$k] = $v;
        }
    }

    // Tạo id theo name (giống component select của anh: id = name)
    $fieldId = $attributes->get('id', $name);

    // Tạo name[] nếu multiple
    $fieldName = $multiple ? ($name . '[]') : $name;
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $fieldId }}">
            {{ $label }} @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif

    <select
        name="{{ $fieldName }}"
        id="{{ $fieldId }}"
        {{ $multiple ? 'multiple' : '' }}
        data-placeholder="{{ $placeholder }}"
        data-close-on-select="{{ $closeOnSelect ? '1' : '0' }}"
        @if($multiple && $max) data-max="{{ (int)$max }}" @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
        {{-- Placeholder: với Select2 nên để value="" để placeholder chuẩn --}}
        @unless($multiple)
            <option value="">{{ $placeholder }}</option>
        @endunless

        @foreach($normalized as $id => $text)
            @if($multiple)
                <option value="{{ $id }}" {{ in_array((string)$id, $selected, true) ? 'selected' : '' }}>{{ $text }}</option>
            @else
                <option value="{{ $id }}" {{ ((string)$id === $selected) ? 'selected' : '' }}>{{ $text }}</option>
            @endif
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
    @push('js')
    <script>
    (function($){
        $(function(){
            // Khởi tạo cho tất cả select có data-placeholder (chỉ init 1 lần)
            $('select[data-placeholder]').each(function(){
                var $el = $(this);
                if ($el.data('select2')) return; // tránh init lại

                var closeOnSelect = $el.data('close-on-select') === 1 || $el.data('close-on-select') === '1';
                var maximumSelectionLength = $el.data('max') ? parseInt($el.data('max'), 10) : 0;

                var cfg = {
                    width: '100%',
                    placeholder: $el.data('placeholder') || '',
                    dropdownAutoWidth: true,
                    closeOnSelect: closeOnSelect
                };
                if ($el.is('[multiple]') && maximumSelectionLength > 0) {
                    cfg.maximumSelectionLength = maximumSelectionLength;
                    cfg.language = {
                        maximumSelected: function (e) {
                            return 'Bạn chỉ được chọn tối đa ' + e.maximum + ' mục';
                        }
                    };
                }

                $el.select2(cfg);
            });
        });
    })(jQuery);
    </script>
    @endpush
@endonce

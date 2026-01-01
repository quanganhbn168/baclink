@props([
    'name',
    'label' => '',
    'selected' => [],
    'url',
    'placeholder' => 'Nhập để tìm hoặc thêm mới',
    'multiple' => false,
    'required' => false,
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif

    <select 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
        id="select-{{ $name }}" 
        class="form-control select2-ajax"
        data-url="{{ $url }}"
        data-placeholder="{{ $placeholder }}"
        {{ $multiple ? 'multiple' : '' }}
        {{ $required ? 'required' : '' }}
    >
        @foreach ($selected as $item)
            <option value="{{ $item['id'] }}" selected>{{ $item['text'] }}</option>
        @endforeach
    </select>
</div>

@once
    @push('css')
        <style>
            .select2-container--bootstrap4 .select2-selection--single,
            .select2-container--bootstrap4 .select2-selection--multiple {
                border: 1px solid #ced4da;
                border-radius: 0.25rem;
                height: calc(2.25rem + 2px);
            }
        </style>
    @endpush

    @push('js')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                $('.select2-ajax').each(function () {
                    let el = $(this);
                    el.select2({
                        theme: 'bootstrap4',
                        tags: true,
                        placeholder: el.data('placeholder'),
                        allowClear: true,
                        createTag: function (params) {
                            const term = $.trim(params.term);
                            if (term === '') return null;
                            return {
                                id: term,
                                text: term,
                                newTag: true
                            };
                        },
                        templateResult: function (data) {
                            if (data.loading) return data.text;
                            if (data.newTag) {
                                return $('<span><i class="fas fa-plus text-white mr-1"></i> Thêm "<strong>' + data.text + '</strong>"</span>');
                            }
                            return data.text;
                        },
                        templateSelection: function (data) {
                            return data.text;
                        },
                        ajax: {
                            url: el.data('url'),
                            dataType: 'json',
                            delay: 250,
                            data: params => ({ q: params.term }),
                            processResults: data => ({
                                results: data.map(item => ({ id: item.id, text: item.name }))
                            }),
                            cache: true
                        },
                        language: {
                            inputTooShort: () => 'Nhập để tìm...',
                            noResults: () => 'Không có dữ liệu',
                            searching: () => 'Đang tìm...',
                        }
                    });
                    el.on('select2:select', function (e) {
                        const selected = e.params.data;
                        if (!selected.newTag) return;
                        $.ajax({
                            type: 'POST',
                            url: el.data('url'), 
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: selected.text
                            },
                            success: function (res) {
                                if (res.id) {
                                    const newOption = new Option(res.text, res.id, true, true);
                                    el.append(newOption).trigger('change');
                                }
                            },
                            error: function () {
                                alert('Không thể tạo mới. Vui lòng thử lại.');
                                el.find("option[value='" + selected.id + "']").remove(); 
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce

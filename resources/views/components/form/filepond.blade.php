@props([
    'name',
    'label',
    'value' => '',
    'required' => false,
    'uploadUrl' => '/admin/upload',
    'uploadFolder' => 'uploads/images',
    'multiple' => false,
])

@php
    $uid = uniqid($name . '_');
    $inputName = $multiple ? $name . '[]' : $name;

    $values = collect($multiple
        ? (is_array($value) ? $value : (empty($value) ? [] : [$value]))
        : [$value]
    )->filter(fn($v) => !empty($v))->values();
@endphp

<div class="form-group row">
    <label class="col-sm-2 col-form-label">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    <div class="col-sm-10">

        {{-- Hiển thị ảnh nhỏ nếu có --}}
        @if (!$multiple)
            <div class="mb-2">
                @php
                    $firstImage = $values->first();
                @endphp
                <img
                    src="{{ asset($firstImage ?: 'images/setting/noimage.png') }}"
                    alt="Ảnh hiện tại"
                    style="height: 80px; object-fit: contain; border: 1px solid #ccc; padding: 2px;"
                >
            </div>
        @endif

        <input
            type="file"
            name="{{ $inputName }}"
            id="{{ $uid }}"
            class="filepond"
            data-max-file-size="5MB"
            data-allow-reorder="true"
            data-allow-image-preview="true"
            data-image-preview-height="140"
            data-max-files="{{ $multiple ? 5 : 1 }}"
            {{ $multiple ? 'multiple' : '' }}
            accept="image/png, image/jpeg, image/jpg, image/webp"
        >

        @if (!$multiple)
            <input type="hidden" name="{{ $name }}" id="{{ $uid }}_hidden" value="{{ old($name, $value) }}">
        @endif

        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('js')
<script>
FilePond.registerPlugin(
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
);

const input_{{ $uid }} = document.getElementById('{{ $uid }}');
const hiddenInput_{{ $uid }} = document.getElementById('{{ $uid }}_hidden');
let oldFile_{{ $uid }} = hiddenInput_{{ $uid }} ? hiddenInput_{{ $uid }}.value : null;

const pond_{{ $uid }} = FilePond.create(input_{{ $uid }}, {
    allowMultiple: {{ $multiple ? 'true' : 'false' }},
    instantUpload: true,
    credits: false,
    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
    maxFileSize: '5MB',
    imagePreviewHeight: 140,
    server: {
        process: {
            url: '{{ $uploadUrl }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            ondata: (formData) => {
                formData.append('folder', '{{ $uploadFolder }}');
                formData.append('field', '{{ $name }}');
                return formData;
            },
            onload: (res) => {
                const result = JSON.parse(res);

                if (oldFile_{{ $uid }} && oldFile_{{ $uid }} !== result.paths) {
                    fetch('/filepond/delete', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ path: oldFile_{{ $uid }} })
                    });
                }

                if (hiddenInput_{{ $uid }}) {
                    hiddenInput_{{ $uid }}.value = result.paths;
                    oldFile_{{ $uid }} = result.paths;
                }

                return result.paths;
            }
        },
        revert: (uniqueFileId, load, error) => {
            let path = uniqueFileId;
            if (path.startsWith(location.origin)) {
                path = path.replace(location.origin + '/', '');
            }

            fetch('/filepond/delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ path })
            })
            .then(res => res.ok ? load() : error('Không thể xoá file'))
            .catch(() => error('Lỗi xoá file'));

            if (hiddenInput_{{ $uid }}) {
                hiddenInput_{{ $uid }}.value = '';
                oldFile_{{ $uid }} = null;
            }
        },
        load: (source, load, error, progress, abort, headers) => {
            fetch(source)
                .then(res => res.blob())
                .then(load)
                .catch(error);
        }
    }
});

(async () => {
    @foreach ($values as $file)
        @if ($file)
        pond_{{ $uid }}.files = [
            {
                source: "{{ asset($file) }}",
                options: {
                    type: 'local',
                    file: {
                        name: "{{ basename($file) }}",
                        size: 123456,
                        type: "{{ \Illuminate\Support\Facades\File::mimeType(public_path($file)) ?? 'image/webp' }}"
                    },
                    metadata: {
                        serverId: "{{ $file }}"
                    }
                }
            }
        ];
        @break
        @endif
    @endforeach
})();
</script>
@endpush

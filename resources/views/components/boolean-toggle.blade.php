@props([
    'model',
    'record',
    'field',
    'onText' => 'Hiện',
    'offText' => 'Ẩn',
])

@php
    $id     = is_object($record) ? $record->id : $record;
    $value  = is_object($record) ? $record->{$field} : false;
    $uid    = 'toggle-' . $model . '-' . $id . '-' . $field;
@endphp

<span id="{{ $uid }}"
      class="badge badge-{{ $value ? 'success' : 'danger' }} boolean-toggle"
      data-model="{{ $model }}"
      data-id="{{ $id }}"
      data-field="{{ $field }}"
      style="cursor: pointer;">
    {{ $value ? $onText : $offText }}
</span>

@once
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.boolean-toggle').forEach(el => {
        el.addEventListener('click', async function () {
            const span = this;

            const payload = {
                _token: '{{ csrf_token() }}',
                model : span.dataset.model,
                id    : span.dataset.id,
                field : span.dataset.field,
            };

            try {
                const res = await fetch('{{ route("admin.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': payload._token
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();

                if (json.success) {
                    const newValue = json.value;
                    span.classList.remove('badge-success', 'badge-danger');
                    span.classList.add(newValue ? 'badge-success' : 'badge-danger');
                    span.textContent = newValue ? '{{ $onText }}' : '{{ $offText }}';
                } else {
                    alert(json.error ?? 'Đã xảy ra lỗi.');
                }

            } catch (err) {
                alert('Không kết nối được đến máy chủ.');
            }
        });
    });
});
</script>
@endpush
@endonce

@extends('layouts.admin')

@section('title', 'Cập nhật thuộc tính')
@section('content_header_title', 'Cập nhật thuộc tính: ' . $attribute->name)

@section('content')
<form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST">
    @csrf
    @method('PUT')

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Đã có lỗi xảy ra!</h5>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- CỘT TRÁI: THÔNG TIN CHUNG --}}
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Thông tin chung</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Tên thuộc tính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $attribute->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="type">Loại hiển thị</label>
                        {{-- Lưu ý: Thường thì khi đã tạo rồi hạn chế cho đổi loại để tránh mất dữ liệu màu, nhưng ở đây em vẫn để --}}
                        <select class="form-control" name="type" id="type-select">
                            <option value="text" {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>Văn bản (Text)</option>
                            <option value="color" {{ old('type', $attribute->type) == 'color' ? 'selected' : '' }}>Màu sắc (Color)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_variant_defining" name="is_variant_defining" value="1" {{ old('is_variant_defining', $attribute->is_variant_defining) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="is_variant_defining">Dùng để tạo biến thể</label>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-default btn-block">Quay lại</a>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: DANH SÁCH GIÁ TRỊ --}}
        <div class="col-md-8">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Các giá trị (Options)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-warning btn-xs" id="btn-add-value">
                            <i class="fas fa-plus"></i> Thêm dòng mới
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped projects" id="values-table">
                        <thead>
                            <tr>
                                <th style="width: 50%">Tên giá trị <span class="text-danger">*</span></th>
                                <th class="col-color" style="display:none; width: 40%">Mã màu</th>
                                <th style="width: 10%" class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="values-container">
                            {{-- Loop qua các giá trị hiện có trong Database --}}
                            @foreach($attribute->values as $index => $val)
                                <tr class="value-row">
                                    {{-- QUAN TRỌNG: Input Hidden chứa ID để Controller biết đường Update --}}
                                    <input type="hidden" name="values[{{ $index }}][id]" value="{{ $val->id }}">

                                    <td>
                                        <input type="text" name="values[{{ $index }}][value]" class="form-control form-control-sm" value="{{ old('values.'.$index.'.value', $val->value) }}" required>
                                    </td>
                                    <td class="col-color" style="display:none;">
                                        <input type="color" name="values[{{ $index }}][color_code]" class="form-control form-control-sm w-100" value="{{ old('values.'.$index.'.color_code', $val->color_code ?? '#000000') }}" style="height: 31px; cursor: pointer;">
                                    </td>
                                    <td class="text-center">
                                        {{-- Nút xóa này sẽ xóa dòng trên giao diện, khi submit Controller sẽ đối chiếu ID để xóa trong DB --}}
                                        <button type="button" class="btn btn-danger btn-xs btn-remove" title="Xóa dòng này">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <small class="text-muted font-italic">
                        <i class="fas fa-info-circle"></i> Nếu bạn xóa một dòng ở đây và bấm Cập nhật, giá trị đó sẽ bị xóa vĩnh viễn khỏi hệ thống.
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Template dòng mới (giống hệt Create) --}}
<template id="value-row-template">
    <tr class="value-row new-row">
        {{-- Dòng mới không có ID --}}
        <td>
            <input type="text" name="values[{index}][value]" class="form-control form-control-sm" placeholder="Giá trị mới..." required>
        </td>
        <td class="col-color" style="display:none;">
            <input type="color" name="values[{index}][color_code]" class="form-control form-control-sm w-100" value="#000000" style="height: 31px; cursor: pointer;">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-xs btn-remove">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        const $typeSelect = $('#type-select');
        const $container = $('#values-container');
        const template = $('#value-row-template').html();
        
        // Đếm số dòng hiện tại để tạo index tiếp theo tránh trùng lặp name="values[index]..."
        // Ví dụ database đang có 3 dòng (index 0,1,2), thì dòng mới phải là 3
        let rowIdx = {{ $attribute->values->count() > 0 ? $attribute->values->count() : 0 }};
        
        // Nếu validation fail và redirect lại, rowIdx có thể bị lệch, ta đếm lại theo DOM cho chắc
        rowIdx = Math.max(rowIdx, $container.find('tr').length);

        function toggleColorColumn() {
            const type = $typeSelect.val();
            if (type === 'color') {
                $('.col-color').show();
            } else {
                $('.col-color').hide();
            }
        }

        $typeSelect.on('change', toggleColorColumn);
        toggleColorColumn();

        $('#btn-add-value').click(function() {
            // Tăng index lên để không trùng với các dòng cũ
            let newRow = template.replace(/{index}/g, 'new_' + rowIdx++);
            $container.append(newRow);
            toggleColorColumn();
        });

        $container.on('click', '.btn-remove', function() {
            if ($container.find('tr').length > 1) {
                // Đánh dấu visual warning cho người dùng biết (optional)
                $(this).closest('tr').css('background-color', '#ffcccc').fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                alert('Không thể xóa hết, cần giữ lại ít nhất 1 giá trị.');
            }
        });
    });
</script>
@endpush

{{-- resources/views/admin/attributes/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Thêm thuộc tính mới')
@section('content_header_title', 'Thêm thuộc tính')

@section('content')
<form action="{{ route('admin.attributes.store') }}" method="POST">
    @csrf

    {{-- Hiển thị lỗi tổng quát nếu có --}}
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
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="VD: Màu sắc..." required>
                    </div>

                    <div class="form-group">
                        <label for="type">Loại hiển thị</label>
                        <select class="form-control" name="type" id="type-select">
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Văn bản (Text)</option>
                            <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Màu sắc (Color)</option>
                        </select>
                        <small class="form-text text-muted">Chọn "Màu sắc" để hiển thị ô chọn màu cho từng giá trị.</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_variant_defining" name="is_variant_defining" value="1" {{ old('is_variant_defining', 1) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="is_variant_defining">Dùng để tạo biến thể</label>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Lưu thuộc tính
                    </button>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-default btn-block">Quay lại</a>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: CÁC GIÁ TRỊ --}}
        <div class="col-md-8">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">Các giá trị (Options)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-xs" id="btn-add-value">
                            <i class="fas fa-plus"></i> Thêm dòng
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
                            {{-- Nếu có old data (khi validate fail) thì loop lại, nếu không thì hiện 1 dòng trống --}}
                            @if(old('values'))
                                @foreach(old('values') as $key => $val)
                                    <tr class="value-row">
                                        <td>
                                            <input type="text" name="values[{{ $key }}][value]" class="form-control form-control-sm" value="{{ $val['value'] }}" placeholder="VD: Đỏ, XL..." required>
                                        </td>
                                        <td class="col-color" style="display:none;">
                                            <input type="color" name="values[{{ $key }}][color_code]" class="form-control form-control-sm w-100" value="{{ $val['color_code'] ?? '#000000' }}" style="height: 31px; cursor: pointer;">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-xs btn-remove"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Dòng mặc định --}}
                                <tr class="value-row">
                                    <td>
                                        <input type="text" name="values[0][value]" class="form-control form-control-sm" placeholder="VD: Đỏ, XL..." required>
                                    </td>
                                    <td class="col-color" style="display:none;">
                                        <input type="color" name="values[0][color_code]" class="form-control form-control-sm w-100" value="#000000" style="height: 31px; cursor: pointer;">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs btn-remove"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <small class="text-muted font-italic"><i class="fas fa-info-circle"></i> Bạn có thể thêm nhiều giá trị cùng lúc.</small>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Template JS --}}
<template id="value-row-template">
    <tr class="value-row">
        <td>
            <input type="text" name="values[{index}][value]" class="form-control form-control-sm" placeholder="Nhập giá trị..." required>
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
        
        // Đếm index tiếp theo dựa trên số dòng hiện có (để hỗ trợ old input)
        let rowIdx = $container.find('tr').length; 

        // Hàm ẩn hiện cột màu
        function toggleColorColumn() {
            const type = $typeSelect.val();
            if (type === 'color') {
                $('.col-color').show();
            } else {
                $('.col-color').hide();
            }
        }

        // Sự kiện đổi loại
        $typeSelect.on('change', toggleColorColumn);
        toggleColorColumn(); // Chạy ngay khi load

        // Thêm dòng
        $('#btn-add-value').click(function() {
            let newRow = template.replace(/{index}/g, rowIdx++);
            $container.append(newRow);
            toggleColorColumn(); // Đồng bộ hiển thị cột màu cho dòng mới
        });

        // Xóa dòng
        $container.on('click', '.btn-remove', function() {
            if ($container.find('tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                // Thay vì alert, dùng Toast của AdminLTE nếu có, hoặc alert thường
                alert('Bạn cần nhập ít nhất 1 giá trị.');
            }
        });
    });
</script>
@endpush

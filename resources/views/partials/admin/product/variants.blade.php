{{-- resources/views/admin/products/partials/variants.blade.php --}}

<style>
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { height: 38px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; }
</style>

<div class="variant-generator-section border-bottom mb-4 pb-4">
    <label class="font-weight-bold text-primary">1. Cấu hình tạo biến thể</label>
    <div id="attribute-rows-container"></div>
    <div class="mt-3">
        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-attribute-row">
            <i class="fas fa-plus"></i> Thêm thuộc tính
        </button>
        <button type="button" class="btn btn-success btn-sm ml-2" id="btn-generate-variants">
            <i class="fas fa-bolt"></i> Tạo danh sách biến thể
        </button>
    </div>
</div>

<div class="variant-list-section">
    <div class="d-flex justify-content-between align-items-end mb-2">
        <label class="font-weight-bold text-primary mb-0">2. Danh sách phiên bản chi tiết</label>
        
        {{-- NÚT TOGGLE CÔNG CỤ SỬA HÀNG LOẠT --}}
        <button type="button" class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#bulk-edit-tools">
            <i class="fas fa-tools"></i> Sửa hàng loạt
        </button>
    </div>

    {{-- KHU VỰC SỬA HÀNG LOẠT (Mặc định ẩn cho gọn) --}}
    <div class="collapse mb-3" id="bulk-edit-tools">
        <div class="card card-body bg-light border">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="small text-muted">Giá bán chung</label>
                    <input type="number" id="bulk-price" class="form-control form-control-sm" placeholder="Nhập giá...">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Giá gốc chung</label>
                    <input type="number" id="bulk-compare-price" class="form-control form-control-sm" placeholder="Nhập giá gốc...">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Tồn kho chung</label>
                    <input type="number" id="bulk-stock" class="form-control form-control-sm" placeholder="Nhập số lượng...">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary btn-sm btn-block" id="btn-apply-bulk">
                        <i class="fas fa-check-double"></i> Áp dụng cho tất cả
                    </button>
                </div>
            </div>
            <small class="text-muted mt-2">
                * Nhập giá trị vào ô bạn muốn sửa đổi, sau đó nhấn "Áp dụng". Các ô để trống sẽ không thay đổi dữ liệu cũ.
            </small>
        </div>
    </div>

    <div class="table-responsive border rounded shadow-sm">
        <table class="table table-bordered mb-0 table-hover" id="variant-table">
            <thead class="bg-light text-dark">
                <tr>
                    <th style="width: 25%">Tên phiên bản</th>
                    <th style="width: 20%">SKU <span class="text-danger">*</span></th>
                    <th style="width: 15%">Giá bán <span class="text-danger">*</span></th>
                    <th style="width: 15%">Giá gốc</th>
                    <th style="width: 15%">Tồn kho</th>
                    <th style="width: 10%" class="text-center">Xóa</th>
                </tr>
            </thead>
            <tbody id="variant-container">
    {{-- Dữ liệu cũ (Edit) --}}
    @if(isset($product) && $product->variants->count() > 0)
        @foreach($product->variants as $index => $variant)
            <tr class="variant-row existing-variant">
                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                
                {{-- ======================================================== --}}
                {{-- THÊM DÒNG NÀY ĐỂ JS BIẾT BIẾN THỂ NÀY GỒM NHỮNG GÌ --}}
                {{-- Pluck ID, sắp xếp tăng dần, nối bằng dấu phẩy --}}
                {{-- ======================================================== --}}
                <input type="hidden" 
                       name="variants[{{ $index }}][attribute_value_ids]" 
                       class="variant-attr-ids" 
                       value="{{ $variant->attributeValues->pluck('id')->sort()->implode(',') }}">

                <td>
                    <input type="text" name="variants[{{ $index }}][variant_name]" class="form-control font-weight-bold" value="{{ $variant->variant_name }}" readonly required>
                </td>
                
                {{-- ... Các cột khác giữ nguyên ... --}}
                 <td>
                    <input type="text" name="variants[{{ $index }}][sku]" class="form-control" value="{{ $variant->sku }}" required>
                </td>
                <td>
                    <input type="number" name="variants[{{ $index }}][price]" class="form-control" value="{{ $variant->price }}" min="0" required>
                </td>
                <td>
                    <input type="number" name="variants[{{ $index }}][compare_at_price]" class="form-control" value="{{ $variant->compare_at_price }}" min="0">
                </td>
                <td>
                    <input type="number" name="variants[{{ $index }}][stock]" class="form-control" value="{{ $variant->stock }}" min="0" required>
                </td>
                <td class="text-center">
                    <input type="hidden" name="variants[{{ $index }}][delete_flag]" class="delete-flag" value="0">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-variant">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    @endif
</tbody>
        </table>
    </div>
    <div class="mt-2 text-center" id="empty-variant-notify" style="{{ (isset($product) && $product->variants->count() > 0) ? 'display:none' : '' }}">
        <span class="text-muted font-italic">Chưa có biến thể nào được tạo.</span>
    </div>
</div>

{{-- TEMPLATE ATTRIBUTE --}}
<template id="attribute-select-template">
    <div class="row attribute-row mb-2 align-items-center bg-light p-2 rounded border">
        <div class="col-md-3">
            <label class="small text-muted mb-0">Thuộc tính:</label>
            <select class="form-control attribute-type-select select2">
                <option value="">-- Chọn --</option>
            </select>
        </div>
        <div class="col-md-8">
            <label class="small text-muted mb-0">Giá trị:</label>
            <div class="value-container">
                <select class="form-control attribute-value-select select2" multiple="multiple" disabled></select>
            </div>
        </div>
        <div class="col-md-1 text-center d-flex align-items-end justify-content-center">
            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-attr-row mt-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

{{-- TEMPLATE VARIANT --}}
<template id="variant-row-template">
    <tr class="variant-row new-variant">
        <input type="hidden" name="variants[{index}][id]" value="">
        <input type="hidden" name="variants[{index}][attribute_value_ids]" class="variant-attr-ids" value="{attr_ids}">
        <td><input type="text" name="variants[{index}][variant_name]" class="form-control font-weight-bold" value="{name}" readonly required></td>
        <td><input type="text" name="variants[{index}][sku]" class="form-control" value="{sku}"></td>
        <td><input type="number" name="variants[{index}][price]" class="form-control" value="{price}" min="0" required></td>
        <td><input type="number" name="variants[{index}][compare_at_price]" class="form-control" value="" min="0"></td>
        <td><input type="number" name="variants[{index}][stock]" class="form-control" value="{stock}" min="0" required></td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btn-remove-variant"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>
</template>

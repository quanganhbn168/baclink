{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm: ' . $product->name)
@section('content_header_title', 'Chỉnh sửa sản phẩm')

@section('content')
<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" 
      id="product-form"
      x-data="productForm()" 
      @submit.prevent="submitForm">
    @csrf
    @method('PUT')

    {{-- Hiển thị lỗi Server --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle"></i> Vui lòng kiểm tra lại dữ liệu:</strong>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow mb-4">
        {{-- Header Tabs --}}
        <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="editProductTab" role="tablist" style="margin-left: 1rem;">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="general-tab" data-toggle="tab" href="#general" role="tab">
                        <i class="fas fa-info-circle mr-1"></i> Thông tin chung
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="media-tab" data-toggle="tab" href="#media" role="tab">
                        <i class="fas fa-images mr-1"></i> Hình ảnh
                    </a>
                </li>
                <li class="nav-item" x-show="hasVariants">
                    <a class="nav-link font-weight-bold" id="variants-tab" data-toggle="tab" href="#variants" role="tab">
                        <i class="fas fa-th-large mr-1"></i> Biến thể
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="seo-tab" data-toggle="tab" href="#seo" role="tab">
                        <i class="fab fa-google mr-1"></i> SEO
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="editProductTabContent">
                
                {{-- TAB 1: THÔNG TIN CHUNG --}}
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row">
                        {{-- Cột bên trái: Thông tin chính --}}
                        <div class="col-md-8">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title text-bold">Thông tin cơ bản</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <x-form.input name="name" label="Tên sản phẩm" x-model="formData.name" required placeholder="Nhập tên sản phẩm..." />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-form.textarea name="description" label="Mô tả ngắn" rows="3" placeholder="Mô tả ngắn gọn về sản phẩm..." :value="old('description', $product->description)" />
                                        </div>
                                        <div class="col-12 mt-3">
                                            <x-form.ckeditor name="content" label="Nội dung chi tiết" :value="old('content', $product->content)" />
                                        </div>
                                        <div class="col-12 mt-3">
                                             <x-form.ckeditor name="specifications" label="Thông số kỹ thuật" :value="old('specifications', $product->specifications ?? '')" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Giá bán (Chỉ hiện khi KHÔNG có biến thể) --}}
                            <div class="card card-secondary card-outline" x-show="!hasVariants">
                                <div class="card-header">
                                    <h3 class="card-title text-bold">Giá bán & Tồn kho</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <x-form.money-input name="price" label="Giá bán (VNĐ)" :value="old('price', $product->price)" />
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.money-input name="price_discount" label="Giá khuyến mãi" :value="old('price_discount', $product->price_discount)" help="Để trống nếu không giảm" />
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.money-input name="stock" label="Tồn kho" :value="old('stock', $product->stock)" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cột bên phải: Cấu hình & Tổ chức --}}
                        <div class="col-md-4">
                            {{-- Trạng thái & Loại sản phẩm --}}
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h3 class="card-title text-bold">Trạng thái</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <x-form.switch name="status" label="Hiển thị sản phẩm" :checked="old('status', $product->status)" />
                                    </div>
                                    <div class="form-group">
                                        <x-form.switch name="is_featured" label="Sản phẩm nổi bật" :checked="old('is_featured', $product->is_featured)" />
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <x-form.switch name="has_variants" label="Sản phẩm có biến thể" x-model="hasVariants" />
                                        <small class="form-text text-muted" x-show="hasVariants">
                                            <i class="fas fa-info-circle"></i> Vui lòng cấu hình chi tiết bên tab "Biến thể".
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Tổ chức (Mã, Danh mục) --}}
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title text-bold">Phân loại</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <x-auto-code name="code" label="Mã sản phẩm (SKU)" source="#name" :value="old('code', $product->code)" :check-url="route('admin.products.validate_uniqueness')" :current-id="$product->id" />
                                        <small class="text-muted">Nhập tên để tự động tạo mã.</small>
                                    </div>
                                    <div class="form-group">
                                        <x-form.select name="category_id" label="Danh mục chính" :options="$categories ?? []" :value="old('category_id', $product->category_id)" required placeholder="-- Chọn danh mục --" />
                                    </div>
                                     <div class="form-group">
                                         <label>Thương hiệu</label>
                                         <select name="brand_id" class="form-control select2">
                                             <option value="">-- Không chọn --</option>
                                         </select>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Slug --}}
                            <div class="card card-secondary card-outline">
                                <div class="card-body">
                                    <x-form.slug name="slug" label="Đường dẫn (Slug)" :value="old('slug', $product->slug)" source="#name" table="products" field="slug" :current-id="$product->id" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: HÌNH ẢNH --}}
                <div class="tab-pane fade" id="media" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="font-weight-bold mb-3">Ảnh đại diện</h6>
                                    <x-admin.form.media-input name="image_original_path" label="" :multiple="false" :value="old('image_original_path', optional($product->mainImage())->original_path)" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                             <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="font-weight-bold mb-3">Album ảnh</h6>
                                    <x-admin.form.media-input name="gallery_original_paths" label="" :multiple="true" :value="old('gallery_original_paths', $product->gallery->pluck('original_path'))" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: BIẾN THỂ (Dynamic) --}}
                <div class="tab-pane fade" id="variants" role="tabpanel">
                    {{-- Chọn thuộc tính để tạo biến thể --}}
                    <div class="card card-primary card-outline mb-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-cogs"></i> Cấu hình thuộc tính</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                     <div class="form-group">
                                        <label>1. Chọn các thuộc tính dùng cho biến thể (VD: Màu sắc, Kích thước)</label>
                                        <select class="form-control select2" multiple id="attribute-select" style="width: 100%;">
                                            @foreach($attributes as $attr)
                                                <option value="{{ $attr->id }}" data-values='@json($attr->values)'>{{ $attr->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Khu vực chọn giá trị cho từng thuộc tính đã chọn --}}
                            <div class="row mt-3" x-show="selectedAttributes.length > 0">
                                <template x-for="(attr, index) in selectedAttributes" :key="attr.id">
                                    <div class="col-md-4 mb-3">
                                        <div class="card card-secondary card-outline">
                                            <div class="card-header pt-2 pb-2">
                                                <h3 class="card-title text-sm font-weight-bold" x-text="attr.name"></h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" @click="removeAttribute(attr.id)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {{-- Checkbox list các giá trị --}}
                                                <div style="max-height: 150px; overflow-y: auto;">
                                                    <template x-for="val in attr.allValues" :key="val.id">
                                                        <div class="custom-control custom-checkbox mb-1">
                                                            <input type="checkbox" class="custom-control-input" 
                                                                   :id="'val-' + val.id" 
                                                                   :value="val.id" 
                                                                   x-model="attr.selectedValues">
                                                            <label class="custom-control-label" :for="'val-' + val.id" x-text="val.value"></label>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <button type="button" class="btn btn-primary mt-2" @click="generateVariants" :disabled="selectedAttributes.length === 0">
                                <i class="fas fa-sync-alt"></i> Tạo lại danh sách biến thể
                            </button>
                            <small class="text-danger d-block mt-1">* Chú ý: Việc tạo lại sẽ xóa danh sách biến thể hiện tại và tạo mới hoàn toàn.</small>
                        </div>
                    </div>

                    {{-- Bảng danh sách biến thể đã tạo --}}
                    <div class="table-responsive" x-show="variants.length > 0">
                        <table class="table table-bordered table-hover text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px" class="text-center">#</th>
                                    <th>Tên biến thể</th>
                                    <th style="width: 150px">Mã SKU <span class="text-danger">*</span></th>
                                    <th style="width: 150px">Giá bán <span class="text-danger">*</span></th>
                                    <th style="width: 150px">Giá gốc</th>
                                    <th style="width: 100px">Kho <span class="text-danger">*</span></th>
                                    <th style="width: 50px" class="text-center">Xóa</th>
                                </tr>
                                {{-- Dòng Bulk Edit --}}
                                <tr class="table-info">
                                    <td colspan="2" class="text-right font-weight-bold align-middle">Áp dụng nhanh:</td>
                                    <td><input type="text" class="form-control form-control-sm" placeholder="SKU chung..." x-model="bulk.sku"></td>
                                    <td><input type="number" class="form-control form-control-sm" placeholder="Giá chung..." x-model="bulk.price"></td>
                                    <td><input type="number" class="form-control form-control-sm" placeholder="Giá gốc..." x-model="bulk.original_price"></td>
                                    <td><input type="number" class="form-control form-control-sm" placeholder="SL..." x-model="bulk.stock"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info btn-block" @click="applyBulk" title="Áp dụng cho tất cả">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(variant, index) in variants" :key="index">
                                    <tr x-show="!variant.delete_flag">
                                        <td class="text-center align-middle" x-text="index + 1"></td>
                                        <td class="align-middle">
                                            <strong x-text="variant.variant_name" class="text-primary"></strong>
                                            
                                            {{-- Hidden inputs --}}
                                            <input type="hidden" :name="'variants['+index+'][id]'" :value="variant.id">
                                            <input type="hidden" :name="'variants['+index+'][variant_name]'" :value="variant.variant_name">
                                            <input type="hidden" :name="'variants['+index+'][attribute_value_ids]'" :value="variant.attribute_value_ids">
                                            <input type="hidden" :name="'variants['+index+'][delete_flag]'" :value="variant.delete_flag" class="delete-flag">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   :name="'variants['+index+'][sku]'" 
                                                   x-model="variant.sku" 
                                                   required
                                                   :class="{'is-invalid': !variant.sku}">
                                        </td>
                                        <td>
                                            {{-- Display Formatted Money, Store/Update Raw in 'price' --}}
                                            <input type="text" class="form-control form-control-sm text-right" 
                                                   :value="formatMoney(variant.price)" 
                                                   @input="variant.price = $event.target.value.replace(/\D/g, ''); $event.target.value = formatMoney(variant.price)"
                                                   placeholder="0">
                                            <input type="hidden" :name="'variants['+index+'][price]'" :value="variant.price">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right" 
                                                   :value="formatMoney(variant.compare_at_price)" 
                                                   @input="variant.compare_at_price = $event.target.value.replace(/\D/g, ''); $event.target.value = formatMoney(variant.compare_at_price)"
                                                   placeholder="0">
                                            <input type="hidden" :name="'variants['+index+'][compare_at_price]'" :value="variant.compare_at_price">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-center" :name="'variants['+index+'][stock]'" x-model="variant.stock" required min="0">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-danger" @click="removeExistingVariant(index)" title="Xóa biến thể">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 4: SEO --}}
                <div class="tab-pane fade" id="seo" role="tabpanel">
                      <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="meta_title" label="Meta Title" :value="old('meta_title', $product->meta_title)" placeholder="Tiêu đề hiển thị Google..." />
                            <div class="form-group mt-3">
                                <label>Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $product->meta_keywords) }}" placeholder="Từ khóa, cách nhau dấu phẩy">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-admin.form.media-input name="meta_image" label="Meta Image" :multiple="false" :value="old('meta_image', $product->meta_image)" />
                        </div>
                        <div class="col-12 mt-2">
                             <x-form.textarea name="meta_description" label="Meta Description" rows="3" :value="old('meta_description', $product->meta_description)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white text-right border-top sticky-bottom" style="z-index: 1000">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Hủy</a>
             <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật sản phẩm
            </button>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    // Chuẩn bị dữ liệu từ Server để nạp vào Alpine
    
    // 1. Dữ liệu Variants hiện có
    // Cần map lại để khớp với object trong Alpine (thêm delete_flag, ...)
    var existingVariants = {!! json_encode($product->variants->map(function($v) {
        return [
            'id' => $v->id, // Có ID để update
            'variant_name' => $v->variant_name,
            'sku' => $v->sku,
            'price' => (float) $v->price,
            'compare_at_price' => (float) $v->compare_at_price,
            'stock' => $v->stock,
            'attribute_value_ids' => $v->attributeValues->pluck('id')->implode(','),
            'delete_flag' => 0 // Mặc định chưa xóa
        ];
    })) !!};

    // 2. Attributes đã chọn (Tính toán từ Controller hoặc View Composer)
    // $usedAttributes được truyền từ controller: [{id: 1, values: [1,2]}]
    // Cần map sang cấu trúc Alpine: {id, name, allValues, selectedValues}
    var usedAttributesSimple = {!! json_encode($usedAttributes) !!}; // from controller
    var allAttributeList = {!! json_encode($attributes) !!}; // raw attributes with values

    // Hàm helper để build selectedAttributes
    function buildSelectedAttributes(used, all) {
        let result = [];
        used.forEach(u => {
            let attrObj = all.find(a => a.id == u.id);
            if(attrObj) {
                result.push({
                    id: attrObj.id,
                    name: attrObj.name,
                    allValues: attrObj.values,
                    selectedValues: u.values // id array
                });
            }
        });
        return result;
    }

    var initialSelectedAttributes = buildSelectedAttributes(usedAttributesSimple, allAttributeList);

    function productForm() {
        return {
            hasVariants: {{ old('has_variants', $product->has_variants) ? 'true' : 'false' }},
            formData: {
                name: '{{ old('name', $product->name) }}'
            },
            selectedAttributes: initialSelectedAttributes, 
            variants: existingVariants.length > 0 ? existingVariants : {!! json_encode(old('variants', [])) !!}, 
            bulk: { sku: '', price: '', original_price: '', stock: '' },

            init() {
                // Khởi tạo Select2 cho thuộc tính
                var self = this;
                var $select = $('#attribute-select');
                
                $select.select2({
                    theme: 'bootstrap4',
                    placeholder: "Chọn thuộc tính...",
                    allowClear: true
                }).on('change', function() {
                    self.handleAttributeSelection($(this).val());
                });

                // Pre-select options trong Select2 dựa trên initialSelectedAttributes
                let initialIds = this.selectedAttributes.map(a => a.id);
                $select.val(initialIds).trigger('change.select2'); // Lưu ý: trigger change có thể kích hoạt handleAttributeSelection lại, cần cẩn thận loop
                
                // Mẹo: handleAttributeSelection logic của mình khá an toàn (chỉ add/remove diff), nên trigger cũng không sao.
            },

            handleAttributeSelection(selectedIds) {
                if (!selectedIds) selectedIds = [];
                // Chuyển về string để so sánh
                selectedIds = selectedIds.map(String);

                // Xóa những cái không còn được chọn
                this.selectedAttributes = this.selectedAttributes.filter(attr => selectedIds.includes(attr.id.toString()));

                // Thêm những cái mới
                 selectedIds.forEach(id => {
                    let exists = this.selectedAttributes.find(a => a.id == id);
                    if (!exists) {
                        let opt = $('#attribute-select option[value="'+id+'"]');
                        let name = opt.text();
                        let values = opt.data('values');
                        
                        this.selectedAttributes.push({
                            id: id,
                            name: name,
                            allValues: values,
                            selectedValues: [] 
                        });
                    }
                });
            },

            removeAttribute(id) {
                this.selectedAttributes = this.selectedAttributes.filter(a => a.id != id);
                let currentVals = $('#attribute-select').val();
                let newVals = currentVals.filter(v => v != id);
                $('#attribute-select').val(newVals).trigger('change');
            },

            generateVariants() {
                // ... (Logic giống hệt create.blade.php) ...
                // Chú ý: Khi generate ở edit, các variant cũ bị thay thế hoàn toàn.
                
                if(!confirm('Cảnh báo: Hành động này sẽ TẠO LẠI toàn bộ danh sách biến thể và XÓA các biến thể hiện tại. Bạn có chắc chắn không?')) {
                    return;
                }

                // 1. Giữ lại các variant ĐÃ CÓ (có ID) và đánh dấu xóa
                let existingToDelete = this.variants.filter(v => v.id).map(v => {
                    v.delete_flag = 1; 
                    return v;
                });

                let attrsToCombine = this.selectedAttributes.filter(a => a.selectedValues.length > 0);
                
                if (attrsToCombine.length === 0) {
                    Swal.fire('Chú ý', 'Vui lòng chọn ít nhất 1 giá trị cho thuộc tính!', 'warning');
                    return;
                }

                let arrays = attrsToCombine.map(a => {
                    return a.selectedValues.map(vId => {
                        let vObj = a.allValues.find(v => v.id == vId);
                        return { id: vId, value: vObj.value, parent: a.name };
                    });
                });

                let combos = this.cartesian(arrays);
                
                let mainSku = $('input[name="code"]').val();
                 if (!mainSku) {
                    let productName = $('input[name="name"]').val();
                    if (productName) {
                        mainSku = this.slugify(productName).toUpperCase();
                        $('input[name="code"]').val(mainSku).trigger('change');
                        toastr.info('Hệ thống tự động tạo Mã SKU từ Tên sản phẩm.');
                    } else {
                        mainSku = 'SKU'; 
                        toastr.warning('Vui lòng nhập Mã sản phẩm (SKU) để mã biến thể được chuẩn hơn.');
                    }
                }

                // 2. Tạo variants mới
                let newVariants = combos.map(combo => {
                    let items = Array.isArray(combo) ? combo : [combo];
                    
                    let name = items.map(i => i.value).join(' - ');
                    let ids = items.map(i => i.id).join(',');
                    
                    let skuSuffix = items.map(i => this.slugify(i.value)).join('-').toUpperCase();
                    let variantSku = `${mainSku}-${skuSuffix}`;

                    return {
                        id: null, // Mới tạo
                        variant_name: name,
                        attribute_value_ids: ids,
                        sku: variantSku,
                        price: ($('input[name="price"]').val() || '').toString().replace(/\D/g, '') || 0,
                        compare_at_price: ($('input[name="price_discount"]').val() || '').toString().replace(/\D/g, '') || '',
                        stock: $('input[name="stock"]').val() || 100,
                        delete_flag: 0
                    };
                });
                
                // 3. Gộp lại: Cũ (đã xóa) + Mới
                this.variants = [...existingToDelete, ...newVariants];
            },

            cartesian(args) {
                var r = [], max = args.length-1;
                function helper(arr, i) {
                    for (var j=0, l=args[i].length; j<l; j++) {
                        var a = arr.slice(0); // clone arr
                        a.push(args[i][j]);
                        if (i==max) r.push(a);
                        else helper(a, i+1);
                    }
                }
                helper([], 0);
                return r;
            },

            // Hàm xóa cho Edit page
            removeExistingVariant(index) {
                // Nếu variant có ID (đã tồn tại trong DB), ta không xóa khỏi mảng mà chỉ set flag
                if (this.variants[index].id) {
                    // Set flag delete
                     this.variants[index].delete_flag = 1;
                } else {
                    // Mới tạo -> xóa khỏi array
                    this.variants.splice(index, 1);
                }
            },

            applyBulk() {
                this.variants.forEach(v => {
                    if (v.delete_flag) return; // Bỏ qua cái đã xóa
                    if(this.bulk.sku) v.sku = this.bulk.sku + '-' + this.slugify(v.variant_name).toUpperCase();
                    if(this.bulk.price) v.price = this.bulk.price;
                    if(this.bulk.original_price) v.compare_at_price = this.bulk.original_price;
                    if(this.bulk.stock) v.stock = this.bulk.stock;
                });
            },
            
            slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')           
                    .replace(/[^\w\-]+/g, '')       
                    .replace(/\-\-+/g, '-')         
                    .replace(/^-+/, '')             
                    .replace(/-+$/, '');            
            },

            formatMoney(value) {
                if (!value) return '';
                let num = value.toString().replace(/[^\d]/g, '');
                return Number(num).toLocaleString('vi-VN');
            },

            submitForm() {
                if (this.hasVariants) {
                     let activeVariants = this.variants.filter(v => !v.delete_flag);
                     if (activeVariants.length === 0) {
                        Swal.fire('Lỗi', 'Bạn đã chọn sản phẩm có biến thể nhưng chưa tạo biến thể nào.', 'error');
                        return;
                     }
                }
                document.getElementById('product-form').submit();
            }
        }
    }
</script>
@endpush

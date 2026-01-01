@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới')
@section('content_header_title', 'Thêm sản phẩm mới')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" 
      id="product-form"
      x-data="productForm()" 
      @submit.prevent="submitForm">
    @csrf

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

    <div class="card shadow mb-4">
        {{-- Header Tabs --}}
        <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="createProductTab" role="tablist" style="margin-left: 1rem;">
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
            <div class="tab-content" id="createProductTabContent">
                
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
                                            <x-form.textarea name="description" label="Mô tả ngắn" rows="3" placeholder="Mô tả ngắn gọn về sản phẩm (hiển thị ở danh sách)..." />
                                        </div>
                                        <div class="col-12 mt-3">
                                            <x-form.ckeditor name="content" label="Nội dung chi tiết" />
                                        </div>
                                        <div class="col-12 mt-3">
                                            <x-form.ckeditor name="specifications" label="Thông số kỹ thuật" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Giá bán (Chỉ hiện khi KHÔNG có biến thể, hoặc cấu hình chung) --}}
                            <div class="card card-secondary card-outline" x-show="!hasVariants">
                                <div class="card-header">
                                    <h3 class="card-title text-bold">Giá bán & Tồn kho</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <x-form.money-input name="price" label="Giá bán (VNĐ)" value="0" />
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.money-input name="price_discount" label="Giá khuyến mãi" value="" />
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.money-input name="stock" label="Tồn kho" value="100" required />
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
                                        <x-form.switch name="status" label="Hiển thị sản phẩm" checked="true" />
                                    </div>
                                    <div class="form-group">
                                        <x-form.switch name="is_featured" label="Sản phẩm nổi bật" />
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label class="font-weight-bold" style="cursor: pointer" for="has_variants">
                                            <i class="fas fa-boxes mr-1"></i> Loại sản phẩm
                                        </label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="has_variants" name="has_variants" value="1" x-model="hasVariants">
                                            <label class="custom-control-label" for="has_variants">Sản phẩm có biến thể</label>
                                        </div>
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
                                        <x-auto-code name="code" label="Mã sản phẩm (SKU)" source="#name" :check-url="route('admin.products.validate_uniqueness')" />
                                        <small class="text-muted">Nhập tên để tự động tạo mã.</small>
                                    </div>
                                    <div class="form-group">
                                        <x-form.select name="category_id" label="Danh mục chính" :options="$categories ?? []" required placeholder="-- Chọn danh mục --" />
                                    </div>
                                     <div class="form-group">
                                        {{-- Dùng component select2 nếu có, hoặc select thường --}}
                                         <label>Thương hiệu</label>
                                         <select name="brand_id" class="form-control select2">
                                             <option value="">-- Không chọn --</option>
                                             {{-- Xử lý brand sau nếu có data --}}
                                         </select>
                                    </div>
                                </div>
                            </div>
                            
                            
                            {{-- Slug --}}
                            <div class="card card-secondary card-outline">
                                <div class="card-body">
                                    <x-form.slug name="slug" label="Đường dẫn (Slug)" source="#name" table="products" field="slug" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    {{-- Duplicates removed --}}
                </div>

                {{-- TAB 2: HÌNH ẢNH --}}
                <div class="tab-pane fade" id="media" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="font-weight-bold mb-3">Ảnh đại diện</h6>
                                    <x-admin.form.media-input name="image_original_path" label="" :multiple="false" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                             <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="font-weight-bold mb-3">Album ảnh</h6>
                                    <x-admin.form.media-input name="gallery_original_paths" label="" :multiple="true" />
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
                                <i class="fas fa-sync-alt"></i> Tạo danh sách biến thể
                            </button>
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
                                    <tr class="align-middle">
                                        <td class="text-center align-middle" x-text="index + 1"></td>
                                        <td class="align-middle">
                                            <strong x-text="variant.variant_name" class="text-primary"></strong>
                                            
                                            {{-- Hidden inputs for submitting raw values --}}
                                            <input type="hidden" :name="'variants['+index+'][variant_name]'" :value="variant.variant_name">
                                            <input type="hidden" :name="'variants['+index+'][attribute_value_ids]'" :value="variant.attribute_value_ids">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   :name="'variants['+index+'][sku]'" 
                                                   x-model="variant.sku" 
                                                   required>
                                        </td>
                                        <td>
                                            {{-- Giá bán: Hiển thị formatted, lưu raw vào hidden nếu cần hoặc clean on submit.
                                                 Ở đây ta lưu raw vào x-model 'price', nhưng input hiển thị formatted.
                                                 Cách tốt nhất là x-model="formatted_price" và watch? 
                                                 Cách đơn giản nhất: Input text, @input update raw & formatted.
                                            --}}
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
                                            <input type="number" class="form-control form-control-sm text-center" 
                                                   :name="'variants['+index+'][stock]'" 
                                                   x-model="variant.stock" 
                                                   required min="0">
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-xs btn-danger" @click="removeNewVariant(index)">
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
                            <x-form.input name="meta_title" label="Meta Title" placeholder="Tiêu đề hiển thị Google..." />
                            <div class="form-group mt-3">
                                <label>Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" placeholder="Từ khóa, cách nhau dấu phẩy">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-admin.form.media-input name="meta_image" label="Meta Image" :multiple="false" />
                        </div>
                        <div class="col-12 mt-2">
                             <x-form.textarea name="meta_description" label="Meta Description" rows="3" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white text-right border-top sticky-bottom" style="z-index: 1000">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Hủy</a>
             <button type="submit" name="save_new" value="1" class="btn btn-success mr-2">
                <i class="fas fa-plus"></i> Lưu & Thêm mới
            </button>
            <button type="submit" name="save" value="1" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu hoàn tất
            </button>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    function productForm() {
        return {
            hasVariants: {{ old('has_variants') ? 'true' : 'false' }},
            formData: {
                name: '{{ old('name') }}'
            },
            selectedAttributes: [], // Chứa {id, name, allValues, selectedValues[]}
            variants: {!! json_encode(old('variants', [])) !!}, // Load old input nếu validate fail
            bulk: { sku: '', price: '', original_price: '', stock: '' },

            init() {
                // Khởi tạo Select2 cho thuộc tính
                var self = this;
                $('#attribute-select').select2({
                    theme: 'bootstrap4',
                    placeholder: "Chọn thuộc tính...",
                    allowClear: true
                }).on('change', function() {
                    self.handleAttributeSelection($(this).val());
                });

                // Nếu có old variants (do validate fail), thì variants đã được fill ở trên
                // Nếu chưa có variants nhưng có selectedAttributes (chưa implement old selected attr) -> generate
            },

            handleAttributeSelection(selectedIds) {
                // 1. Xác định các attr mới được chọn
                let options = $('#attribute-select option');
                
                // Lọc ra các attr chưa có trong list hiện tại
                // Hoặc remove các attr bị bỏ chọn
                
                // Cách đơn giản: Re-build selectedAttributes dựa trên selectedIds, 
                // nhưng cố gắng giữ lại selectedValues của những cái cũ để không mất công user chọn lại
                
                if (!selectedIds) selectedIds = [];

                // Xóa những cái không còn được chọn
                this.selectedAttributes = this.selectedAttributes.filter(attr => selectedIds.includes(attr.id.toString()));

                // Thêm những cái mới
                 selectedIds.forEach(id => {
                    let exists = this.selectedAttributes.find(a => a.id == id);
                    if (!exists) {
                        // Tìm option element để lấy data
                        let opt = options.filter(`[value="${id}"]`);
                        let name = opt.text();
                        let values = opt.data('values'); // JSON parsed automatically by jQuery data() if valid json? No, blade sends object
                        
                        this.selectedAttributes.push({
                            id: id,
                            name: name,
                            allValues: values,
                            selectedValues: [] // User sẽ check chọn sau
                        });
                    }
                });
            },

            removeAttribute(id) {
                // Xóa khỏi UI Alpine
                this.selectedAttributes = this.selectedAttributes.filter(a => a.id != id);
                // Sync lại Select2
                let currentVals = $('#attribute-select').val();
                let newVals = currentVals.filter(v => v != id);
                $('#attribute-select').val(newVals).trigger('change');
            },

            generateVariants() {
                // ... Logic Cartesian ...
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
                
                // Lấy Main SKU từ input
                let mainSku = $('input[name="code"]').val();
                
                // Nếu chưa có Main SKU, thử lấy từ Tên sản phẩm
                if (!mainSku) {
                    let productName = $('input[name="name"]').val();
                    if (productName) {
                        mainSku = this.slugify(productName).toUpperCase();
                        // Gán ngược lại vào ô Code để user thấy
                        $('input[name="code"]').val(mainSku);
                        // Trigger change để update label/placeholder nếu cần
                        $('input[name="code"]').trigger('change');
                        
                        toastr.info('Hệ thống tự động tạo Mã SKU từ Tên sản phẩm.');
                    } else {
                        mainSku = 'SKU'; // Fallback cuối cùng
                        toastr.warning('Vui lòng nhập Mã sản phẩm (SKU) để mã biến thể được chuẩn hơn.');
                    }
                }

                // 2. Map combos thành variants objects
                this.variants = combos.map(combo => {
                    let items = Array.isArray(combo) ? combo : [combo];
                    
                    let name = items.map(i => i.value).join(' - ');
                    let ids = items.map(i => i.id).join(',');
                    
                    // Auto-Gen SKU: MAINSKU-RED-XL
                    let skuSuffix = items.map(i => this.slugify(i.value)).join('-').toUpperCase();
                    let variantSku = `${mainSku}-${skuSuffix}`;

                    return {
                        variant_name: name,
                        attribute_value_ids: ids,
                        sku: variantSku,
                        price: ($('input[name="price"]').val() || '').toString().replace(/\D/g, '') || 0, 
                        compare_at_price: ($('input[name="price_discount"]').val() || '').toString().replace(/\D/g, '') || '',
                        stock: $('input[name="stock"]').val() || 100
                    };
                });
                
                // Switch sang tab variants nếu đang ở tab khác (optional)
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

            removeVariant(index) {
                this.variants.splice(index, 1);
            },

            applyBulk() {
                this.variants.forEach(v => {
                    if(this.bulk.sku) v.sku = this.bulk.sku + '-' + this.slugify(v.variant_name).toUpperCase();
                    if(this.bulk.price) v.price = this.bulk.price;
                    if(this.bulk.original_price) v.compare_at_price = this.bulk.original_price;
                    if(this.bulk.stock) v.stock = this.bulk.stock;
                });
            },
            
            slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')           // Replace spaces with -
                    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                    .replace(/^-+/, '')             // Trim - from start of text
                    .replace(/-+$/, '');            // Trim - from end of text
            },

            formatMoney(value) {
                if (!value) return '';
                // Ensure value is numeric string or number
                let num = value.toString().replace(/[^\d]/g, '');
                return Number(num).toLocaleString('vi-VN');
            },

            submitForm() {
                // Validate logic JS client side thêm nếu cần
                // Ví dụ: Bắt buộc variants phải có SKU
                if (this.hasVariants && this.variants.length === 0) {
                     Swal.fire('Lỗi', 'Bạn đã chọn sản phẩm có biến thể nhưng chưa tạo biến thể nào.', 'error');
                     return;
                }
                document.getElementById('product-form').submit();
            }
        }
    }
</script>
@endpush